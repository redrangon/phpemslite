<template>
	<lay-card>
		<lay-tab v-model="tabCurrent" :activeBarTransition="true" type="brief" @change="getData">
			<lay-tab-item id="1">
				<template #title>
					<span class="tabtitle">已交卷</span>
				</template>
				<div style="margin-top: 10px;">
					<lay-container>
						<lay-quote>本界面仅显示正式考试成绩，未判分试卷显示的分数为客观题得分。</lay-quote>
						<lay-card>
							<lay-space direction="vertical">
								<lay-space size="lg">
									<lay-space>
										<span style='width:70px'> 学号</span><lay-input v-model="searchScore.passport" allow-clear style="width: 180px;"></lay-input>
									</lay-space>
									<lay-space>
										<span style='width:70px'> 考试时间</span>
										<lay-date-picker v-model="searchScore.range" :allow-clear="true" :placeholder="['开始日期','结束日期']" range></lay-date-picker>
									</lay-space>
									<lay-space>
										<span style='width:70px'> 考试分数</span>
										<lay-input-number v-model="searchScore.minscore" :max="100" :min="0" :step="10"></lay-input-number> -
										<lay-input-number v-model="searchScore.maxscore" :max="100" :min="0" :step="10"></lay-input-number>
									</lay-space>
									<lay-space>
										<lay-button type="primary" @click="getScore">搜索</lay-button>
									</lay-space>
								</lay-space>
							</lay-space>
						</lay-card>
						<lay-table ref="tableRef2" :columns="scoreColumns" :data-source="scoreSource" :default-toolbar="false">
							<template #toolbar>
								已交卷
							</template>
							<template #footer>
								<lay-page v-model="scorePage.current" v-model:limit="scorePage.limit" :layout="layout" :total="scorePage.total" style="float:right;" @change="changeScorePage"></lay-page>
							</template>
							<template v-slot:resit="{ row }">
								<span v-if="row.ehneedresit === 1" style="color:red;">撤销</span>
								<span v-else-if="row.ehscreenout === 1" :title="`原始得分${row.ehbasescore}`" style="color:red;">切屏违规</span>
                                <span v-else-if="row.ehstatus === 0">未判分</span>
								<span v-else>正常</span>
							</template>
							<template v-slot:operator="{ row }">
								<lay-button size="xs" type="primary" @click="readPaper(row.ehid)">阅卷</lay-button>
								<lay-button v-if="row.ehneedresit !== 1" size="xs" type="danger" @click="resitPage(row.ehid)">撤销</lay-button>
								<lay-button v-if="row.ehneedresit === 1" size="xs" type="primary" @click="showLog(row.ehid)">日志</lay-button>
								<lay-button v-else size="xs" type="primary" @click="openCamera(row.ehid)">照片</lay-button>
							</template>
						</lay-table>
					</lay-container>
				</div>
			</lay-tab-item>
			<lay-tab-item id="2">
				<template #title>
					<span class="tabtitle">正在考试</span>
				</template>
				<div style="margin-top: 10px;">
					<lay-container>
						<lay-quote>本界面仅显示正在考试的正式考试会话</lay-quote>
						<lay-card>
							<lay-space size="lg">
								<lay-space>
									<span style='width:70px'> 学号：</span>
									<lay-input v-model="search.passport "></lay-input>
								</lay-space>
								<lay-space>
									<lay-button type="primary" @click="getData">搜索</lay-button>
								</lay-space>
							</lay-space>
						</lay-card>
						<lay-table id="examsessionid" :columns="columns" :data-source="dataSource" :default-toolbar="false">
							<template #toolbar>
								<h3>正在考试</h3>
							</template>
							<template #footer>
								<lay-page v-model="sessionPage.current" v-model:limit="sessionPage.limit" :layout="layout" :total="sessionPage.total" style="float:right;" @change="changeSessionPage"></lay-page>
							</template>
						</lay-table>
					</lay-container>
				</div>
			</lay-tab-item>
		</lay-tab>
		<lay-layer v-model="showLogsPage" :area="['800px']" title="操作日志">
			<div style="padding: 20px;">
				<lay-table ref="logsTableRef" :columns="logColumns" :data-source="logSource" :default-toolbar="false">
					<template v-slot:logtype="{ row }">
						<span v-if="row.ehltype === 1" style="color:red;">撤销成绩</span>
						<span v-else-if="row.ehltype === 2" style="color:red;">撤销主观题评分</span>
						<span v-else>未知操作</span>
					</template>
					<template #footer>
						<lay-page v-model="logPage.current" v-model:limit="logPage.limit" :layout="layout" :total="logPage.total" style="float:right;" @change="changeLogPage"></lay-page>
					</template>
				</lay-table>
			</div>
		</lay-layer>
		<lay-layer v-model="showResitPage" :area="['700px']" :btn="showResitPageBtns" title="撤销成绩">
			<div style="padding: 20px;">
				<lay-form ref="resitPageForm" :label-width="100">
					<lay-form-item label="撤销原因">
						<lay-input v-model="resit.reason" :rows="4" placeholder="请输入撤销原因" type="textarea"></lay-input>
					</lay-form-item>
				</lay-form>
			</div>
		</lay-layer>
		<lay-layer v-model="showFacePage" :area="['800px']" title="查看详情">
			<div style="padding: 20px;">
				<table class="table">
					<thead>
						<tr><th colspan="5">考生信息</th></tr>
					</thead>
					<tbody>
					<tr>
						<td>姓名</td>
						<td>{{ face.pmname }}</td>
						<td>学号</td>
						<td>{{ face.pmpassport }}</td>
						<td rowspan="3"><img :src="face.pmphoto" width="90"></td>
					</tr>
					<tr>
						<td>抽卷IP</td>
						<td>{{ face.ehstartip }}</td>
						<td>交卷IP</td>
						<td>{{ face.ehendip }}</td>
					</tr>
					<tr>
						<td>抽卷设备</td>
						<td>{{ face.ehstartclient }}</td>
						<td>交卷设备</td>
						<td>{{ face.ehendclient }}</td>
					</tr>
					</tbody>
				</table>
				<lay-container fluid style="padding:20px 0;">
					<lay-row space="10">
						<lay-col v-for="(img,iid) in face.ehfaces" md="6" sm="12" xs="24">
							<div class="grid-demo">
								<img :src="img" width="100%" />
							</div>
						</lay-col>
					</lay-row>
				</lay-container>
			</div>
		</lay-layer>
	</lay-card>
