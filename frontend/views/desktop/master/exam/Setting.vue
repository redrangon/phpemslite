<template>
	<lay-card>
		<template v-slot:title>
			<span style="font-size: 18px;">模块设置</span>
		</template>
		<lay-field title="水印设置">
			<lay-form :model="setting" ref="addPageFrom">
				<lay-form-item label="开启水印" prop="watermark">
					<lay-switch v-model="setting.watermark"></lay-switch>
				</lay-form-item>
				<lay-form-item label="字体大小" prop="fontsize" required>
					<lay-input v-model="setting.fontsize">
						<template #append="{ disabled }">px</template>
					</lay-input>
				</lay-form-item>
				<lay-form-item label="字体颜色" prop="color">
					<lay-color-picker v-model="setting.color"></lay-color-picker>
				</lay-form-item>
				<lay-form-item label="旋转角度" prop="rotate" required>
					<lay-input v-model="setting.rotate"></lay-input>
				</lay-form-item>
				<lay-form-item label="&nbsp;" required>
					<lay-button type="primary" @click="saveSetting">保存</lay-button>
				</lay-form-item>
			</lay-form>
		</lay-field>
	</lay-card>
</template>
<style scoped></style>
<script>
import examApi from '@/framework/api/admin/exam.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import {withLayer} from "@/framework/utils/decorators.js";
export default {
	data() {
		return {
			setting:{
				color:'#F2F2F2',
				rotate:-45
			}
		}
	},
	async mounted(){
		await this.getData();
	},
	methods:{
		base:async function(fn){
			await withLayer(fn,	null,this.getData);
		},
		getData:function(){
			withLayer(
					async () => {
						const data = await examApi.getConfig();
						this.setting = data??this.setting;
					},[null,null]
			);

		},
		saveSetting:function(){
			this.base( async() => {
				const data = await examApi.setConfig(this.setting);
				await this.getData();
			});
		},
	}
}
</script>