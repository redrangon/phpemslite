
/**
 * 创建API代理实例
 * @param {Object} modules - API模块配置
 * @returns {Object}
 * 使用代理IDE无法提示
 */
function createApiProxy(modules) {
    const moduleCache = new Map();

    // 创建主代理，用于获取模块
    return new Proxy({}, {
        get(_, moduleName) {
            // 检查模块是否存在
            if (!(moduleName in modules)) {
                throw new Error(`API module "${String(moduleName)}" not found`);
            }

            // 返回模块代理，用于获取具体方法
            return new Proxy({}, {
                get(target, methodName) {
                    return async (...args) => {
                        try {
                            // 检查模块是否已缓存
                            if (!moduleCache.has(moduleName)) {
                                // 加载模块并缓存
                                const loadPromise = modules[moduleName]() // 使用传入的modules参数
                                    .then(m => {
                                        if (!m || typeof m.default !== 'object') {
                                            throw new Error(`Module "${moduleName}" does not export a default object`);
                                        }
                                        return m.default;
                                    })
                                    .catch(error => {
                                        console.error(`Failed to load module "${moduleName}":`, error);
                                        throw error;
                                    });

                                moduleCache.set(moduleName, loadPromise);
                            }

                            // 获取缓存的模块
                            const module = await moduleCache.get(moduleName);

                            // 验证方法是否存在
                            if (typeof module[methodName] !== 'function') {
                                throw new Error(`Method "${String(methodName)}" not found in module "${moduleName}"`);
                            }

                            return module[methodName](...args);
                        } catch (error) {
                            console.error(`Error calling ${moduleName}.${methodName}:`, error);
                            throw error;
                        }
                    };
                }
            });
        }
    });
}

const api = createApiProxy({
    authApi: () => import('@/framework/api/auth.js'),
    userApi: () => import('@/framework/api/user.js'),
    examApi: () => import('@/framework/api/exam.js'),
    cardApi: () => import('@/framework/api/card.js'),
    utilsApi: () => import('@/framework/api/utils.js'),
});

export default api;
