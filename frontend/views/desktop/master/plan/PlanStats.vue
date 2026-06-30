<template>
	<lay-card>
		<table class="table">
			<thead>
				<td colspan="4">人员信息</td>
			</thead>
			<tr>
				<th>计划参训人数</th>
				<td>{{ number }}</td>
				<th>目前参训（至少学完一个课件）人数</th>
				<td>{{ activenumber }}</td>
			</tr>
		</table>
		<table class="table">
			<thead>
				<td colspan="5">课程统计</td>
			</thead>
			<tr>
				<th>课程名称</th>
				<th>共计课件数</th>
				<th>平均学习课件数</th>
				<th>平均学习进度（%）</th>
				<th>学完人数</th>
			</tr>
			<tr v-for="(lesson,lid) in lessons" :key="lid">
				<td>{{ lesson.cstitle }}</td>
				<td>{{ lesson.all }}</td>
				<td>{{ lesson.avgused }}</td>
				<td>{{ lesson.avgrate }}</td>
				<td>{{ lesson.overnumber }}</td>
			</tr>
		</table>
		<table class="table">
			<thead>
				<td colspan="4">考试统计</td>
			</thead>
			<tr>
				<th>考场名称</th>
				<th>考试人次</th>
				<th>平均成绩</th>
				<th>通过人次</th>
			</tr>
			<tr v-for="(basic,bid) in basics" :key="bid">
				<td>{{ basic.basic }}</td>
				<td>{{ basic.number }}</td>
				<td>{{ basic.score }}</td>
				<td>{{ basic.passnumber }}</td>
			</tr>
		</table>
		<table class="table">
			<thead>
				<td colspan="4">分组统计</td>
			</thead>
			<tr>
				<th>组名</th>
				<th>计划人数</th>
				<th>完成人数</th>
			</tr>
			<tr v-for="(group,gid) in groups" :key="gid">
				<td>{{ group.groupname }}</td>
				<td>{{ groupstats[group.groupid]&&groupstats[group.groupid].number?groupstats[group.groupid].number:0 }}</td>
				<td>{{ groupstats[group.groupid]&&groupstats[group.groupid].passnumber?groupstats[group.groupid].passnumber:0 }}</td>
			</tr>
		</table>
	</lay-card>
</template>
<script>
import plan from '@/framework/api/plan.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
	data() {
		return {
			planid:ref(),
			lessons:ref(),
			basics:ref(),
			number:ref(),
			number:ref(),
			groupstats:ref(),
			activenumber:ref(),
			plan:ref(),
			groups:ref()
		}
	},
	emits:['setVal'],
	setup(){
		//
	},
	created() {
		this.planid = this.$route.params.planid
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
				title:'统计信息',
				path:'/plan/planstats/'+this.planid
			}
		]});
		this.getData();
	},
	methods:{
		getData:async function(){
			const id = layer.load(0);
			let data = await plan.getPlanStats(this.planid);
			this.lessons = data.lessons;
			this.basics = data.basics;
			this.number = data.number;
			this.groupstats = data.groupstats;
			this.activenumber = data.activenumber;
			this.plan = data.plan;
			this.groups = data.groups;
			layer.close(id);
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
		margin-bottom:20px;
	}
	.table thead{
		background-color: #fafafa;
		font-weight: bold;
	}
	.table th {
		border:1px solid #ddd;
		padding:10px;
		min-width:80px;
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