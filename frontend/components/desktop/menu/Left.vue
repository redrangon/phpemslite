<template>
	<lay-side class="mySide">		
		<div class="mylogo">
			<lay-avatar :src="user?user.userphoto:''" style="width:120px;height:168px;margin-top: 10px;" :autoFixSize="true"></lay-avatar>
			<h1 class="title">{{user?user.username:''}}</h1>
		</div>
		<lay-menu :tree="true" class="myMenu" :selected-key="selectedKey" theme="light" @changeSelectedKey="changeSelectedKey">
			<router-link to="/desktop/home/core/home">
				<lay-menu-item id="1">
					<lay-icon type="layui-icon-home"></lay-icon> 个人中心
				</lay-menu-item>
			</router-link>
			<router-link to="/desktop/home/core/course">
				<lay-menu-item id="2">
					<lay-icon type="layui-icon-read"></lay-icon> 我的课程
				</lay-menu-item>
			</router-link>
			<router-link to="/desktop/home/core/exam">
				<lay-menu-item id="3">
					<lay-icon type="layui-icon-form"></lay-icon> 我的考试
				</lay-menu-item>
			</router-link>
			<router-link to="/desktop/home/core/cert">
				<lay-menu-item id="4">
					<lay-icon type="layui-icon-diamond"></lay-icon> 我的证书
				</lay-menu-item>
			</router-link>
			<router-link to="/desktop/home/core/expense">
				<lay-menu-item id="5">
					<lay-icon type="layui-icon-rmb"></lay-icon> 消费记录
				</lay-menu-item>
			</router-link>
			<router-link to="/desktop/home/core/profile">
				<lay-menu-item id="6">
					<lay-icon type="layui-icon-user"></lay-icon> 个人信息
				</lay-menu-item>
			</router-link>
			<router-link to="/desktop/master" v-if="isMaster">
				<lay-menu-item id="7">
					<lay-icon type="layui-icon-auz"></lay-icon> 后台管理
				</lay-menu-item>
			</router-link>
		</lay-menu>				
	</lay-side>
</template>
<script setup>
	import {ref,onMounted,computed} from 'vue';
    import { useAuthStore} from '@/stores/auth.js';
	import { useRouter,useRoute } from 'vue-router';
	const router = useRouter();
	const route = useRoute();
	const authStore = useAuthStore();
	const selectedKey = ref("1");

	const changeSelectedKey = function(val){
		selectedKey.value = val;
	}
	const user = computed(() => authStore.userInfo);
	const isMaster = authStore.isMaster;
	onMounted(async function(){
		if(route?.meta?.lm)selectedKey.value = route.meta.lm.toString();
	})
</script>
<style src="@/assets/css/desktop/left.css"></style>