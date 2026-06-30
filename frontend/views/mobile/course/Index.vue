<template>
	<div class="page">
		<div class="page-container">
			<div class="bg-decoration">
				<div class="bg-circle bg-circle-1"></div>
				<div class="bg-circle bg-circle-2"></div>
				<div class="bg-pattern bg-pattern-1">
					<van-icon name="books" />
				</div>
				<div class="bg-pattern bg-pattern-2">
					<van-icon name="graduation-cap" />
				</div>
				<div class="bg-pattern bg-pattern-3">
					<van-icon name="star" />
				</div>
			</div>
			<router-view v-slot="{ Component, route }">
				<keep-alive v-if="route.meta.keepAlive">
					<component :is="Component" :key="route.fullPath"/>
				</keep-alive>
				<component :is="Component" v-else/>
			</router-view>
		</div>
	</div>
</template>
<script setup>
import {useAuthStore} from "@/stores/auth.js";
import {useRouter} from "vue-router";
const user = useAuthStore();
const router = useRouter();
if(!user.isLoggedIn)router.push('/mobile/auth/login');
</script>