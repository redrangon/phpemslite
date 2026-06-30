<template>
	<lay-space direction="vertical" fill wrap class="login_box">
		<h2 style="text-align: center;color:#FFFFFF;font-size:18px;">账号登录</h2>
		<lay-form :model="model" :pane="false" size="md" labelWidth="80" class="form" ref="loginForm">
			<lay-form-item class="formitem" prop="username" required error-message="请输入用户名">
				<lay-input prefix-icon="layui-icon-username" size="lg"  v-model="model.username" placeholder="请输入用户名" autocomplete="username"></lay-input>
			</lay-form-item>
			<lay-form-item class="formitem" prop="userpassword" required :rules="{type:'string',min:6,max:16}" error-message="密码必须为6-16个字符串">
				<lay-input prefix-icon="layui-icon-password" size="lg"  v-model="model.userpassword" type="password" password placeholder="请输入密码" autocomplete="userpassword"></lay-input>
			</lay-form-item>
			<lay-form-item required error-message="请输入验证码" prop="randcode">
				<lay-row space="5">
					<lay-col sm="14" xs="14">
						<lay-input prefix-icon="layui-icon-chat" size="lg" v-model="model.randcode" placeholder="请输入验证码" autocomplete="off"></lay-input>
					</lay-col>
					<lay-col sm="10" xs="10">
						<img style="width:120px;height:42px;" :src="randImage" @click="getRandCode" />
					</lay-col>
				</lay-row>
			</lay-form-item>
			<lay-form-item class="btns">
				<lay-space direction="vertical" fill wrap size="md">
					<lay-button type="normal" fluid @click="submit" :loading="loading">登录</lay-button>
					<lay-row v-if="settings.closeregist">
						<lay-col sm="24" xs="24">
							<router-link to="/desktop/home/auth/forget" style="color:#FFFFFF;float:right">忘记密码</router-link>
						</lay-col>
					</lay-row>
					<lay-row v-else>
						<lay-col sm="12" xs="12"><router-link to="/desktop/home/auth/register" style="color:#FFFFFF;">注册新账号</router-link></lay-col>
						<lay-col sm="12" xs="12" style="text-align: right;"><router-link to="/desktop/home/auth/forget" style="color:#FFFFFF;">忘记密码</router-link></lay-col>
					</lay-row>
				</lay-space>
			</lay-form-item>
		</lay-form>
	</lay-space>
</template>
<script setup>
import {layer} from '@layui/layui-vue';
import { useRouter } from 'vue-router';
import userApi from '@/framework/api/user.js';
import authApi from '@/framework/api/auth.js';
import utilsApi from '@/framework/api/utils.js';
import {ref,onMounted} from 'vue';
import {useAuthStore} from "@/stores/auth.js";

const authStore = useAuthStore();
const router = useRouter();
const model = ref({
	username: '',
	userpassword: '',
	randcode: '',
	randid:''
});
const loginForm = ref(null);
const settings = ref({
	closeregist: false
});
const loading = ref(false);
const randImage = ref('');
const submit = async function () {
	loginForm.value.validate().then(async (res) => {
		try{
			const authData = await authApi.login({
				username: model.value.username,
				password: model.value.userpassword,
				captcha: model.value.randcode,
				captchaId: model.value.randid
			});
			layer.msg('登录成功');
			localStorage.setItem('token', authData.token);
			await authStore.getCurrentUser();
			await router.push('/desktop/home');
		} catch (e) {
			layer.msg(e.error??'登录失败');
			await getRandCode();
		}finally {
			loading.value = false;
		}
	}).catch(res => {
		//
	});
};
const getRandCode = async () => {
	try{
		const res = await utilsApi.getRandCode();
		randImage.value = res.image;
		model.value.randid = res.id;
	}
	catch (e) {
		layer.msg(e.msg??'获取验证码失败');
	}
};
onMounted(async() => {
	layer.closeAll();
	settings.value = await userApi.getRegisterSetting();
	await getRandCode();
})
</script>
<style scoped>
@import "@/assets/css/desktop/auth.css";
.textinfo{
	padding:10px 0px;
	background-color: rgba(0,0,0,0.3);
	color:#FFFFFF;
	text-align: center;
}
</style>