<template>
	<div class="login-content">
		<!-- 品牌标识区域 -->
		<div class="brand-header">
			<h1 class="brand-title">PHPEMS</h1>
			<p class="brand-subtitle">用户注册</p>
		</div>

		<!-- 注册表单卡片 -->
		<div class="card-container" style="padding:35px 25px;">
			<van-form @submit="onSubmit" class="login-form">
				<!-- 用户名输入框 -->
				<template v-if="hasCode">
					<div class="input-group captcha-group">
						<van-icon name="shield-o" class="input-icon" />
						<div class="captcha-input-wrapper">
							<van-field
									v-model="formData.verifycode"
									placeholder="验证码"
									:rules="[{ required: true, message: '请输入验证码' }]"
									required
							/>
						</div>
						<div class="captcha-button disabled" v-if="outTime > 0">
							{{outTime}}秒后重发
						</div>
						<div class="captcha-button" @click="reSendPhoneCode" v-else>
							重新发送
						</div>
					</div>

					<!-- 密码输入框 -->
					<div class="input-group">
						<van-icon name="lock" class="input-icon" />
						<van-field
								v-model="formData.userpassword"
								type="password"
								placeholder="请输入密码"
								:rules="[{ required: true, message: '请填写密码' }]"
								required
						/>
					</div>

					<!-- 重复密码输入框 -->
					<div class="input-group">
						<van-icon name="lock" class="input-icon" />
						<van-field
								v-model="formData.userpassword2"
								type="password"
								placeholder="请重复输入密码"
								:rules="[{ required: true, message: '请重复密码' }]"
								required
						/>
					</div>

					<van-button
							block
							native-type="submit"
							:loading="loading"
							class="login-button"
					>
						注 册
					</van-button>
				</template>
				<template v-else>
					<div class="input-group">
						<van-icon name="user-o" class="input-icon" />
						<van-field
							v-model="formData.username"
							placeholder="请输入用户名"
							:rules="[{ required: true, message: '请填写用户名' }]"
							required
						/>
					</div>

					<!-- 邮箱输入框 -->
					<div class="input-group">
						<van-icon name="envelop-o" class="input-icon" />
						<van-field
							v-model="formData.useremail"
							type="email"
							placeholder="请输入邮箱"
							:rules="[
								{ required: true, message: '请输入邮箱' },
								{ pattern: /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/, message: '请输入正确的邮箱' }
							]"
							required
						/>
					</div>

					<!-- 验证码输入框 -->
					<div class="input-group captcha-group">
						<van-icon name="shield-o" class="input-icon" />
						<div class="captcha-input-wrapper">
							<van-field
									v-model="formData.randcode"
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



					<!-- 提交按钮 -->
					<van-button
						block
						native-type="submit"
						:loading="loading"
						class="login-button"
					>
						发送邮箱验证码
					</van-button>
				</template>

				<!-- 返回登录链接 -->
				<div class="links" style="justify-content: center;">
					<router-link to="/mobile/auth/login">
						已有账号，返回登录
					</router-link>
				</div>
			</van-form>
		</div>
	</div>
</template>

<style scoped>
</style>

<script setup>
import {onMounted, ref} from 'vue';
import { useRouter } from 'vue-router';
import { showFailToast, showSuccessToast } from 'vant';
import authApi from '@/framework/api/auth.js';
import api from "@/framework/api/index.js";
import userApi from "@/framework/api/user.js";
import {layer} from "@layui/layui-vue";

const router = useRouter();
const loading = ref(false);
const formData = ref({
	username: '',
	useremail: '',
	randcode: '',
	randid:''
});
const randImage = ref('');
const hasCode = ref(false);
const outTime = ref(0);
const intervalHandle = ref(null);
const onSubmit = async () => {
	// 验证两次密码是否一致
	if (formData.value.userpassword !== formData.value.userpassword2) {
		showFailToast('两次输入的密码不一致');
		return;
	}

	loading.value = true;
	try {
		if(hasCode.value){
			if(formData.value.userpassword === formData.value.userpassword2)
			{
				await authApi.register({
					username:formData.value.username,
					useremail:formData.value.useremail,
					userpassword:formData.value.userpassword,
					randcode:formData.value.verifycode,
				});
				showSuccessToast('注册成功');
				// 注册成功后跳转到登录页
				await router.push('/mobile/auth/login');
			}
			else
			{
				layer.msg('两次输入的密码不一致');
			}
		}
		else
		{
			await authApi.getRegisterCode({
				username:formData.value.username,
				useremail:formData.value.useremail,
				captcha: formData.value.randcode,
				captchaId: formData.value.randid
			});
			showSuccessToast('验证码发送成功');
			hasCode.value = true;
			outTime.value = 120;
			intervalHandle.value = setInterval(() => {
				outTime.value --;
				if(outTime.value <= 0)
				{
					clearInterval(intervalHandle.value);
				}
			},1000)
		}
	} catch (e) {
		showFailToast(e.msg ?? '操作失败');
	} finally {
		loading.value = false;
	}
};
const getRandCode = async () => {
	try
	{
		const res = await api.utilsApi.getRandCode();
		randImage.value = res.image;
		formData.value.randid = res.id;
	}
	catch (e) {
		//showFailToast(e.msg??'获取验证码失败');
	}
};
const reSendPhoneCode = async() => {
	clearInterval(intervalHandle.value);
	hasCode.value = false;
	formData.value = {
		username: '',
		useremail: '',
		userpassword: '',
		randcode: '',
		randid:''
	};
	await getRandCode();
}
onMounted(async() => {
	await getRandCode();
})
</script>