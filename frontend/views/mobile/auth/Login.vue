<template>
	<div class="login-content">
		<!-- 品牌标识区域 -->
		<div class="brand-header">
			<h1 class="brand-title">PHPEMS</h1>
			<p class="brand-subtitle">开启您的终身学习之旅</p>
		</div>

		<!-- 登录表单卡片 -->
		<div class="card-container" style="padding:35px 25px;">
			<van-form @submit="onSubmit" class="login-form">
				<!-- 用户名输入框 -->
				<div class="input-group">
					<van-icon name="user-o" class="input-icon" />
					<van-field
						v-model="model.username"
						placeholder="请输入用户名/手机号"
						:rules="[{ required: true, message: '请填写用户名' }]"
						required
					/>
				</div>

				<!-- 密码输入框 -->
				<div class="input-group">
					<van-icon name="lock" class="input-icon" />
					<van-field
						v-model="model.userpassword"
						type="password"
						placeholder="请输入密码"
						:rules="[{ required: true, message: '请填写密码' }]"
						required
					/>
				</div>

				<!-- 验证码输入框 -->
				<div class="input-group captcha-group">
					<van-icon name="shield-o" class="input-icon" />
					<div class="captcha-input-wrapper">
						<van-field
							v-model="model.randcode"
							placeholder="验证码"
							:maxlength="4"
							:rules="[{ required: true, message: '请填写验证码' }]"
							required
						/>
					</div>
					<div class="captcha-image" @click="getRandCode">
						<van-image :src="randImage" />
					</div>
				</div>

				<!-- 登录按钮 -->
				<van-button
					block
					native-type="submit"
					:loading="loading"
					class="login-button"
				>
					登 录
				</van-button>

				<!-- 链接区域 -->
				<div class="links" v-if="!settings.closeregist">
					<router-link to="/mobile/auth/register">
						注册新账号
					</router-link>
					<router-link to="/mobile/auth/forget">
						忘记密码?
					</router-link>
				</div>
				<div class="links" style="justify-content: center" v-else>
					<router-link to="/mobile/auth/forget">
						忘记密码?
					</router-link>
				</div>
			</van-form>
			<van-divider :style="{color: '#6663cd', borderColor: '#6663cd', padding: '16px 0'}">其他方式登录</van-divider>
			<div style="text-align: center;">
				<van-space :size="20">
					<van-icon name="wechat" color="#1989fa" size="40" @click="getOpenid"/>
				</van-space>
			</div>
		</div>
	</div>
</template>
<style scoped>
</style>
<script setup>
import { ref, onMounted } from 'vue';
import {useAuthStore} from "@/stores/auth.js";
import api from '@/framework/api/index.js';
import wechat from '@/framework/api/wechat.js';
import {useRouter} from "vue-router";
import {showFailToast,showSuccessToast } from 'vant'
import userApi from "@/framework/api/user.js";

const authStore = useAuthStore();
const router = useRouter();
const model = ref({
	username: '',
	userpassword: '',
	randcode: '',
	randid:''
});
const settings = ref({
	closeregist: false
});
const loading = ref(false);
const randImage = ref('');

const onSubmit = async function () {
	try{
		const authData = await api.authApi.login({
			username: model.value.username,
			password: model.value.userpassword,
			captcha: model.value.randcode,
			captchaId: model.value.randid
		});
        authStore.updateToken(authData.token);
		await authStore.getCurrentUser();
		showSuccessToast('登录成功');
		await router.push('/mobile/core');
	} catch (e) {
		showFailToast(e.msg??'登录失败');
	}finally {
		loading.value = false;
		await getRandCode();
	}
};

const getRandCode = async () => {
	try{
		const res = await api.utilsApi.getRandCode();
		randImage.value = res.image;
		model.value.randid = res.id;
	}
	catch (e) {
		showFailToast(e.msg??'获取验证码失败');
	}
};
const getOpenid = async () => {
};
onMounted(async() => {
	await getRandCode();
	settings.value = await userApi.getRegisterSetting();
})
</script>