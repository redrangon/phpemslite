<template>
	<lay-layout style="flex-direction:column;" class="cardLayout">
		<headerMenu></headerMenu>
		<lay-layout class="frontLayOut">
			<router-view></router-view>
		</lay-layout>
	</lay-layout>
</template>
<script setup>
import {useAuthStore} from "@/stores/auth.js";
import headerMenu from "@/components/desktop/menu/Header.vue";
import leftMenu from "@/components/desktop/menu/Left.vue";
import {useRouter} from "vue-router";
const user = useAuthStore();
const router = useRouter();
if(!user.isLoggedIn)router.push('/desktop/home/auth/login');
</script>
<style src="@/assets/css/desktop/card.css"></style>