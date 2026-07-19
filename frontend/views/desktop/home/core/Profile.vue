<template>
	<lay-card class="pagecontent">
		<lay-card>
			<lay-tab v-model="tabCurrent" :activeBarTransition="true" type="brief">
				<lay-tab-item id="1">
					<template #title>
						<span class="tabtitle">实名信息</span>
					</template>
					<div style="padding:20px">
						<lay-quote v-if="user.userstatus <= 0">请先完成实名信息后再开始学习。务必正确填写姓名和通行证ID，此信息将用来填入报名信息和发放证书，提交后将无法更改。</lay-quote>
						<lay-quote v-else-if="user.userstatus === 1">您的实名认证申请已提交，请等待审核。</lay-quote>
						<lay-quote v-else-if="user.userstatus === 2">您的实名认证申请被拒绝，请核验认证信息后重新提交。</lay-quote>
						<lay-quote v-else-if="user.userstatus === 3">您已完成实名认证，如需要修改实名信息，请联系管理员。</lay-quote>
						<lay-quote v-else>未知状态，请联系管理员。</lay-quote>
						<lay-form v-if="user.userstatus <= 0 || user.userstatus === 2" :labelWidth="120" :model="user">
							<lay-form-item label="照片" prop="userphoto">
								<myThumb v-model:src="user.userphoto" style="width: 120px;height:168px;"></myThumb>
							</lay-form-item>
							<lay-form-item label="姓名" prop="usertruename" required>
								<lay-input v-model="user.usertruename"></lay-input>
							</lay-form-item>
							<lay-form-item label="通行证ID" prop="userpassport" required>
								<lay-input v-model="user.userpassport" disabled></lay-input>
							</lay-form-item>
							<lay-form-item label="&nbsp;">
								<lay-button type="normal" @click="verifyUser()">提交</lay-button>
								<lay-button type="default" @click="getData()">重置</lay-button>
							</lay-form-item>
						</lay-form>
						<lay-form v-else :labelWidth="120" :model="user">
							<lay-form-item label="照片" prop="userphoto">
								<img :src="user.userphoto" style="width: 120px;height:168px;" />
							</lay-form-item>
							<lay-form-item label="姓名" prop="usertruename">
								<lay-input v-model="user.usertruename" disabled></lay-input>
							</lay-form-item>
							<lay-form-item label="通行证ID" prop="userpassport">
								<lay-input v-model="user.userpassport" disabled></lay-input>
							</lay-form-item>
							<lay-form-item v-if= "user.userstatus === 1" label="&nbsp;">
								<lay-button type="normal" @click="clearVerify()">撤回申请</lay-button>
							</lay-form-item>
						</lay-form>
					</div>
				</lay-tab-item>
				<lay-tab-item id="2">
					<template #title>
						<span class="tabtitle">用户资料</span>
					</template>
					<div style="padding:20px">
						<lay-form :labelWidth="120" :model="profile">
							<lay-form-item label="性别" prop="usergender">
								<lay-radio v-model="profile.usergender" label="男" name="usergender" value="男"></lay-radio>
								<lay-radio v-model="profile.usergender" label="女" name="usergender" value="女"></lay-radio>
							</lay-form-item>
							<lay-form-item label="&nbsp;">
								<lay-button type="normal" @click="modifyUser()">提交</lay-button>
								<lay-button type="default" @click="getData()">重置</lay-button>
							</lay-form-item>
						</lay-form>
					</div>
				</lay-tab-item>
				<lay-tab-item id="3">
					<template #title>
						<span class="tabtitle">修改密码</span>
					</template>
					<div style="padding:20px">
						<lay-form :labelWidth="120" :model="password">
							<lay-form-item label="原密码" prop="oldpassword" required>
								<lay-input v-model="password.oldpassword" password type="password"></lay-input>
							</lay-form-item>
							<lay-form-item label="新密码" prop="newpassword" required>
								<lay-input v-model="password.newpassword" password type="password"></lay-input>
							</lay-form-item>
							<lay-form-item label="确认新密码" prop="newpassword2" required>
								<lay-input v-model="password.newpassword2" password type="password"></lay-input>
							</lay-form-item>
							<lay-form-item label="&nbsp;">
								<lay-button type="normal" @click="modifyUserPassword()">提交</lay-button>
								<lay-button type="default" @click="getData()">重置</lay-button>
							</lay-form-item>
						</lay-form>
					</div>
				</lay-tab-item>
			</lay-tab>
		</lay-card>
	</lay-card>
</template>
<script>
import userApi from '@/framework/api/user.js';
import {layer} from '@layui/layui-vue';
import myThumb from '@/components/desktop/Thumb.vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {withConfirm} from "@/framework/utils/decorators.js";

export default {
	mixins: [baseMixin],
	components:{
		myThumb
	},
	data() {
		return {
			tabCurrent:"1",
			user:{
				username:'',
				userstatus: 0,
				userphoto:'',
				usertruename:'',
				userpassport:'',
				usergender:''
			},
			profile:{
				usergender:'男'
			},
			password:{
				oldpassword:'',
				ewpassword:'',
				newpassword2:''
			}
		}
	},
	async mounted() {
		await this.getData();
	},
	methods:{
		getData:function(){
			this.execute(async () => {
				const user = await userApi.getCurrentUser();
				this.user = {
					userpassport:user.userpassport,
					usertruename:user.usertruename,
					userphoto:user.userphoto,
					userstatus:user.userstatus,
				}
				this.profile = {
					usergender:user.usergender,
				}
			},null,null)
		},
		verifyUser:async function(){
			withConfirm(
				'确定要提交申请吗？',
				async () => {
					await userApi.verifyUser(this.user);
				}, this.getData
			)
		},
		clearVerify:async function(){
			withConfirm(
				'确定要撤回申请吗？',
				async () => {
					await userApi.cancelVerify();
				}, this.getData
			);
		},
		modifyUser:function(){
			this.execute(async () => {
				await userApi.modifyProfile(this.profile);
			},null,null);
		},
		modifyUserPassword:function(){
			this.execute(async () => {
				await userApi.modifyPassword(this.password);
			});
		}
	}
}
</script>
<style scoped>
.tabtitle{
	font-size: 16px;;
	padding-left:20px;
	padding-right: 20px;
}
</style>