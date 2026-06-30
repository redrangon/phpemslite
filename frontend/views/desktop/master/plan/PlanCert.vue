<template>
	<lay-card>
		<lay-table :columns="columns" v-model:selectedKeys="selectedKeys" :data-source="dataSource" id="piid">
			<template #toolbar>
				<span>{{plan.planname}}</span>
				<lay-button type="primary" style="float:right;"  @click="showCertSource()">添加证书</lay-button>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary"  @click="showPlanCertList(row.piitemid)">名单</lay-button>
				<lay-button size="xs" type="danger"  @click="delItem(row.piid)">移除</lay-button>
			</template>
			<template #footer>
				<lay-row>
					<lay-button type="danger" size="sm" @click="delItem()" :disabled="selectedKeys.length < 1">删除选中数据</lay-button>
				</lay-row>
			</template>
		</lay-table>
		<lay-layer v-model="showCertPage" :area="['800px']" title="添加证书">
			<lay-card>
				<lay-space>
					<lay-space></lay-space>
					<lay-space>
						<span style='width:70px'> 关键字：</span><lay-input v-model="search.keyword" size="sm" style="width: 180px;" allow-clear placeholder="请输入关键字"></lay-input>
					</lay-space>
					<lay-space>
						<lay-button type="primary" size="sm" @click="getCertData">搜索</lay-button>
					</lay-space>
				</lay-space>
			</lay-card>
			<lay-card>
				<lay-table :columns="columns" :data-source="certSource" v-model:selectedKeys="certSelectedKeys" id="ceid">
					<template v-slot:operator="{ row }">
						<lay-button size="xs" type="primary"  @click="addItem(row.ceid)">加入</lay-button>
					</template>
					<template #footer>
						<lay-row>
							<lay-col md="12">
								<lay-button type="primary"  @click="addItem()">加入计划</lay-button>
							</lay-col>
							<lay-col md="12">
								<lay-page v-model="page.current" :layout="layout" v-model:limit="page.limit" :total="page.total" @change="changePage" style="float:right;"></lay-page>
							</lay-col>
						</lay-row>
					</template>
				</lay-table>
			</lay-card>
		</lay-layer>
	</lay-card>
</template>
<style scoped></style>
<script>
import {layer} from '@layui/layui-vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {ref} from 'vue';
import certApi from "@/framework/api/admin/cert.js";
import planApi from "@/framework/api/admin/plan.js";
import examApi from "@/framework/api/admin/exam.js";
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
			planid:0,
			plan:{},
			selectedKeys:[],
			showCertPage:false,
			showModifyPage:false,
			search:{},
			certSource:[],
			certSelectedKeys:[],
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{ current: 1, limit: 10, total: 0 },
			modelModify:{},
			modifyPageBtns:[
				{
					text: "确认",
					callback: () => {
						this.$refs['modifyPageFrom'].validate().then((res) => {
							this.showModifyPage = false;
							this.saveModify();					
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
	methods:{
		getData:async function(){
			await this.execute(async () => {
				this.dataSource = await planApi.getItemList({
					planid: this.planid,
					itemtype: 'cert'
				});
			}, null, null);
		},
		addItem:function(id){
			this.base(async () => {
				await planApi.addItem({
					piplanid:this.planid,
					pitype:'cert',
					piitemid:id? [id]: this.certSelectedKeys
				});
				this.showCertPage = false;
			}, '添加成功');
		},
		delItem:function(id){
			this.confirmDelete(async () => {
				await planApi.delItem(id?[id]:this.selectedKeys);
			}, this.getData);
		},
		getCertData:async function(){
			await this.execute(async () => {
				const data = await certApi.getCertList({
					search:this.search,
					limit:this.page.limit,
					page:this.page.current
				});
				this.page = {
					current: data.page,
					limit: data.limit,
					total: data.total
				};
				this.certSource = data.data;
			}, null, null);
		},
		showCertSource:async function(){
			this.showCertPage = true;
			await this.getCertData();
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getCertData()
		},
		showPlanCertList:function(certid){
			this.$router.push('/desktop/master/plan/cert/'+this.planid+'/'+certid);
		}
	}
}
</script>