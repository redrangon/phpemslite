<template>
	<lay-card>
		<lay-quote>
			<p>证书颁发时间为审核通过时间。证书需审核后成为有效证书</p>
		</lay-quote>
		<lay-table :columns="columns" v-model:selectedKeys="selectedKeys" :data-source="dataSource" id="pcid">
			<template #toolbar>
				<span>{{plan.planname}} - {{cert.cetitle}}</span>
				<lay-button type="primary" style="float:right;"  @click="importEligibleMember()">导入合格学员</lay-button>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary"  @click="modifyItem(row)">编辑</lay-button>
				<lay-button size="xs" type="danger"  @click="delItem(row.pcid)">移除</lay-button>
			</template>
			<template #pcstatus="{ row }">
				<p v-if="row.pcstatus === 0">未审核</p>
				<p v-else-if="row.pcstatus === 1">已审核</p>
				<p v-else>未知</p>
			</template>
			<template #footer>
				<lay-row>
					<lay-button type="primary" size="sm" @click="verifyCert()" :disabled="selectedKeys.length < 1">通过审核</lay-button>
					<lay-button type="danger" size="sm" @click="cancelCert()" :disabled="selectedKeys.length < 1">撤销审核</lay-button>
					<lay-button type="danger" size="sm" @click="delItem()" :disabled="selectedKeys.length < 1">删除选中数据</lay-button>
				</lay-row>
			</template>
		</lay-table>
		<lay-layer v-model="showCertPage" :area="['800px']" title="添加证书">
			<lay-card>
				<lay-space>
					<lay-space></lay-space>
					<lay-space>
						<span style='width:70px'> 序列号：</span><lay-input v-model="search.pcsn" size="sm" style="width: 180px;" allow-clear placeholder="请输入序列号"></lay-input>
					</lay-space>
					<lay-space>
						<lay-button type="primary" size="sm" @click="getCertData">搜索</lay-button>
					</lay-space>
				</lay-space>
			</lay-card>
			<lay-card>
				<lay-table :columns="certColumns" :data-source="certSource" v-model:selectedKeys="certSelectedKeys" id="pcceid">
					<template v-slot:operator="{ row }">
						<lay-button size="xs" type="primary"  @click="addItem(row.pcceid)">加入</lay-button>
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
		<lay-layer v-model="showModifyPage" :area="['600px','500px']" :btn="modifyPageBtns" title="修改证书">
			<div style="padding: 20px 50px 20px 20px;">
				<lay-form :model="modelModify" :pane="false" size="md" :labelWidth="100" class="form" ref="modifyPageFrom">
					<lay-form-item label="序列号" prop="pcsn" required>
						<lay-input v-model="modelModify.pcsn" placeholder="请输入证书序列号" type="number"></lay-input>
					</lay-form-item>
					<lay-form-item label="通行证ID" prop="pcpassport" required>
						<lay-input v-model="modelModify.pcpassport" placeholder="请输入通行证ID" type="number"></lay-input>
					</lay-form-item>
					<lay-form-item label="状态" prop="pcstatus" required>
						<lay-radio v-model="modelModify.pcstatus" name="pcstatus" :value="0" label="未审核"></lay-radio>
						<lay-radio v-model="modelModify.pcstatus" name="pcstatus" :value="1" label="已审核"></lay-radio>
					</lay-form-item>
					<lay-form-item label="过期时间" prop="pcexpirytime">
						<lay-date-picker v-model="modelModify.pcexpirytime" type="datetime" placeholder="请选择过期时间"></lay-date-picker>
					</lay-form-item>
				</lay-form>
			</div>
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
export default {
	data() {
		return {
			columns:[{
				title:'复选',
				type: "checkbox",
				width:'60px',
				fixed: "left"
			},{
				title:"ID",
				width: "80px",
				key: "pcid"
			},{
				title:'证书序列号',
				key:'pcsn'
			},{
				title:'身份证',
				width:'180px',
				key:'pcpassport'
			},{
				title:'颁发时间',
				key:'pctime',
				width:'160px'
			},{
				title:'过期时间',
				key:'pcexpirytime',
				width:'160px'
			},{
				title:'状态',
				customSlot:'pcstatus',
				key:'pcstatus',
				width:'100px'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"120px"
			}],
			certColumns:[{
				title:'复选',
				type: "checkbox",
				width:'60px',
				fixed: "left"
			},{
				title:"ID",
				width: "80px",
				key: "ceid"
			},{
				title:'证书名称',
				key:'cetitle'
			},{
				title:'有效期（天）',
				key:'cedays'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"55px"
			}],
			dataSource:[],
			planId:0,
			certId:0,
			plan:{},
			cert:{},
			selectedKeys:[],
			showCertPage:false,
			showModifyPage:false,
			search:{},
			certSource:[],
			certSelectedKeys:[],
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{ current: 1, limit: 10, total: 0 },
			statsPage:{ current: 1, limit: 500, total: 0 },
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
		this.planId = this.$route.params.planid;
		this.certId = this.$route.params.certid;
		this.plan = await planApi.getPlan(this.planId);
		this.cert = await certApi.getCert(this.certId)
		await this.getData();
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await planApi.getCertList({
					search: {
						pcplanid: this.planId,
						pcceid: this.certId
					}
				});
				this.dataSource = data.data;
			}, null, null);
		},
		addItem:function(id){
			this.base(async () => {
				const ceid = id || this.certSelectedKeys[0];
				if(!ceid) {
					layer.msg('请选择要添加的证书');
					return;
				}
				await planApi.addCert({
					pcceid: ceid,
					pcplanid: this.planid,
					pcstatus: 0
				});
				this.showCertPage = false;
				await this.getData();
			}, '添加成功');
		},
		delItem:function(id){
			this.confirmDelete(async () => {
				await planApi.delCert(id?[id]:this.selectedKeys);
			}, this.getData);
		},
		modifyItem:function(row){
			this.modelModify = JSON.parse(JSON.stringify(row));
			this.showModifyPage = true;
		},
		saveModify:async function(){
			await this.execute(async () => {
				await planApi.modifyCert(this.modelModify);
			}, '修改成功', this.getData);
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
		importEligibleMember:async function(){
			await this.base(async () => {
				let status = true;
				let data;
				while(status){
					data = await planApi.importEligibleMember({
						planId:this.planId,
						certId:this.certId,
						limit:this.statsPage.limit,
						page:this.statsPage.current
					});
					this.statsPage.current = data.page + 1;
					this.statsPage.toal = data.toal;
					this.statsPage.limit = data.limit;
					status = data.status;
				}
				this.statsPage.current = 1;
			});
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getCertData()
		},
		verifyCert:function(){
			this.confirmOperate('确定要通过审核吗？',async () => {
				await planApi.verifyCert(this.selectedKeys)
			},this.getData);
		},
		cancelCert:function(){
			this.confirmOperate('确定要撤销审核吗？',async () => {
				await planApi.cancelCert(this.selectedKeys)
			},this.getData);
		}
	}
}
</script>