// frontend/framework/api/Service.ts
import axios from 'axios';
import router from '@/router/index.js';
import {decrypt, encrypt} from '@/framework/security/ajax.js';
import Config from '@/config';

const Service = axios.create({
	baseURL: Config.url,
	timeout: 180000,
	withCredentials: true
});

// 请求拦截器
Service.interceptors.request.use(async (config) => {
	// 设置 token
	config.headers['X-Auth-Token'] = localStorage.getItem('token') || '';

	// 文件上传：不加密
	if (config.data instanceof FormData) {
		config.headers['Content-Type'] = 'multipart/form-data';
		return config;
	}
	// 普通请求：加密
	if (config.data) {
		try {
			config.data = await encrypt(config.data); // 包装为 { data: "base64..." }
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
	// 文件下载：直接返回 Blob
	if (response.data instanceof Blob) {
		return response.data;
	}

	let resData = response.data;

	if(resData.keyId)
	{
		resData.data = decrypt(resData.data,resData.keyId);
	}

	if (resData?.token) {
		localStorage.setItem('token', resData.token);
	}
	return resData;
}, (error) => {
	return Promise.reject(error);
});

export default Service;