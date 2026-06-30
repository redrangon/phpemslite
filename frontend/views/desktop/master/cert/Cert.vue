<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 关键字：</span><lay-input v-model="search.keyword" allow-clear style="width: 220px;"></lay-input>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" :data-source="dataSource" id="ceid" v-model:selected-keys="selectedKeys" even>
			<template #toolbar>
				<lay-button type="primary" size="sm" @click="showAddPage = true">添加凭证</lay-button>
			</template>
			<template v-slot:cethumb="{row}">
				<img :src="row.cethumb?row.cethumb:'/src/assets/images/noimages.png'" style="height:40px;max-width:80px;">
			</template>
			<template #footer>
				<lay-button type="danger" size="sm" @click="delCert()" :disabled="selectedKeys.length < 1">删除选中数据</lay-button>
				<lay-page v-model="page.current" v-model:limit="page.limit" :total="page.total"  @change="changePage" style="float:right;"></lay-page>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delCert(row.ceid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :area="['800px','90%']" :btn="addPageBtns" title="添加凭证">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="model" :pane="false" size="md" :labelWidth="100" class="form" ref="addPageFrom">
				<lay-form-item label="凭证名称" prop="cetitle" required>
					<lay-input v-model="model.cetitle" placeholder="请输入凭证名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="有效期" prop="cedays" required>
					<lay-input v-model="model.cedays" placeholder="请输入有效期" style="width:150px"><template #append="{ disabled }">天</template></lay-input>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="cethumb" required>
					<myThumb v-model:src="model.cethumb"></myThumb>
				</lay-form-item>
				<lay-form-item label="凭证模板" prop="cetpl" required>
					<myThumb v-model:src="model.cetpl"></myThumb>
				</lay-form-item>
				<lay-form-item label="标签定位" prop="cetags" required>
					<lay-textarea placeholder="请输入标签定位" v-model="model.cetags"></lay-textarea>
				</lay-form-item>
				<lay-form-item label="凭证简介" prop="cedescribe" required>
					<lay-textarea placeholder="请输入凭证简介" v-model="model.cedescribe"></lay-textarea>
				</lay-form-item>
				<lay-form-item label="凭证详情" prop="cetext" required>
					<myEditor v-model:content="model.cetext">{{ model.cetext }}</myEditor>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :area="['800px','90%']" :btn="modifyPageBtns" title="修改凭证">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modelModify" :pane="false" size="md" :labelWidth="100" class="form" ref="modifyPageFrom">
				<lay-form-item label="凭证名称" prop="cetitle" required>
					<lay-input v-model="modelModify.cetitle" placeholder="请输入凭证名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="有效期" prop="cedays" required>
					<lay-input v-model="modelModify.cedays" placeholder="请输入有效期" style="width:150px"><template #append="{ disabled }">天</template></lay-input>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="cethumb" required>
					<myThumb v-model:src="modelModify.cethumb"></myThumb>
				</lay-form-item>
				<lay-form-item label="凭证模板" prop="cetpl" required>
					<myThumb v-model:src="modelModify.cetpl"></myThumb>
				</lay-form-item>
				<lay-form-item label="标签定位" prop="cetags" required>
					<lay-textarea placeholder="请输入标签定位" v-model="modelModify.cetags"></lay-textarea>
				</lay-form-item>
				<lay-form-item label="凭证简介" prop="cedescribe" required>
					<lay-textarea placeholder="请输入凭证简介" v-model="modelModify.cedescribe"></lay-textarea>
				</lay-form-item>
				<lay-form-item label="凭证详情" prop="cetext" required>
					<myEditor v-model:content="modelModify.cetext">{{ modelModify.cetext }}</myEditor>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import certApi from '@/framework/api/admin/cert.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
import myThumb from "@/components/desktop/Thumb.vue";
import myEditor from "@/components/master/Editor.vue";

export default {
	data() {
		return {
			columns:[{
				title:"选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			},{
				title:'ID',
				key:'ceid',
				width:'20px'
			},{
				title:'缩略图',
				key:'cethumb',
				customSlot:'cethumb',
				width:"80px"
			},{
				title:'凭证名称',
				key:'cetitle'
			},{
				title:'有效期（天）',
				key:'cedays',
				width:"120px"
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"100"
			}],
			dataSource:[],
			search:{},
			selectedKeys:[],
			page:{ current: 1, limit: 20, total: 0 },
			showAddPage:false,
			showModifyPage:false,
			model:{},
			modelModify:{},
			addPageBtns:[
				{
					text: "确认",
					callback: () => {
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showAddPage = false;
							this.addCert();					
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
			],
			modifyPageBtns:[
				{
					text: "确认",
					callback: () => {
						this.$refs['modifyPageFrom'].validate().then((res) => {
							this.showModifyPage = false;
							this.modifyCert();
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
			]
		}
	},
	mixins:[baseMixin],
	components:{
		myEditor,
		myThumb
	},
	async mounted() {
		await this.getData();
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await certApi.getCertList({
					page:this.page.current,
					limit:this.page.limit,
					search:this.search
				});
				this.dataSource = data.data;
				this.page.current = data.page;
				this.page.limit = data.limit;
				this.page.total = data.total;
			}, null, null);
		},
		showModify:function(row){
			this.modelModify = {...row};
			this.showModifyPage = true;
		},
		delCert:function(id){
			this.confirmDelete(async () => {
				await certApi.delCert(id?[id]:this.selectedKeys);
			}, this.getData);
		},
		addCert:function(){
			this.base(async () => {
				await certApi.addCert(this.model);
			}, '添加成功');
		},
		modifyCert:function(){
			this.base(async () => {
				await certApi.modifyCert(this.modelModify);
			}, '修改成功');
		},
		changePage:function({ current, limit }){
			this.page.current = current;
			this.page.limit = limit;
			this.getData();
		},
	}
}
</script>