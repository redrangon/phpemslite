<!-- src/App.vue -->
<template>
	<router-view v-slot="{ Component, route }">
		<transition :name="transitionName">
            <component :is="Component" :key="route.path"/>
		</transition>
	</router-view>
</template>

<script setup>
import { usePageTransition } from '@/framework/utils/mobile/usePageTransition.js'
const { transitionName } = usePageTransition()
</script>

<style>
#app{
    width: 100%;
    overflow: hidden;
}
/* ========== 前进动画：slide-left ========== */
.slide-left-enter-active,
.slide-left-leave-active {
	transition: transform 0.3s ease;
}
.slide-left-enter-from {
	transform: translateX(100%);
}
.slide-left-enter-to {
	transform: translateX(0);
}
.slide-left-leave-to {
	transform: translateX(-100%);
}

/* ========== 返回动画：slide-right ========== */
.slide-right-enter-active,
.slide-right-leave-active {
	transition: transform 0.3s ease;
}
.slide-right-enter-from {
	transform: translateX(-100%);
}
.slide-right-enter-to {
	transform: translateX(0);
}
.slide-right-leave-to {
	transform: translateX(100%);
}
</style>