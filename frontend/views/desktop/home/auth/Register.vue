<template>
	<lay-space direction="vertical" fill wrap class="login_box">
		<h2 style="text-align: center;color:#FFFFFF;font-size:18px;">注册新账号</h2>
		<lay-form :model="model" :pane="false" size="md" labelWidth="80" class="form" ref="registerForm">
			<template v-if="hasCode">
				<lay-form-item class="formitem" required error-message="请输入验证码" prop="verifycode">
					<lay-row space="5">
						<lay-col sm="14" xs="14"><lay-input prefix-icon="layui-icon-chat" size="lg"  v-model="model.verifycode" placeholder="请输入验证码" autocomplete="off"></lay-input></lay-col>
						<lay-col sm="10" xs="10">
							<lay-button type="primary" fluid style="height: 44px;" v-if="outTime > 0" disabled="">{{outTime}}秒后重发</lay-button>
							<lay-button type="primary" fluid style="height: 44px;" @click="reSendPhoneCode" v-else>重新发送</lay-button>
						</lay-col>
					</lay-row>
				</lay-form-item>
				<lay-form-item class="formitem" required prop="userpassword" :rules="{type:'string',min:6,max:16}" error-message="密码必须为8-16个字符串">
					<lay-input prefix-icon="layui-icon-password" size="lg"  v-model="model.userpassword" type="password" password placeholder="请输入密码" autocomplete="new-password"></lay-input>
				</lay-form-item>
				<lay-form-item class="formitem" required prop="userpassword2" :rules="{type:'string',min:6,max:16}" error-message="密码必须为8-16个字符串">
					<lay-input prefix-icon="layui-icon-password" size="lg"  v-model="model.userpassword2" type="password" password placeholder="请再次输入密码" autocomplete="new-password"></lay-input>
				</lay-form-item>
				<lay-form-item>
					<lay-space direction="vertical" fill wrap size="md">
						<lay-button type="normal" fluid @click="submit">注册</lay-button>
						<p style="text-align: center;">
							<router-link style="color:#ffffff;" to="/desktop/home/auth/login">已有账号，立即登录</router-link>
						</p>
					</lay-space>
				</lay-form-item>
			</template>
			<template v-else>
				<lay-form-item class="formitem" :rules="{type:'string',min:6}" prop="username" required error-message="用户名至少要6位">
					<lay-input prefix-icon="layui-icon-username" size="lg"  v-model="model.username" placeholder="请输入用户名" autocomplete="username"></lay-input>
				</lay-form-item>
				<lay-form-item class="formitem" :rules="{type:'email'}" prop="useremail" required error-message="请输入邮箱">
					<lay-input prefix-icon="layui-icon-email" size="lg"  v-model="model.useremail" placeholder="请输入邮箱" autocomplete="useremail"></lay-input>
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
				<lay-form-item>
					<lay-space direction="vertical" fill wrap size="md">
						<lay-button type="normal" fluid @click="submit">获取邮箱验证码</lay-button>
						<p style="text-align: center;">
							<router-link style="color:#ffffff;" to="/desktop/home/auth/login">已有账号，立即登录</router-link>
						</p>
					</lay-space>
				</lay-form-item>
			</template>
		</lay-form>
	</lay-space>
</template>
<script setup>
import {onMounted, ref} from 'vue';
import {useRouter} from "vue-router";
import {layer} from "@layui/layui-vue";
import utilsApi from "@/framework/api/utils.js";
import authApi from "@/framework/api/auth.js";
const router = useRouter();
const randImage = ref('');
const hasCode = ref(false);
const model = ref({
	username: '',
	useremail: '',
	userpassword: '',
	randcode: '',
	randid:''
});
const outTime = ref(0);
const intervalHandle = ref(null);
const registerForm = ref(null);
const submit = async function () {
	registerForm.value.validate().then(async (res) => {
		try{
			if(hasCode.value)
			{
				if(model.value.userpassword === model.value.userpassword2)
				{
					await authApi.register({
						username:model.value.username,
						useremail:model.value.useremail,
						userpassword:model.value.userpassword,
						randcode:model.value.verifycode,
					});
					layer.confirm('注册成功',{
						title:'操作提示',
						close: () => {
							router.push('/desktop/home/auth/login');
						}
					});
				}
				else
				{
					layer.msg('两次输入的密码不一致');
				}
			}
			else
			{
				await authApi.getRegisterCode({
					username:model.value.username,
					useremail:model.value.useremail,
					captcha: model.value.randcode,
					captchaId: model.value.randid
				});
				layer.confirm("短信已发送，有效时间为5分钟。",{title:'发送提示'})
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
		}
		catch(e){
			layer.msg(e.message || '操作失败');
			await getRandCode();
		}
	})
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

const reSendPhoneCode = async() => {
	clearInterval(intervalHandle.value);
	hasCode.value = false;
	model.value = {
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
<style scoped>
@import "@/assets/css/desktop/auth.css";
.textinfo{
	padding:10px 0;
	background-color: rgba(0,0,0,0.3);
	color:#FFFFFF;
	text-align: center;
}
</style>