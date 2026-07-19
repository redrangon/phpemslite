<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 通行证ID</span><lay-input v-model="search.passport" allow-clear style="width: 180px;"></lay-input>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="gosearch">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-tab v-model="searchType">
			<lay-tab-item title="培训" id="plan">
				<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" :data-source="dataSource" v-model:selected-keys="selectedKeys">
					<template v-slot:pmstatus="{row}">
						<span v-if="row.pmstatus == 1">已完成</span>
						<span v-else>训练中</span>
					</template>
					<template v-slot:pmverify="{row}">
						<span v-if="row.pmverify == 1" style="color:#16baaa">已审核</span>
						<span v-else style="color:#FFB800">待审核</span>
					</template>
					<template v-slot:pmpayment="{row}">
						<span v-if="row.pmpayment == 1" style="color:#16baaa">已缴费</span>
						<span v-else style="color:#FFB800">待缴费</span>
					</template>
					<template #footer>
						<lay-page v-model="page.current"  :layout="layout" v-model:limit="page.limit" :total="page.total"  @change="changePage" style="float:right;"></lay-page>
					</template>
				</lay-table>
			</lay-tab-item>
			<lay-tab-item title="证书" id="cert">
				<lay-table ref="ctableRef" :default-toolbar="false" :columns="ccolumns" :data-source="cdataSource" v-model:selected-keys="cselectedKeys">
					<template v-slot:pmstatus="{row}">
						<span v-if="row.pmstatus == 1">已完成</span>
						<span v-else>训练中</span>
					</template>
					<template v-slot:pmverify="{row}">
						<span v-if="row.pmverify == 1" style="color:#16baaa">已审核</span>
						<span v-else style="color:#FFB800">待审核</span>
					</template>
					<template v-slot:pmpayment="{row}">
						<span v-if="row.pmpayment == 1" style="color:#16baaa">已缴费</span>
						<span v-else style="color:#FFB800">待缴费</span>
					</template>
					<template #footer>
						<lay-page v-model="cpage.current"  :layout="clayout" v-model:limit="cpage.limit" :total="cpage.total"  @change="cchangePage" style="float:right;"></lay-page>
					</template>
				</lay-table>
			</lay-tab-item>
			<lay-tab-item title="成绩" id="score">
				<lay-table ref="etableRef" :default-toolbar="false" :columns="ecolumns" :data-source="edataSource" v-model:selected-keys="eselectedKeys">
					<template v-slot:pmstatus="{row}">
						<span v-if="row.pmstatus == 1">已完成</span>
						<span v-else>训练中</span>
					</template>
					<template v-slot:pmverify="{row}">
						<span v-if="row.pmverify == 1" style="color:#16baaa">已审核</span>
						<span v-else style="color:#FFB800">待审核</span>
					</template>
					<template v-slot:pmpayment="{row}">
						<span v-if="row.pmpayment == 1" style="color:#16baaa">已缴费</span>
						<span v-else style="color:#FFB800">待缴费</span>
					</template>
					<template #footer>
						<lay-page v-model="epage.current" :layout="elayout" v-model:limit="epage.limit" :total="epage.total"  @change="echangePage" style="float:right;"></lay-page>
					</template>
				</lay-table>
			</lay-tab-item>
		</lay-tab>		
	</lay-card>
