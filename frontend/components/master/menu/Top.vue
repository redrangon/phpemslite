<template>
	<lay-menu :tree="false" :selected-key="selectedKey" :open-keys="openKeys" :indent="true" class="myTopMenu" @changeSelectedKey="changeSelectedKey">
		<router-link to="/desktop/master/dashboard">
			<lay-menu-item id="core" to="/xxxx">
				<template #title>
					<lay-icon type="layui-icon-home"></lay-icon>
					首页
				</template>
			</lay-menu-item>
		</router-link>
		<router-link to="/desktop/master/course">
			<lay-menu-item id="course"><lay-icon type="layui-icon-video"></lay-icon> 课程</lay-menu-item>
		</router-link>
		<router-link to="/desktop/master/exam">
			<lay-menu-item id="exam"><lay-icon type="layui-icon-survey"></lay-icon> 考试</lay-menu-item>
		</router-link>
		<router-link to="/desktop/master/content">
			<lay-menu-item id="content"><lay-icon type="layui-icon-list"></lay-icon> 内容</lay-menu-item>
		</router-link>
		<router-link to="/desktop/master/member">
			<lay-menu-item id="member"><lay-icon type="layui-icon-form"></lay-icon> 档案</lay-menu-item>
		</router-link>
        <router-link to="/desktop/master/cert">
            <lay-menu-item id="cert"><lay-icon type="layui-icon-date"></lay-icon> 证书</lay-menu-item>
        </router-link>
        <router-link to="/desktop/master/trade">
			<lay-menu-item id="trade"><lay-icon type="layui-icon-rmb"></lay-icon> 交易</lay-menu-item>
		</router-link>
		<!--
		<router-link to="/desktop/master/attach">
			<lay-menu-item id="attach"><lay-icon type="layui-icon-gift"></lay-icon> 附件</lay-menu-item>
		</router-link>
		-->
		<router-link to="/desktop/master/user">
			<lay-menu-item id="user"><lay-icon type="layui-icon-username"></lay-icon> 用户</lay-menu-item>
		</router-link>
		<span style="float:right;" id="99">
			<lay-space :size="30">
				<span style="cursor:pointer">
					<lay-icon type="layui-icon-username"></lay-icon> {{user.username}}
				</span>
				<span @click="toHome" style="cursor:pointer">
					<lay-icon type="layui-icon-home"></lay-icon> 首页
				</span>
				<span @click="logout" v-if="user.userid > 0" style="cursor:pointer">
					<lay-icon type="layui-icon-logout"></lay-icon> 退出
				</span>
			</lay-space>
		</span>
	</lay-menu>
</template>
<script setup>
import {ref, onMounted, inject, watch} from 'vue';
import {layer} from '@layui/layui-vue'
import api from "@/framework/api/index.js"
import { useRouter,useRoute } from "vue-router";
import {useAuthStore} from '@/stores/auth.js';
const authStore = useAuthStore();
const user = authStore.user;
const selectedKey = inject('module');
const openKeys = ref();
const router = useRouter();
const route = useRoute();
const refresh = function(){
	window.location.reload();
}
const toHome = function(){
	router.push('/desktop/home/');
}
const changeSelectedKey = function(val){
	selectedKey.value = val;
}
const logout = function(){
	layer.confirm("您确定要退出吗？", {
		title:'退出确认',
		btn: [
			{
				text:'确定',
				callback: async (id) => {
					await api.authApi.logout();
					authStore.clearUser();
					layer.closeAll();
					await router.push('/desktop/home/auth/login');
				}
			},
			{
				text:'取消',
				callback: (id) => {
					layer.close(id);
				}
			}
		]
	});
}
watch(
	() => route.meta,
	(meta) => {
		if (meta?.module !== undefined) {
			selectedKey.value = meta.module
		} else {
			selectedKey.value = "core"
		}
	},
	{ immediate: true }
)
onMounted(() => {
	selectedKey.value = route.meta.module;
	if(route.meta?.openmenu)openKeys.value = route.meta.openmenu;
});
</script>
<style scoped>
.myTopMenu{
	border-radius: 0px;
	background-color: #02756E;
}
.myTopMenu{
	padding-left: 0px;
}
.myTopMenu >.layui-nav-item{
	border-radius: 0px;
}
.layui-this{
	background: #16A98C;
}
</style>