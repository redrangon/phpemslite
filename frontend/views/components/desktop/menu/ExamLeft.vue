<template>
	<lay-side class="mySide">
		<div class="mylogo">
			<lay-avatar :src="user?user.userphoto:''" style="width:120px;height:168px;" :autoFixSize="true"></lay-avatar>
			<h1 class="title">{{user?user.username:''}}</h1>
		</div>
		<lay-menu :tree="true" class="myMenu" :selected-key="selectedKey" theme="light" @changeSelectedKey="changeSelectedKey">
			<template v-if="basic.basicexam?.model === 2">
				<router-link to="/desktop/home/exam/exam">
					<lay-menu-item id="1"><lay-icon type="layui-icon-edit"></lay-icon> 正式考试</lay-menu-item>
				</router-link>
				<router-link to="/desktop/home/exam/examhistory">
					<lay-menu-item id="2"><lay-icon type="layui-icon-form"></lay-icon> 考试记录</lay-menu-item>
				</router-link>
				<router-link to="/desktop/home/core/exam/">
					<lay-menu-item id="4"><lay-icon type="layui-icon-return"></lay-icon> 返回列表</lay-menu-item>
				</router-link>
			</template>
			<template v-else>
				<router-link to="/desktop/home/exam/exercise">
					<lay-menu-item id="1"><lay-icon type="layui-icon-edit"></lay-icon> 章节练习</lay-menu-item>
				</router-link>
				<router-link to="/desktop/home/exam/exampaper">
					<lay-menu-item id="2"><lay-icon type="layui-icon-log"></lay-icon> 模拟考试</lay-menu-item>
				</router-link>
				<router-link to="/desktop/home/exam/history">
					<lay-menu-item id="3"><lay-icon type="layui-icon-form"></lay-icon> 考试记录</lay-menu-item>
				</router-link>
				<router-link to="/desktop/home/exam/favor">
					<lay-menu-item id="4"><lay-icon type="layui-icon-star"></lay-icon> 习题收藏</lay-menu-item>
				</router-link>
				<router-link to="/desktop/home/exam/question" v-if="basic.basicexam?.model === 0">
					<lay-menu-item id="5"><lay-icon type="layui-icon-survey"></lay-icon> 全部试题</lay-menu-item>
				</router-link>
				<router-link to="/desktop/home/core/exam/">
					<lay-menu-item id="6"><lay-icon type="layui-icon-return"></lay-icon> 返回列表</lay-menu-item>
				</router-link>
			</template>
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
const basic = ref({});
basic.value = authStore.basic;
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