// src/stores/router.js
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useRouterStore = defineStore('router', () => {
    // 1. 定义状态（等同于 Options 里的 state）
    const transitionName = ref('');
    const prevRouteLevel = ref(0);
    const pageData = ref({}); // 初始化为空对象

    // 2. 定义更新函数（等同于 Options 里的 actions）
    // 将更新逻辑封装在这里，组件调用时就不需要关心内部是如何赋值的
    function updatePageData(pageName, data) {
        pageData.value[pageName] = {
            ...pageData.value[pageName], // 保留该页面原有的其他数据
            ...data                      // 覆盖或新增传入的数据
        };
    }

    // 3. 定义获取函数（等同于 Options 里的 getters）
    function getPageData(pageName) {
        return pageData.value[pageName] || {};
    }

    // 4. 【关键】必须返回所有需要暴露的状态和方法
    return {
        transitionName,
        prevRouteLevel,
        pageData,
        updatePageData,
        getPageData
    };
}, {
    persist: true // 👈 加上这一行，开启持久化
});