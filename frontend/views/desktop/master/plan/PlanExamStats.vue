<template>
	<lay-card>
		<lay-table :default-toolbar="false" :columns="columns" :data-source="dataSource">
			<template #toolbar>
				{{ basic.basic }} 成绩分析
				<lay-button type="primary" style="float: right;" @click="knowstats()">知识点正确率分析</lay-button>
			</template>
			<template v-slot:questype="{ row }">
				{{ questypes[row.type].questype }}
			</template>
			<template v-slot:planA="{ row }">
				{{ row.A && row.number?(row.A / row.number * 100).toFixed(2):0 }} %
			</template>
			<template v-slot:planB="{ row }">
				{{ row.B && row.number?(row.B / row.number * 100).toFixed(2):0 }} %
			</template>
			<template v-slot:planC="{ row }">
				{{ row.C && row.number?(row.C / row.number * 100).toFixed(2):0 }} %
			</template>
			<template v-slot:planD="{ row }">
				{{ row.D && row.number?(row.D / row.number * 100).toFixed(2):0 }} %
			</template>
			<template v-slot:rightnumber="{ row }">
				{{ row.right?row.right:0 }}
			</template>
			<template v-slot:viewnumber="{ row }">
				{{ row.number?row.number:0 }}
			</template>
			<template v-slot:rightratio="{ row }">
				{{ row.right && row.number?(row.right / row.number * 100).toFixed(2):0 }} %
			</template>
			<template #footer>
				<lay-page v-model="page.current" :layout="layout" v-model:limit="page.limit" :total="page.total"  @change="changePage" style="float:right;"></lay-page>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="viewquestion(row.id)">详情</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showPage" :area="['800px','80%']" title="试题正确率详情">
		<lay-tab v-model="tabcurrent" style="padding:0px 20px;">
			<lay-tab-item title="正确名单" id="1">
				<lay-table :columns="rightcolumns" :data-source="datarights"></lay-table>
			</lay-tab-item>
			<lay-tab-item title="错误名单" id="2">
				<lay-table :columns="wrongcolumns" :data-source="datawrongs"></lay-table>
			</lay-tab-item>
		</lay-tab>
	</lay-layer>
</template>
<style scoped></style>
<script>
import plan from '@/framework/api/plan.js';
import exam from '@/framework/api/exam.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
	data() {
		return {
			dataSource:[],
			datarights:[],
			datawrongs:[],
			basic:ref({}),
			tableRef:ref(),
			layout:ref(['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip']),
			page:ref({limit:20,current:1,total:0}),
			search:{},
			planid:ref(),
			basicid:ref(),
			questypes:ref(),
			tabcurrent:ref("1"),
			showPage:ref(false)
		}
	},
	emits: ['setVal'],
	setup(){
		const columns = [{
			title:'ID',
			key:'id',
			width:'80px'
		},{
			title:'题型',
			customSlot:'questype',
			key:'questype',
			width:'120px'
		},{
			title:'试题名称',
			key:'title'
		},{
			title:'A',
			customSlot:'planA',
			key:'planA',
			width:'80px'
		},{
			title:'B',
			customSlot:'planB',
			key:'planB',
			width:'80px'
		},{
			title:'C',
			customSlot:'planC',
			key:'planC',
			width:'80px'
		},{
			title:'D',
			customSlot:'planD',
			key:'planD',
			width:'80px'
		},{
			title:'正确次数',
			customSlot:'rightnumber',
			key:'rightnumber',
			width:'80px'
		},{
			title:'出现次数',
			customSlot:'viewnumber',
			key:'viewnumber',
			width:'80px'
		},{
			title:'正确率',
			customSlot:'rightratio',
			key:'rightratio',
			width:'80px'
		},{
			title:'操作',
			customSlot:"operator",
			key:"operator",
			width:"50px"
		}];
		const rightcolumns = [{
			title:'考卷ID',
			key:'id',
			width:'80px'
		},{
			title:'用户ID',
			key:'userid',
			width:'80px'
		},{
			title:'用户名',
			key:'username'
		},{
			title:'姓名',
			key:'usertruename'
		}];
		const wrongcolumns = [{
			title:'考卷ID',
			key:'id',
			width:'80px'
		},{
			title:'用户ID',
			key:'userid',
			width:'80px'
		},{
			title:'用户名',
			key:'username'
		},{
			title:'姓名',
			key:'usertruename'
		}];
		return {columns,rightcolumns,wrongcolumns}
	},
	async created() {
		this.planid = this.$route.params.planid;
		this.basicid = this.$route.params.basicid;
		this.$emit('setVal',{bcmus:[{
				title:'首页',
				path:'/'
			},{
				title:'计划',
				path:'/plan'
			},{
				title:'计划管理',
				path:'/plan/plan'
			},{
				title:'计划配置',
				path:'/plan/plansetting/'+this.planid
			},{
				title:'成绩统计',
				path:'/plan/planexamstats/'+this.planid+'/'+this.basicid
			},{
				title:'成绩分析',
				path:'/plan/planscorestats/'+this.planid+'/'+this.basicid
			}
			]})
		this.questypes = await exam.getQuestypes();
		await this.getData()
	},
	components:{},
	methods:{
		getData:async function(){
			const id = layer.load(0);
			const data = await plan.getPlanScoreStats({
				planid:this.planid,
				basicid:this.basicid,
				limit:this.page.limit,
				page:this.page.current
			});
			this.page = data.page;
			console.log(this.page,'pppp');
			this.dataSource = data.stats;
			this.basic = data.basic;
			layer.close(id);
		},
		knowstats:function(){
			this.$router.push('/plan/planknowstats/'+ this.planid + '/' + this.basicid)
		},
		viewquestion:async function(questionid){
			const id = layer.load(0);
			const data = await plan.getHistoryQuestion({
				planid:this.planid,
				basicid:this.basicid,
				questionid:questionid
			});
			this.datarights = data.datarights;
			this.datawrongs = data.datawrongs;
			this.basic = data.basic;
			this.tabcurrent = ref("1");
			this.showPage = true;
			layer.close(id);
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		}
	}
}
</script>