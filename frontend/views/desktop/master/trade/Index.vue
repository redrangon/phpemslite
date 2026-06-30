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
						<component :is="Component" :key="route.meta.name"/>
					</keep-alive>
					<component :is="Component" v-else/>
				</router-view>
			</lay-body>
		</lay-layout>
	</lay-layout>
</template>
<script setup>
import {ref, provide, inject} from "vue";
import leftMenu from '@/components/master/menu/Left.vue';
import topMenu from '@/components/master/menu/Top.vue';
import breadCrumb from "@/components/master/menu/Bread.vue";
const module = ref("module")
const menu = ref("1")
provide("module",module);
provide("menu",menu);
</script>