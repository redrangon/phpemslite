<template>
	<lay-card>
		<lay-field title="模块设置">
			<lay-form ref="settingForm" :labelWidth="120" :model="model" :pane="false" class="form" size="md">
				<lay-form-item label="关闭注册" prop="closeregist" required>
					<lay-switch v-model="model.closeregist"></lay-switch>
				</lay-form-item>
				<lay-form-item label="实名认证" prop="userverify" required>
					<lay-radio v-model="model.userverify" label="自动认证" name="userverify" value="0"></lay-radio>
					<lay-radio v-model="model.userverify" label="手动认证" name="userverify" value="1"></lay-radio>
				</lay-form-item>
				<lay-form-item label="&nbsp;">
					<lay-button type="primary" @click="submitSetting">提交</lay-button>
				</lay-form-item>
			</lay-form>
		</lay-field>
	</lay-card>
</template>
<style scoped></style>
<script>
import userApi from '@/framework/api/admin/user.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {ref} from 'vue';

export default {
	mixins: [baseMixin],
	data() {
		return {
			model:ref({})
		}
	},
	created() {
		this.getData();
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				this.model = await userApi.getConfig();
			},null,null)
		},
		submitSetting:async function(){
			await this.base(async () => {
				await userApi.setConfig(this.model);
			});
		}
	}
}
</script>
<style>
.panel-container{
	padding:20px;
}
</style>