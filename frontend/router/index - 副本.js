// router/index.ts
import {createRouter,createWebHashHistory} from 'vue-router';
import { ref } from 'vue'
import { getDeviceType } from '@/framework/utils/device.js'
import homeRoutes from './home.js'
import masterRoutes from './master.js'
import mobileRoutes from "@/router/mobile.js";
const routes = [
	{
		path: '/',
		component: () => import('../views/Index.vue'),
		meta: {requiresAuth: false} // 可选，明确声明
	},
	{
		path: '/desktop',
		component: () => import('../views/desktop/home/Index.vue'),
		meta: {requiresAuth: false},
		children: [
			{
				path: 'home',
				meta: {layout: 'desktop'},
				children: homeRoutes
			},
			{
				path: 'master',
				component: () => import('../views/desktop/master/Index.vue'),
				meta: {layout: 'master'},
				children: masterRoutes
			},
		]
	},
	{
		path: '/mobile',
		component: () => import('../views/mobile/Index.vue'),
		meta: {layout: 'mobile'},
		children: mobileRoutes
	},
	{
		path: '/:catchAll(.*)',
		name: 'NotFound',
		component: () => import('../views/NotFound.vue')
	}
];

const router = createRouter({
	history: createWebHashHistory(),
	routes
});
router.transitionName = ref('slide-left')
router.beforeEach((to, from, next) => {
	// 1. 如果访问的是根路径 `/`，根据设备类型重定向
	const currentDevice = getDeviceType()
	if (to.path === '/') {
		if (currentDevice === 'mobile') {
			next('/mobile')
		} else {
			next('/desktop/home/core')
		}
		return
	}
	const fromLevel = from.meta?.level ?? 0
	const toLevel = to.meta?.level ?? 0

	// 兜底：获取浏览器原生的历史堆栈位置（防止用户点击浏览器自带的前进/后退按钮）
	const fromPosition = from.meta?.savedPosition || 0
	const toPosition = history.state?.position || 0
	to.meta.savedPosition = toPosition

	if (toLevel !== fromLevel) {
		// 优先：如果配置了严格的 level 级别，根据级别判断
		router.transitionName.value = toLevel > fromLevel ? 'slide-left' : 'slide-right'
	} else {
		// 备用：如果同级页面互相跳转，通过 history.state 计数器判断前进后退
		router.transitionName.value = toPosition > fromPosition ? 'slide-left' : 'slide-right'
	}

	// 2. 【可选】防止 PC 用户误入 mobile 路由（或反之）
	/**
	const isMobileRoute = to.path.startsWith('/mobile')
	const isPcRoute = to.path.startsWith('/desktop/home/core')

	if (isMobileRoute && currentDevice === 'desktop') {
		// PC 用户访问了 mobile 路由 → 跳转到 PC 首页
		next('/desktop')
		return
	}

	if (isPcRoute && currentDevice === 'mobile') {
		// Mobile 用户访问了 PC 路由 → 跳转到 Mobile 首页
		next('/mobile')
		return
	}
		**/

	// 3. 正常放行
	next()
})

export default router;