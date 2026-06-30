<template>
    <lay-header>
        <lay-row class="topheader">
            <lay-col md="8">
                <div class="logo">新乡市落笔千言网络技术有限公司</div>
            </lay-col>
            <lay-col md="16" style="text-align:right">
                <lay-menu>
                    <router-link to="/desktop/home/core">
                        <lay-menu-item><lay-icon type="layui-icon-user"></lay-icon> 我的</lay-menu-item>
                    </router-link>
                    <router-link to="/desktop/home/content">
                        <lay-menu-item><lay-icon type="layui-icon-tree"></lay-icon> 新闻</lay-menu-item>
                    </router-link>
                    <router-link to="/desktop/home/course">
                        <lay-menu-item><lay-icon type="layui-icon-read"></lay-icon> 课程</lay-menu-item>
                    </router-link>
                    <router-link to="/desktop/home/exam">
                        <lay-menu-item><lay-icon type="layui-icon-form"></lay-icon> 考试</lay-menu-item>
                    </router-link>
                    <router-link to="/desktop/home/cert">
                        <lay-menu-item><lay-icon type="layui-icon-diamond"></lay-icon> 证书</lay-menu-item>
                    </router-link>
                    <lay-menu-item @click="logout"><lay-icon type="layui-icon-logout"></lay-icon> 退出</lay-menu-item>
                </lay-menu>
            </lay-col>
        </lay-row>
    </lay-header>
</template>
<script setup>
	import {ref} from 'vue';
	import {layer} from '@layui/layui-vue'
	import api from "@/framework/api/index.js"
	import { useRouter } from "vue-router";
	import {useAuthStore} from '@/stores/auth.js';
	const authStore = useAuthStore();

	const selectedKey = ref("1");
	const openKeys = ref();
	const router = useRouter();
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
</script>
<style scoped>
.layui-header{
    background:#117bcb;
    height: 72px;
	min-width: 1200px;	
}
.topheader{
	width: 100%;
	max-width: 1200px;
    margin: auto;
}
.logo{
	line-height: 72px;
	font-size: 24px;
	color: #bde4ff;
	text-indent: 20px;
}
.layui-nav{
	margin-top: 6px;
}
.layui-header .layui-nav{
    background: transparent;
}
</style>