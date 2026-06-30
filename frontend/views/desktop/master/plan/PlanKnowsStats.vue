<template>
	<lay-card>
		<lay-table :default-toolbar="false" :columns="knowcolumns" :data-source="dataSource">
			<template #toolbar>
				{{ basic.basic }} 知识点正确率分析
				<lay-button type="primary" style="float: right;" @click="questionstats()">试题正确率分析</lay-button>
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
				<lay-page v-model="page.current"  :layout="layout" v-model:limit="page.limit" :total="page.total"  @change="changePage" style="float:right;"></lay-page>
			</template>
		</lay-table>
	</lay-card>
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
			questypes:ref()
		}
	},
	emits: ['setVal'],
	setup(){
		const knowcolumns = [{
			title:'知识点ID',
			key:'knowsid',
			width:'120px'
		},{
			title:'知识点名称',
			key:'knows'
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
		}];
		return {knowcolumns}
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
			const data = await plan.getPlanKnowsStats({
				planid:this.planid,
				basicid:this.basicid,
				limit:this.page.limit,
				page:this.page.current
			});
			this.page = data.page;
			this.dataSource = data.stats;
			this.basic = data.basic;
			layer.close(id);
		},
		questionstats:function(){
			this.$router.push('/plan/planscorestats/'+ this.planid + '/' + this.basicid)
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		}
	}
}
</script>