// frontend/framework/api/Service.js
import axios from 'axios';
import router from '@/router/index.js';
import {decrypt, encrypt} from '@/framework/security/ajax.js';
import Config from '@/config';
import { useAuthStore } from '@/stores/auth';

const Service = axios.create({
	baseURL: Config.url,
	timeout: 180000,
	withCredentials: true
});

// token刷新锁
let isRefreshing = false;
// 等待队列
let pendingRequests = [];

// 处理等待队列
const processPendingRequests = (token, error) => {
	pendingRequests.forEach(({ config, resolve, reject }) => {
		if (error) {
			reject(error);
		} else {
			// 更新请求头的token
			config.headers['X-Auth-Token'] = token;
			// 重新发送请求
			resolve(Service(config));
		}
	});
	pendingRequests = [];
};

// 获取最新的authStore实例
const getAuthStore = () => useAuthStore();

// 请求拦截器
Service.interceptors.request.use(async (config) => {
	const authStore = getAuthStore();
	const token = authStore.token;
	// 设置 token
	config.headers['X-Auth-Token'] = token || '';

	// 文件上传：不加密
	if (config.data instanceof FormData) {
		config.headers['Content-Type'] = 'multipart/form-data';
		return config;
	}
	// 普通请求：加密
	if (config.data) {
		try {
			config.data = encrypt(config.data); // 包装为 { data: "base64..." }
		} catch (error) {
			return Promise.reject(error);
		}
	}
	return config;
}, (error) => {
	return Promise.reject(error);
});

// 响应拦截器
Service.interceptors.response.use(async (response) => {
	const authStore = getAuthStore();

	// 文件下载：直接处理
	if (response.data instanceof Blob) {
		const contentType = response.headers['content-type'] || '';
		if (contentType.includes('application/json')) {
			response.data = JSON.parse(await response.data.text());
		} else {
			return response.data;
		}
	}

	const resData = response.data;

	// 处理token旋转
	if (resData?.token) {
		// 如果正在刷新token，将当前请求加入等待队列
		if (isRefreshing) {
			return new Promise((resolve, reject) => {
				pendingRequests.push({
					config: response.config,
					resolve,
					reject
				});
			});
		}

		// 开始刷新token
		isRefreshing = true;

		try {
			// 更新token
			authStore.updateToken(resData.token);

			// 处理所有等待的请求
			processPendingRequests(resData.token);

			// 重置刷新状态
			isRefreshing = false;
		} catch (error) {
			// 如果更新失败，reject所有等待的请求
			processPendingRequests('', error);
			isRefreshing = false;
			return Promise.reject(error);
		}
	}

	// 处理加密数据
	if (resData?.keyId && resData?.data) {
		try {
			resData.data = decrypt(resData.data, resData.keyId);
		} catch (error) {
			return Promise.reject(error);
		}
	}

	return resData;
}, (error) => {
	return Promise.reject(error);
});

export default Service;