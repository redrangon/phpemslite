<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 关键字：</span><lay-input v-model="search.keyword" allow-clear style="width: 180px;"></lay-input>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card class="pagecontent">
		<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" v-model:selectedKeys="selectedKeys" :data-source="dataSource" id="pnid">
			<template #toolbar>
				<lay-space size="md">
					{{ plan.planname }}
				</lay-space>
				<lay-space size="md" class="pull-right">
					<lay-button type="primary" size="sm" @click="showAddPage = true">添加通知公告</lay-button>
				</lay-space>
			</template>
			<template #pnsort="{ row }">
				<lay-input v-model="row.pnsort" @change="modifySequence(row)"/>
			</template>
			<template #pnthumb="{ row }">
				<lay-avatar :src="row.pnthumb?row.pnthumb:'/src/assets/images/noimages.png'" size="lg"></lay-avatar>
			</template>
			<template #footer>
				<lay-row>
					<lay-col md="12">
						<lay-button type="danger" size="sm" @click="delNotice" :disabled="selectedKeys.length < 1">删除选中数据</lay-button>
					</lay-col>
					<lay-col md="12">
						<lay-page v-model="page.current" :layout="layout" v-model:limit="page.limit" :total="page.total"  @change="changePage" style="float:right;"></lay-page>
					</lay-col>
				</lay-row>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delNotice(row.pnid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :area="['900px']" :btn="addPageBtns" title="添加通知公告">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="model" :pane="false" size="md" :labelWidth="100" class="form" ref="addPageForm">
				<lay-form-item label="标题" prop="pntitle" required>
					<lay-input v-model="model.pntitle" placeholder="请输入标题"></lay-input>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="pnthumb">
					<myThumb v-model:src="model.pnthumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="排序" prop="pnsort">
					<lay-input v-model="model.pnsort" placeholder="请输入排序值"></lay-input>
				</lay-form-item>
				<lay-form-item label="详情" prop="pncontent">
					<myEditor v-model:content="model.pncontent"></myEditor>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :area="['900px']" :btn="modifyPageBtns" title="编辑通知公告">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modifyModel" :pane="false" size="md" :labelWidth="100" class="form" ref="modifyPageForm">
				<lay-form-item label="标题" prop="pntitle" required>
					<lay-input v-model="modifyModel.pntitle" placeholder="请输入标题"></lay-input>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="pnthumb">
					<myThumb v-model:src="modifyModel.pnthumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="排序" prop="pnsort">
					<lay-input v-model="modifyModel.pnsort" placeholder="请输入排序值"></lay-input>
				</lay-form-item>
				<lay-form-item label="详情" prop="pncontent">
					<myEditor v-model:content="modifyModel.pncontent"></myEditor>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import planApi from '@/framework/api/admin/plan.js';
import {layer} from '@layui/layui-vue';
import myThumb from '@/components/desktop/Thumb.vue';
import {ref} from 'vue';
import myEditor from '@/components/master/Editor.vue';
import baseMixin from "@/framework/mixins/baseMixin.js";

export default {
	data() {
		return {
			columns:[{
				title:'复选',
				type: "checkbox",
				width:'40px',
				fixed: "left"
			},{
				title:"ID",
				width: "80px",
				key: "pnid"
			},{
				title:'权重',
				key:'pnsort',
				customSlot:"pnsort",
				width:'80px'
			},{
				title:'缩略图',
				key:'pnthumb',
				customSlot:'pnthumb',
				width:'100px'
			},{
				title:'标题',
				key:'pntitle'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"100px"
			}],
			planid:0,
			plan:{},
			dataSource:[],
			search:{},
			selectedKeys:[],
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{ current: 1, limit: 10, total: 0 },
			model:{},
			modifyModel:{},
			showAddPage:false,
			showModifyPage:false,
			addPageBtns:[
				{
					text: "确认",
					callback: () => {
						this.$refs['addPageForm'].validate().then((res) => {
							this.showAddPage = false;
							this.addNotice();
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
						this.$refs['modifyPageForm'].validate().then((res) => {
							this.showModifyPage = false;
							this.modifyNotice();
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
	async mounted() {
		this.planid = this.$route.params.planid;
		this.plan = await planApi.getPlan(this.planid);
		await this.getData();
	},
	components:{
		myThumb:myThumb,
		myEditor:myEditor
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await planApi.getNoticeList({
					planid: this.planid,
					search:this.search,
					limit:this.page.limit,
					page:this.page.current
				});
				this.page = {
					current: data.page,
					limit: data.limit,
					total: data.total
				};
				this.dataSource = data.data;
			}, null, null);
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		addNotice:async function(){
			await this.base(async () => {
				this.model.pnplanid = this.planid;
				await planApi.addNotice(this.model);
			}, '添加成功', this.getData);
		},
		modifyNotice:async function(){
			await this.base(async () => {
				await planApi.modifyNotice(this.modifyModel);
			}, '修改成功', this.getData);
		},
		modifySequence: async function(row){
			await this.base(async () => {
				await planApi.modifyNotice({
					pnid: row.pnid,
					pnsort: row.pnsort
				});
			}, '修改成功', this.getData);
		},
		delNotice:function(id){
			this.confirmDelete(async () => {
				await planApi.delNotice(id?[id]:this.selectedKeys);
			}, this.getData);
		},
		showModify:function(row){
			this.modifyModel = {...row};
			this.showModifyPage = true;
		},
	}
}
</script>