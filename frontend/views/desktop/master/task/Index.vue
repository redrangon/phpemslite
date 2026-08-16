<template>
	<lay-layout class="masterLayOut">
		<leftMenu></leftMenu>
		<lay-layout>
			<lay-header class="masterHeader">
				<topMenu></topMenu>
			</lay-header>
			<lay-body>
				<bread-crumb></bread-crumb>
				<router-view v-slot="{ Component, route }">
					<keep-alive v-if="route.meta.keepAlive">
						<component :is="Component" :key="route.fullPath"/>
					</keep-alive>
					<component :is="Component" v-else/>
				</router-view>
			</lay-body>
		</lay-layout>
	</lay-layout>
</template>
<script setup>
import {ref, provide} from "vue";
import leftMenu from '@/views/components/master/menu/Left.vue';
import topMenu from '@/views/components/master/menu/Top.vue';
import breadCrumb from "@/views/components/master/menu/Bread.vue";
const module = ref("task")
const menu = ref("11")
provide("module",module);
provide("menu",menu);
</script>
