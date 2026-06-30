<template>
	<lay-card>
		<lay-table :default-toolbar="false" :columns="columns" :data-source="dataSource" ref="table" id="atid" v-model:selected-keys="selectedKeys" even>
			<template #toolbar>
				<lay-button type="primary" size="sm" @click="showAddType = true">添加新类型</lay-button>
			</template>
			<template #footer>
				<lay-button type="primary" size="sm" @click="deleteData()" :disabled="selectedKeys.length < 1">删除选中数据</lay-button>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="deleteData(row.atid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddType" :shade="false" :area="['500px']" :btn="addBtns" title="添加类型">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="model" ref="attachTypeFrom" required>
				<lay-form-item label="类型名称" prop="attachtype">
					<lay-input v-model="model.attachtype"></lay-input>
				</lay-form-item>
				<lay-form-item label="扩展名" prop="attachexts">
					<lay-input v-model="model.attachexts">></lay-input>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyType" :shade="false" :area="['500px']" :btn="modifyBtns" title="修改类型">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modelModify" ref="attachModifyTypeFrom" required>
				<lay-form-item label="类型名称" prop="attachtype">
					<lay-input v-model="modelModify.attachtype"></lay-input>
				</lay-form-item>
				<lay-form-item label="扩展名" prop="attachexts">
					<lay-input v-model="modelModify.attachexts">></lay-input>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import attach from '@/framework/api/attach.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
	data() {
		return {
			columns:attach.columns.exts,
			dataSource:ref(),
			selectedKeys:ref(),
			model:ref({}),
			modelModify:ref({}),
			showAddType:ref(false),
			showModifyType:ref(false),
			addBtns:ref([
				{
					text: "确认",
					callback: () => {
						this.$refs['attachTypeFrom'].validate().then((res) => {
							this.addType();					
						}).catch( res => {
							console.log(res);
						});	
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showAddType = false;
					}
				}
			]),
			modifyBtns:ref([
				{
					text: "确认",
					callback: () => {
						this.$refs['attachModifyTypeFrom'].validate().then((res) => {
							this.modifyType();					
						}).catch( res => {
							console.log(res);
						});	
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showModifyType = false;
					}
				}
			])
		}
	},
	emits: ['setVal'],
	async created() {
		this.$emit('setVal',{bcmus:[{
			title:'首页',
			path:'/'
		},{
			title:'附件',
			path:'/attach'
		},{
			title:'附件类型',
			path:'/attach/type'
		}]});
		await this.getData();
	},
	methods:{
		addType:async function(){
			const data = await attach.addAttachType({
				attach:this.model
			});
			this.getData();
			this.showAddType = false;
		},
		modifyType:async function(){
			const data = await attach.modifyAttachType({
				attach:this.modelModify
			});
			this.getData();
			this.showModifyType = false;
		},
		getData:async function(){
			const id = layer.load(0);
			const data = await attach.getAttachTypes();
			console.log(data);
			this.dataSource = Object.values(data?data:[]);
			layer.close(id);
		},
		deleteData:function(id){
			let ids = this.selectedKeys;
			if(id){
				ids = [id];
			}
			attach.delAttachType(ids,this.getData);			
		},
		showModify:function(val){
			this.modelModify = ref(val);
			this.showModifyType = true;
		}
	}
}
</script>