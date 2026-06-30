<template>
	<lay-card class="pagecontent">
		<lay-card style="position: relative;">
			<lay-page-header :content="basic.basic" @back="$router.go(-1)" class="planbread"></lay-page-header>
			<lay-tab v-model="tabCurrent" type="brief" @change="getData">
				<lay-tab-item id="1">
					<template #title>
						<span class="tabtitle">模拟考试</span>
					</template>
					<lay-container>
						<lay-panel shadow="never">
							<div class="sectionitem">
								<span style="color:#1E9FFF">您共进行了 {{ page.total }}</span> 次考试。
							</div>
						</lay-panel>
						<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" :data-source="histories" id="ehid" even>
							<template #footer>
								<lay-row>
										<lay-page v-model="page.current" v-model:limit="page.limit" :total="page.total" :layout="layout" @change="changePage" style="float:right;"></lay-page>
								</lay-row>
							</template>
							<template v-slot:ehtime="{ row }">
								{{ timeFormat(row.ehtime) }}
							</template>
							<template v-slot:ehscore="{ row }">
								<template v-if="row.ehstatus === 1">{{ row.ehscore }}</template>
							</template>
							<template v-slot:operator="{ row }">
								<template v-if="row.ehstatus === 1">
									<lay-button type="normal" size="xs" @click="toStats(row.ehid)">统计</lay-button>
									<lay-button type="normal" size="xs" @click="toView(row.ehid)">解析</lay-button>
									<lay-button type="warm" size="xs" @click="toRedo(row.ehid)">重做</lay-button>
									<lay-button type="danger" size="xs" @click="delData(row.ehid)" v-if="row.ehtype !== 2">删除</lay-button>
								</template>
								<template v-else>
									等待教师评分
								</template>
							</template>
						</lay-table>
					</lay-container>
				</lay-tab-item>
				<lay-tab-item id="2">
					<template #title>
						<span class="tabtitle">正式考试</span>
					</template>
					<lay-container>
						<lay-panel shadow="never">
							<div class="sectionitem">
								<span style="color:#1E9FFF">您共进行了 {{ page.total }}</span> 次考试。
							</div>
						</lay-panel>
						<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" :data-source="histories" id="ehid" even>
							<template #footer>
								<lay-row>
									<lay-page v-model="page.current" v-model:limit="page.limit" :total="page.total" :layout="layout" @change="changePage" style="float:right;"></lay-page>
								</lay-row>
							</template>
							<template v-slot:ehtime="{ row }">
								{{ timeFormat(row.ehtime) }}
							</template>
							<template v-slot:ehscore="{ row }">
								<template v-if="row.ehstatus === 1">{{ row.ehscore }}</template>
							</template>
							<template v-slot:operator="{ row }">
								<template v-if="row.ehstatus === 1">
									<lay-button type="normal" size="xs" @click="toStats(row.ehid)">统计</lay-button>
									<lay-button type="normal" size="xs" @click="toView(row.ehid)">解析</lay-button>
									<lay-button type="warm" size="xs" @click="toRedo(row.ehid)">重做</lay-button>
									<lay-button type="danger" size="xs" @click="delData(row.ehid)" v-if="row.ehtype !== 2">删除</lay-button>
								</template>
								<template v-else>
									等待教师评分
								</template>
							</template>
						</lay-table>
					</lay-container>
					<lay-page v-model="page.current" theme="blue" :limit="page.limit" :total="page.total" @change="changePage" style="float: right;"></lay-page>
				</lay-tab-item>
			</lay-tab>

		</lay-card>
	</lay-card>
</template>
<script>
import {layer} from '@layui/layui-vue';
import examApi from '@/framework/api/exam.js';
import {useAuthStore} from "@/stores/auth.js";
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	name:'examHistory',
	setup() {
		const authStore = useAuthStore();
		return {authStore};
	},
	mixins:[baseMixin],
	data() {
		return {
			columns:[{
				title: '试卷名称',
				key: 'ehexam'
			}, {
				title: '答题时间',
				key: 'ehstarttime',
				width: '140px'
			}, {
				title: '交卷时间',
				key: 'ehendtime',
				width: '140px'
			}, {
				title: '用时',
				key: 'ehtime',
				customSlot: 'ehtime',
				width: '90px'
			}, {
				title: '得分',
				key: 'ehscore',
				customSlot: 'ehscore',
				width: '70px'
			}, {
				title: '操作',
				customSlot: "operator",
				key: "operator",
				width: "190px"
			}],
			tabCurrent:"1",
			histories:[],
			page:{
				current:1,
				limit:20,
				total:1
			},
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			basic:{},
			plan:{}
		}
	},
	async mounted(){
		this.basic = this.authStore.basic;
		if(this.basic.basicexam.model === 2)this.$router.replace('/desktop/home/exam/exam');
		else {
			await this.getData();
		}
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await examApi.getHistoryList({
					page:this.page.current,
					limit:this.page.limit,
					examType:this.tabCurrent
				});
				this.histories = data.data;
				this.page.current = data.page;
				this.page.limit = data.limit;
				this.page.total = data.total;
			},null,null);
		},
		changePage:function({current,limit}){
			this.page.current = current
			this.page.limit = limit
			this.getData();	
		},
		toStats:function(ehid){
			this.$router.push('/desktop/home/exam/result/' + ehid)
		},
		delData:function(ehid){
			this.confirmDelete(async () => {
				await examApi.delHistory(ehid);
			},this.getData)
		},
		timeFormat:function(time){
			let format = 0;
			if(time >= 60){
				if(time % 60){
					format = parseInt(time / 60) +'分'+ time % 60 + '秒'
				}else{
					format = parseInt(time / 60) +'分'
				}
			}else{
				format = time + '秒'
			}
			return format;
		},
		toRedo:async function(ehid){
			await this.execute(async () => {
				const data = await examApi.getReTestExamSession(ehid);
				this.$router.push({path:'/desktop/home/exam/paper/'+data.sessionid});
			},null,null);
		},
		toView:function(ehid){
			this.$router.push('/desktop/home/exam/historyview/'+ehid)
		}
	}
}
</script>
<style scoped>
.sectiontitle{
	font-size:16px;
	line-height: 2;
	border-bottom: 1px solid #fafafa;
	margin-bottom:10px;
	width: 100%;
	padding:20px 10px;
}
.sectionitem{
	line-height:32px;
	font-size:16px;
	border-bottom:1px solid #fafafa;
}
.sectionitem span{
	color:#999999;
	margin-left:10px;
}
.sectionitem button{
	float:right;
}
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