</template>
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
<script>
import planApi from '@/framework/api/admin/plan.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	name:'planMonitor',
	mixins: [baseMixin],
	data() {
		return {
			search:{},
			searchScore:{},
			dataSource:[],
			scoreSource:[],
			logSource:[],
			face:{},
			tabCurrent:'1',
			columns:[{
				title: 'ID',
				key: 'esid',
				width: '80px'
			}, {
				title: '试卷名称',
				key: 'examsession'
			}, {
				title: '考生考号',
				key: 'examsessionpassport',
				width: '180px'
			}, {
				title: '开考时间',
				key: 'examsessionstarttime',
				width: '180px'
			}, {
				title: 'IP',
				key: 'examsessionip',
				width: '150px'
			}, {
				title: '客户端环境',
				key: 'examsessionclient',
				width: '240px'
			}],
			scoreColumns:[{
				title:'ID',
				key:'ehid',
				width:'80px'
			},{
				title:'姓名',
				key:'mname',
				width:'120px'
			},{
				title:'学号',
				key:'ehpassport',
				width:'180px'
			},{
				title:'分数',
				key:'ehscore',
				width:'70px'
			},{
				title:'考试名称',
				key:'ehexam'
			},{
				title:'抽卷IP',
				key:'ehstartip',
				width:'130px'
			},{
				title:'交卷IP',
				key:'ehendip',
				width:'130px'
			},{
				title:'抽卷终端',
				key:'ehstartclient',
				width:'140px'
			},{
				title:'交卷终端',
				key:'ehendclient',
				width:'140px'
			},{
				title:'考试时间',
				key:'ehstarttime',
				width:'160px'
			},{
				title:'状态',
				customSlot:"resit",
				key:'ehneedresit',
				width:'60px'
			},{
				title:'操作',
				customSlot:"operator",
				width:'150px'
			}],
			logColumns:[{
				title:'ID',
				key:'ehlid',
				width: "60px"
			},{
				title:'操作人',
				key:'ehlusername',
				width:'150px'
			},{
				title:'操作类型',
				key:'ehltype',
				customSlot:'logtype'
			},{
				title:'操作原因',
				key:'ehlinfo'
			},{
				title:'操作时间',
				key:'ehltime',
				width:'160px'
			}],
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			sessionPage:{
				limit:20,
				current:1,
				total:0
			},
			scorePage:{
				limit:20,
				current:1,
				total:0
			},
			logEhId:0,
			logPage:{
				limit:10,
				current:1,
				total:0
			},
			planId:0,
			showFacePage:false,
			showResitPage:false,
			showLogsPage:false,
			showResitPageBtns:[
				{
					text: "提交",
					callback: () => {
						this.$refs['resitPageForm'].validate().then((res) => {
							this.showResitPage = false;
							this.base(async() => {
								await planApi.delExamHistory(this.resit);
							});
						}).catch( res => {
							//
						});
					}
				},
				{
					text: "关闭",
					callback: () => {
						this.showResitPage = false;
					}
				}
			],
			resit:{
				reason:'',
				ehId:0
			}
		}
	},
	async mounted() {
		this.planId = this.$route.params.planid;
	},
	async activated(){
		await this.getData();
	},
	methods:{
		getData:async function(){
			if(this.tabCurrent === '1'){
				await this.getScore();
			}else if(this.tabCurrent === '2'){
				await this.getSession();
			}
		},
		getSession:async function(){
			await this.execute(async() => {
				const data = await planApi.getExamSessionList({
					search:this.search,
					planId:this.planId,
					limit:this.sessionPage.limit,
					page:this.sessionPage.current
				})
				this.sessionPage.current = data.page;
				this.sessionPage.total = data.total;
				this.sessionPage.limit = data.limit;
				this.dataSource = data.data;
			},null,null)
		},
		getScore:async function(){
			await this.execute(async() => {
				const data = await planApi.getExamHistoryList({
					planId:this.planId,
					search:{
                        ...this.searchScore,
                        ehtype:2
                    },
					limit:this.scorePage.limit,
					page:this.scorePage.current
				})
				this.scorePage.current = data.page;
				this.scorePage.total = data.total;
				this.scorePage.limit = data.limit;
				this.scoreSource = data.data;
			},null,null)
		},
		changeSessionPage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		changeScorePage:function({ current, limit }){
			this.scorePage.current = current
			this.scorePage.limit = limit
			this.getScore()
		},
		changeLogPage:function({ current, limit }){
			this.logPage.current = current
			this.logPage.limit = limit
			this.getLogData();
		},
		resitPage:function(ehId){
			this.showResitPage = true;
			this.resit.ehId = ehId;
		},
		showLog:async function(ehId)
		{
			this.logEhId = ehId;
			await this.getLogData();
			this.showLogsPage = true;
		},
		getLogData:async function(){
			await this.execute(async() => {
				const data = await planApi.getExamHistoryLog(this.logEhId);
				this.logSource = data.data;
				this.logPage.current = data.page;
				this.logPage.limit = data.limit;
				this.logPage.total = data.total;
			},null,null);
		},
		openCamera:async function(ehId){
			this.showFacePage = true;
			this.face = await planApi.getExamHistoryFace(ehId);
		},
		readPaper:function(ehId){
			this.$router.push('/desktop/master/plan/view/' + this.planId + '/' + ehId);
		},
	}
}
</script>