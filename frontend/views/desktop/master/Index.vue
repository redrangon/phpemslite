<script setup>
</script>
<template>
	<router-view v-slot="{ Component, route }">
		<keep-alive v-if="route.meta.keepAlive">
			<component :is="Component" :key="route.fullPath"/>
		</keep-alive>
		<component :is="Component" v-else/>
	</router-view>
</template>
<style>
@import "@/assets/css/desktop/master.css";
</style>