</template>
<script>
import plan from '@/framework/api/plan.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
	data() {
		return {
			dataSource:ref([]),
			cdataSource:ref([]),
			edataSource:ref([]),
			searchType:ref('plan'),
			tableRef:ref(),
			selectedKeys:ref(),
			ctableRef:ref(),
			cselectedKeys:ref(),
			etableRef:ref(),
			eselectedKeys:ref(),
			layout:ref(['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip']),
			clayout:ref(['count', 'prev', 'page', 'next']),
			elayout:ref(['count', 'prev', 'page', 'next']),
			page:ref({limit:20,current:1,total:0}),
			cpage:ref({limit:10,current:1,total:0}),
			epage:ref({limit:10,current:1,total:0}),
			search:{}
		}
	},
	emits: ['setVal'],
	setup(){
		const columns = [{
			title:'ID',
			key:'pmid',
			width:'20px'
		},{
			title:'姓名',
			key:'pmname',
			width:'150px'
		},{
			title:'单位',
			key:'pmunitallname'
		},{
			title:'训练状态',
			customSlot:'pmstatus',
			key:'pmstatus',
			width:'100px'
		},{
			title:'审核状态',
			customSlot:'pmverify',
			key:'pmverify',
			width:'100px'
		},{
			title:'缴费状态',
			customSlot:'pmpayment',
			key:'pmpayment',
			width:'100px'
		}];
		const ccolumns = [{
			title:'ID',
			key:'ceqid',
			width:'50px'
		},{
			title:'姓名',
			key:'pmname',
			width:'120px'
		},{
			title:'证书名称',
			key:'cetitle',
			width:'120px'
		},{
			title:'获取时间',
			key:'ceqtime',
			width:'150px'
		},{
			title:'有效期',
			key:'ceqexpiretime',
			width:'150px'
		}];
		const ecolumns = [{
			title:'ID',
			key:'ehid',
			width:'20px'
		},{
			title:'考试名称',
			key:'ehexam',
			width:'120px'
		},{
			title:'分数',
			key:'ehscore',
			width:'80px'
		},{
			title:'考试时间',
			key:'ehstarttime',
			width:'160px'
		},{
			title:'考试用时',
			key:'ehtime',
			width:'80px'
		}];
		return {columns,ccolumns,ecolumns}
	},
	created() {
		this.$emit('setVal',{bcmus:[{
				title:'首页',
				path:'/'
			},{
				title:'计划',
				path:'/plan'
			},{
				title:'综合查询',
				path:'/plan/search'
			}
		]})
	},
	methods:{
		getPlan:async function(){
			const id = layer.load(0);
			const data = await plan.searchPlanMember({
				search:this.search,
				limit:this.page.limit,
				page:this.page.current
			});
			this.page = data.page?data.page:[];
			this.dataSource = data.data?data.data:[];
			layer.close(id);
		},
		getCert:async function(){
			const id = layer.load(0);
			const data = await plan.searchPlanCert({
				search:this.search,
				limit:this.cpage.limit,
				page:this.cpage.current
			});
			this.cpage = data.page?data.page:[];
			this.cdataSource = data.data?data.data:[];
			layer.close(id);
		},
		getScore:async function(){
			const id = layer.load(0);
			const data = await plan.searchPlanScore({
				search:this.search,
				limit:this.epage.limit,
				page:this.epage.current
			});
			this.epage = data.page?data.page:[];
			this.edataSource = data.data?data.data:[];
			layer.close(id);
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getPlan()
		},
		cchangePage:function({current}){
			this.cpage.current = current
			this.getCert()
		},
		echangePage:function({current}){
			this.epage.current = current
			this.getScore()
		},
		gosearch:function(){
			if(!this.search.passport){
				layer.msg('请输入通行证ID');
				return;
			}
			if(this.searchType == 'plan')
			{
				this.getPlan();
			}
			else if(this.searchType == 'cert')
			{
				this.getCert();
			}
			else if(this.searchType == 'score')
			{
				this.getScore();
			}
		}
	},
	watch:{
		searchType:function(){
			this.gosearch();
		}
	}
}
</script>
<style scoped>
	.table {
		border-collapse:collapse;
		border:1px solid #aaa;
		width:100%;
		text-align: center;
	}
	.table thead{
		background-color: #fafafa;
		font-weight: bold;
	}
	.table th {
		border:1px solid #ddd;
		padding:10px;
		width:80px;
	}
	.table td {
		padding:10px;
		border:1px solid #ddd;
		min-width:80px;
	}
	.table .left{
		text-align: left;
	}
</style>