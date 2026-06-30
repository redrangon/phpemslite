<template>
	<lay-card>
		<lay-page-header content="详情页面" backText="" @back="$router.go(-1)">
			<lay-breadcrumb>
				<lay-breadcrumb-item :title="menu.title" v-for="(menu,mid) in menus" :key="mid" @click="$router.push({path:menu.path?menu.path:''})"></lay-breadcrumb-item>
			</lay-breadcrumb>
		</lay-page-header>
	</lay-card>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
const route = useRoute();

const menus = computed(() => {
	// 从匹配的路由中，找到第一个有 breadcrumb 的（通常是最深的那个）
	const matched = route.matched
	let breadcrumbConfig = null

	// 从后往前找（优先使用子路由的 breadcrumb）
	for (let i = matched.length - 1; i >= 0; i--) {
		if (matched[i].meta && typeof matched[i].meta.breadcrumb !== 'undefined') {
			breadcrumbConfig = matched[i].meta.breadcrumb
			break
		}
	}

	if (!breadcrumbConfig) {
		return []
	}

	// 如果是函数，调用它并传入 route
	if (typeof breadcrumbConfig === 'function') {
		return breadcrumbConfig(route)
	}
	console.log('sss');

	// 如果是静态数组（可选支持）
	return Array.isArray(breadcrumbConfig) ? breadcrumbConfig : []
})

</script>
<style scoped>
</style>