<template>
	<lay-card>
        <lay-quote>
            通过人数按照正式考试分数计算
        </lay-quote>
		<lay-table id="piid" v-model:selectedKeys="selectedKeys" :columns="columns" :data-source="dataSource">
			<template #toolbar>
				<span>{{plan.planname}}</span>
				<span style="float:right;">
					<lay-button type="primary" @click="showExamSource()">添加考试</lay-button>
					<lay-button type="danger" @click="statsExamSource()">更新数据</lay-button>
				</span>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary"  @click="showScore(row.piitemid)">成绩</lay-button>
				<lay-button size="xs" type="danger"  @click="delItem(row.piid)">移除</lay-button>
			</template>
			<template #footer>
				<lay-row>
					<lay-button :disabled="selectedKeys.length < 1" size="sm" type="danger" @click="delItem()">删除选中数据</lay-button>
				</lay-row>
			</template>
		</lay-table>
		<lay-layer v-model="showExamPage" :area="['800px']" title="添加考试">
			<lay-card>
				<lay-space>
					<lay-space></lay-space>
					<lay-space>
						<span style='width:70px'> 关键字：</span><lay-input v-model="search.keyword" allow-clear size="sm" style="width: 180px;"></lay-input>
					</lay-space>
					<lay-space>
						<lay-button size="sm" type="primary" @click="getExamData">搜索</lay-button>
					</lay-space>
				</lay-space>
			</lay-card>
			<lay-card>
				<lay-table id="basicid" v-model:selectedKeys="examSelectedKeys" :columns="examColumns" :data-source="examSource">
					<template v-slot:operator="{ row }">
						<lay-button size="xs" type="primary"  @click="addItem(row.csid)">加入</lay-button>
					</template>
					<template #footer>
						<lay-row>
							<lay-col md="12">
								<lay-button type="primary"  @click="addItem()">加入计划</lay-button>
							</lay-col>
							<lay-col md="12">
								<lay-page v-model="page.current" v-model:limit="page.limit" :layout="layout" :total="page.total" style="float:right;" @change="changePage"></lay-page>
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
import planApi from "@/framework/api/admin/plan.js";
import examApi from '@/framework/api/admin/exam.js';
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
				key: "piid"
			},{
				title:'考场名称',
				key:'basic'
			},{
				title:'通过人数',
				key:'finishNumber',
				width: "120px",
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"100px"
			}],
			examColumns:[{
				title:'复选',
				type: "checkbox",
				width:'60px',
				fixed: "left"
			},{
				title:"ID",
				width: "80px",
				key: "basicid"
			},{
				title:'考场名称',
				key:'basic'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"55px"
			}],
			dataSource:[],
			planid:0,
			plan:{},
			selectedKeys:[],
			showExamPage:false,
			search:{},
			examSource:[],
			examSelectedKeys:[],
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{ current: 1, limit: 10, total: 0 },
			statsPage:{ current: 1, limit: 500, total: 0 },
		}
	},
	mixins:[baseMixin],
	async mounted() {
		this.planid = this.$route.params.planid;
		this.plan = await planApi.getPlan(this.planid);
	},
	async activated(){
		await this.getData();
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				this.dataSource = await planApi.getItemList({
					planid: this.planid,
					itemtype: 'exam'
				});
			}, null, null);
		},
		addItem:function(id){
			this.base(async () => {
				await planApi.addItem({
					piplanid:this.planid,
					pitype:'exam',
					piitemid:id? [id]: this.examSelectedKeys
				});
				this.showExamPage = false;
			}, '添加成功');
		},
		delItem:function(id){
			this.confirmDelete(async () => {
				await planApi.delItem(id?[id]:this.selectedKeys);
			}, this.getData);
		},
		getExamData:async function(){
			await this.execute(async () => {
				const data = await examApi.getBasicList({
					search:this.search,
					limit:this.page.limit,
					page:this.page.current
				});
				this.page = {
					current: data.page,
					limit: data.limit,
					total: data.total
				};
				this.examSource = data.data;
			}, null, null);
		},
		showExamSource:async function(){
			this.showExamPage = true;
			await this.getExamData();
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getExamData()
		},
		showScore:function(basicId)
		{
			this.$router.push('/desktop/master/plan/score/' + this.planid + '/' + basicId);
		},
		statsExamSource:async function(){
			await this.base(async () => {
				let status = true;
				let data;
				while(status){
					data = await planApi.statsPlanExamScore({
						planId:this.planid,
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
		}
	}
}
</script>