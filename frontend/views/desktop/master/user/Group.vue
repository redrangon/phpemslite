<template>
	<lay-card>
		<lay-table :default-toolbar="false" :columns="columns" :data-source="dataSource" id="groupid" v-model:selectedKey="selectedRadio" v-model:selectedKeys="selectedCheckBox" even >
			<template v-slot:toolbar>
				<lay-button type="primary" size="sm" @click="showAddPage = true">添加用户组</lay-button>
			</template>
			<template #footer>
				<lay-button type="primary" size="sm" :disabled="selectedCheckBox?.length < 1" @click="delGroup()">删除选中用户组</lay-button>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="modelModify = row;showModifyPage = true">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delGroup(row.groupid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :shade="false" :area="['500px']" :btn="addPageBtns" title="添加用户组">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="model" ref="addPageFrom" required>
				<lay-form-item label="用户组" prop="groupname">
					<lay-input v-model="model.groupname"></lay-input>
				</lay-form-item>
				<lay-form-item label="组描述" prop="groupdescribe">
					<lay-input v-model="model.groupdescribe"></lay-input>
				</lay-form-item>
				<lay-form-item label="绑定模型" prop="groupmoduleid">
					<lay-select v-model="model.groupmoduleid" placeholder="请选择">
						<lay-select-option :value="item.moduleid" :label="item.modulename" v-for="item in modules"></lay-select-option>
					</lay-select>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :shade="false" :area="['500px']" :btn="modifyPageBtns" title="修改用户组">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modelModify" ref="modifyPageFrom" required>
				<lay-form-item label="用户组" prop="groupname">
					<lay-input v-model="modelModify.groupname"></lay-input>
				</lay-form-item>
				<lay-form-item label="组描述" prop="groupdescribe">
					<lay-input v-model="modelModify.groupdescribe"></lay-input>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import userApi from "@/framework/api/admin/user.js";
import baseMixin from "@/framework/mixins/baseMixin.js";
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
	mixins: [baseMixin],
	data() {
		return {
			columns:ref([{
				title:"选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			},{
				title:'ID',
				key:'groupid',
				width:'20px'
			},{
				title:'用户组',
				key:'groupname',
				width:'240px'
			},{
				title:'默认注册',
				width:'100px',
				type:'radio'
			},{
				title:'绑定模型',
				key:'modulename',
				width:'160px'
			},{
				title:'用户组描述',
				key:'groupdescribe'
			},{
				title:'操作',
				customSlot:"operator",
				width:'120px',
				key:"operator"
			}]),
			dataSource:ref([]),
			selectedCheckBox:ref(),
			selectedRadio:ref(),
			modules:ref([]),
			model:ref({}),
			modelModify:ref({}),
			showAddPage:ref(false),
			showModifyPage:ref(false),
			addPageBtns:ref([
				{
					text: "确认",
					callback: () => {
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showAddPage = false;
							this.addGroup();					
						}).catch( res => {
							console.log(res);
						});	
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showAddPage = false;
					}
				}
			]),
			modifyPageBtns:ref([
				{
					text: "确认",
					callback: () => {
						this.$refs['modifyPageFrom'].validate().then((res) => {
							this.showModifyPage = false;
							this.modifyGroup();					
						}).catch( res => {
							console.log(res);
						});	
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showModifyPage = false;
					}
				}
			])
		}
	},
	async created() {
		await this.getData();
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await userApi.getGroups();
				this.dataSource = data;
				for(const item of data)
				{
					if(item.groupdefault === 1)
					{
						this.selectedRadio = item.groupid;
						break;
					}
				}
			},null,null);
		},
		delGroup:function(id){
			let ids = this.selectedCheckBox;
			if(id){
				ids = [id];
			}
			userApi.deleteGroups(ids);
		},
		addGroup:async function(){
			await userApi.addGroup({
				group:this.model
			});
			await this.getData();
		},
		modifyGroup:async function(){
			await userApi.modifyGroup({
				group:this.modelModify
			});
			await this.getData();
		}
	},
	watch:{
		selectedRadio:async function(n,o)
		{
			if(o && o !== n)
			{
				await this.base(async () => {
					await userApi.setDefaultGroup(n);
				},null,null);
			}
		}
	}
}
</script>