// src/composables/usePageTransition.js
import { useRoute, useRouter } from 'vue-router';
import { useRouterStore } from '@/stores/router';
import {watch,computed} from "vue";

export function usePageTransition() {
    const route = useRoute();
    const router = useRouter();
    const routerStore = useRouterStore();

    // 内部标记，用于区分是用户点击还是浏览器前进/后退
    let isProgrammaticNav = false;

    // 拦截 router.push 和 router.replace
    const originalPush = router.push;
    const originalReplace = router.replace;

    router.push = (...args) => {
        isProgrammaticNav = true;
        return originalPush.apply(router, args);
    };

    router.replace = (...args) => {
        isProgrammaticNav = true;
        return originalReplace.apply(router, args);
    };

    // 监听路由变化
    watch(
        () => route.fullPath,
        (newPath, oldPath) => {
            // 【关键】首次加载时不执行动画，避免页面刚出来就闪一下
            if (!oldPath) return;

            const currentLevel = route.meta?.level ?? 0;
            const isProgrammatic = isProgrammaticNav;
            isProgrammaticNav = false; // 重置标记

            if (isProgrammatic) {
                // 主动跳转：根据 level 判断方向
                routerStore.transitionName = routerStore.prevRouteLevel < currentLevel
                    ? 'slide-left'
                    : 'slide-right';
            } else {
                // 非主动跳转（浏览器返回/前进）
                routerStore.transitionName = 'slide-right';
            }

            // 更新层级
            routerStore.prevRouteLevel = currentLevel;
        }
    );

    // 返回 Store 中的 transitionName，供模板直接使用
    return {
        transitionName: computed(() => routerStore.transitionName)
    };
}