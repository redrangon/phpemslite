// router/index.ts
import {createRouter,createWebHashHistory} from 'vue-router';
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