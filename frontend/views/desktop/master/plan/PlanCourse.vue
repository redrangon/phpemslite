<template>
	<lay-card>
		<lay-table :columns="columns" v-model:selectedKeys="selectedKeys" :data-source="dataSource" id="piid">
			<template #toolbar>
				<span>{{plan.planname}}</span>
				<span style="float:right;">
					<lay-button type="primary" @click="showCourseSource()">添加课程</lay-button>
					<lay-button type="danger" @click="statsPlanCourse()">更新数据</lay-button>
				</span>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="danger"  @click="delItem(row.piid)">移除</lay-button>
			</template>
			<template #footer>
				<lay-row>
					<lay-button type="danger" size="sm" @click="delItem()" :disabled="selectedKeys.length < 1">删除选中数据</lay-button>
				</lay-row>
			</template>
		</lay-table>
		<lay-layer v-model="showCoursePage" :area="['800px']" title="添加课程">
			<lay-card>
				<lay-space>
					<lay-space></lay-space>
					<lay-space>
						<span style='width:70px'> 关键字：</span><lay-input v-model="search.keyword" size="sm" style="width: 180px;" allow-clear></lay-input>
					</lay-space>
					<lay-space>
						<lay-button type="primary" size="sm" @click="getCourseData">搜索</lay-button>
					</lay-space>
				</lay-space>
			</lay-card>
			<lay-card>
				<lay-table :columns="courseColumns" :data-source="courseSource" v-model:selectedKeys="courseSelectedKeys" id="csid">
					<template v-slot:operator="{ row }">
						<lay-button size="xs" type="primary"  @click="addItem(row.csid)">加入</lay-button>
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
import planApi from "@/framework/api/admin/plan.js";
import courseApi from '@/framework/api/admin/course.js';
import contentApi from "@/framework/api/admin/content.js";
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
				title:'课程名称',
				key:'cstitle'
			},{
				title:'学完人数',
				key:'finishNumber',
				width: "120px",
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"60px"
			}],
			courseColumns:[{
				title:'复选',
				type: "checkbox",
				width:'60px',
				fixed: "left"
			},{
				title:"ID",
				width: "80px",
				key: "csid"
			},{
				title:'课程名称',
				key:'cstitle'
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
			showCoursePage:false,
			search:{},
			courseSource:[],
			courseSelectedKeys:[],
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{ current: 1, limit: 10, total: 0 },
			statsPage:{ current: 1, limit: 500, total: 0 },
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
					itemtype: 'course'
				});
			}, null, null);
		},
		addItem:function(id){
			this.base(async () => {
				await planApi.addItem({
					piplanid:this.planid,
					pitype:'course',
					piitemid:id? [id]: this.courseSelectedKeys
				});
				this.showCoursePage = false;
			}, '添加成功');
		},
		delItem:function(id){
			this.confirmDelete(async () => {
				await planApi.delItem(id?[id]:this.selectedKeys);
			}, this.getData);
		},
		getCourseData:async function(){
			await this.execute(async () => {
				const data = await courseApi.getSubjectList({
					search:this.search,
					limit:this.page.limit,
					page:this.page.current
				});
				this.page = {
					current: data.page,
					limit: data.limit,
					total: data.total
				};
				this.courseSource = data.data;
			}, null, null);
		},
		showCourseSource:async function(){
			this.showCoursePage = true;
			await this.getCourseData();
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getCourseData()
		},
		statsPlanCourse:async function(){
			await this.base(async () => {
				let status = true;
				let data;
				while(status){
					data = await planApi.statsPlanCourse({
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