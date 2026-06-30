<template>
	<lay-layout style="flex-direction:column;" class="cardLayout">
		<headerMenu></headerMenu>
		<lay-layout class="frontLayOut">
            <leftMenu v-if="!$route.meta.hideMenu"></leftMenu>
			<lay-body class="frontLayBody">
				<router-view v-slot="{ Component }">
					<keep-alive :include="aliveComponents">
						<component :is="Component"/>
					</keep-alive>
				</router-view>
			</lay-body>
		</lay-layout>
	</lay-layout>
</template>
<script setup>
import {useAuthStore} from "@/stores/auth.js";
import headerMenu from "@/components/desktop/menu/Header.vue";
import leftMenu from "@/components/desktop/menu/ExamLeft.vue";
import {useRouter} from "vue-router";
const user = useAuthStore();
const router = useRouter();
const aliveComponents = ['examHistory'];
if(!user.isLoggedIn)router.push('/desktop/home/auth/login');
const store = useAuthStore();
if(!store.basic)router.replace('/desktop/home/core/exam');
</script>
<style src="@/assets/css/desktop/card.css"></style>