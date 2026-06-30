// src/framework/http/request.js
import axios from '@/framework/http/axios.js';
import router from '@/router';
import {isMobile} from "@/framework/utils/device.js";

/**
 * 统一处理响应数据
 * @param {Object} response - Axios 响应对象
 * @returns {*} 业务数据（当 code === 200）
 * @throws {Error} 业务错误或网络错误
 */
const handleResponse = (response) => {
    if(response instanceof Blob)
    {
        return response;
    }
    const { code, data, msg } = response || {};
    if (code === 200) {
        return data;
    }
    const displayMsg = import.meta.env.PROD
        ? '请求失败，请稍后重试' : (response.error || '请求失败');
    const error = new Error(displayMsg);
    error.code = code;
    if (import.meta.env.DEV) {
        error.msg = msg;
    }
    if(error.code === 301 || error.code === 311)
    {
        if(error.code === 311)
        {
            router.push('/');
            return ;
        }
        if(isMobile())
        {
            router.push('/mobile/auth/login');
        }
        else
        {
            router.push('/desktop/home/auth/login');
        }
        return ;
    }
    throw error;
};

/**
 * 统一错误包装器
 * @param {Error} error - 原始错误
 * @returns {Error} 标准化错误对象
 */
const wrapError = (error) => {
    // 如果已经是业务错误（有 code），直接抛出
    if (error.code) {

    }

    const displayMsg = import.meta.env.PROD
        ? '网络异常，请检查网络后重试'
        : (error.message || '网络错误');

    const networkError = new Error(displayMsg);
    networkError.code = error.response?.status || 500;

    // 开发环境保留原始信息
    if (import.meta.env.DEV) {
        networkError.msg =
            error.response?.data?.msg ||
            error.response?.data?.message ||
            error.message ||
            '未知错误';
    }
    throw networkError;
};

/**
 * 通用请求方法
 * @param {string} method - HTTP 方法 ('get', 'post', 'put', 'delete'...)
 * @param {string} url
 * @param {any} [dataOrParams] - GET 用作 params，其他方法用作 data
 * @param {Object} [options] - Axios 配置
 * @returns {Promise<any>}
 */
const request = async (method, url, dataOrParams = null, options = {}) => {
    const config = {
        withCredentials: true,
        ...options
    };

    try {
        let response;

        if (method.toLowerCase() === 'get') {
            // GET 请求：dataOrParams 作为查询参数
            response = await axios.get(url, { ...config, params: dataOrParams });
        } else {
            // POST/PUT/DELETE 等：dataOrParams 作为请求体
            response = await axios.post(url, dataOrParams, config);
        }
        return handleResponse(response);
    } catch (error) {
        wrapError(error);
    }
};
// 导出便捷方法（保持你原有的 API 不变）
export const get = (url, options = {}) => request('get', url, null, options);
export const post = (url, data = null, options = {}) => request('post', url, data, options);
// 或者导出 request 对象（更灵活）
export default {get,post};