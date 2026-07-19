<template>
	<div>
		<lay-card>
			<lay-space direction="vertical">
				<lay-space size="lg">
					<lay-space></lay-space>
					<lay-space>
						<span style='width:70px'> 通行证ID</span><lay-input v-model="search.ehpassport" allow-clear style="width: 180px;"></lay-input>
					</lay-space>
					<lay-space>
						<span style='width:70px'> 考试分数</span>
						<lay-input-number v-model="search.minscore" :max="100" :min="0" :step="10"></lay-input-number> -
						<lay-input-number v-model="search.maxscore" :max="100" :min="0" :step="10"></lay-input-number>
					</lay-space>
                    <lay-space>
                        <span style='width:70px'> 考试类型</span>
                        <lay-select v-model="search.ehtype" :allow-clear="true" placeholder="请选择考试类型">
                            <lay-select-option :value="1" label="模拟考试"></lay-select-option>
                            <lay-select-option :value="2" label="正式考试"></lay-select-option>
                        </lay-select>
                    </lay-space>
					<lay-space>
						<lay-button type="primary" @click="getData">搜索</lay-button>
						<lay-button style="display: none" type="danger" @click="examStats">成绩分析</lay-button>
					</lay-space>
				</lay-space>
				<lay-space size="lg">
					<lay-space></lay-space>
					<lay-space>
						<span style='width:70px'> 考试时间</span>
						<lay-date-picker v-model="search.range" :allow-clear="true" :placeholder="['开始日期','结束日期']" range></lay-date-picker>
					</lay-space>
				</lay-space>
			</lay-space>
		</lay-card>
		<lay-card>
			<lay-table ref="tableRef" :columns="columns" :data-source="dataSource" :default-toolbar="false">
				<template #toolbar>
					{{ basic.basic }} 成绩统计
					<lay-button style="float:right;" type="primary" @click="exportScore">导出成绩</lay-button>
				</template>
                <template v-slot:ehtype="{ row }">
                    <span v-if="row.ehtype === 2">正式考试</span>
                    <span v-else>模拟考试</span>
                </template>
				<template v-slot:ehtime="{ row }">
					{{Math.floor(row.ehtime / 60)}}分{{row.ehtime % 60}}秒
				</template>
				<template #footer>
					<lay-page v-model="page.current" v-model:limit="page.limit" :layout="layout" :total="page.total" style="float:right;" @change="changePage"></lay-page>
				</template>
				<template v-slot:operator="{ row }">
					<lay-button size="xs" type="primary" @click="readPaper(row.ehid)">阅卷</lay-button>
					<lay-button v-if="row.ehneedresit !== 1" size="xs" type="danger" @click="resitPage(row.ehid)">撤销</lay-button>
					<lay-button v-if="row.ehneedresit === 1" size="xs" type="primary" @click="showLog(row.ehid)">日志</lay-button>
					<lay-button v-else size="xs" type="primary" @click="openCamera(row.ehid)">照片</lay-button>
				</template>
			</lay-table>
		</lay-card>
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
	</div>
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
import examApi from '@/framework/api/admin/exam.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {layer} from '@layui/layui-vue';

export default {
	mixins: [baseMixin],
	data() {
		return {
			columns:[{
				title:'ID',
				key:'ehid',
				width:'80px'
			},{
				title:'姓名',
				key:'mname',
				width:'120px'
			},{
				title:'通行证ID',
				key:'ehpassport'
			},{
				title:'分数',
				key:'ehscore',
				width:'80px'
			},{
				title:'考试名称',
				key:'ehexam'
			},{
                title:'类型',
                key:'ehtype',
                customSlot:"ehtype",
                width:'80px'
            },{
				title:'考试时间',
				key:'ehstarttime',
				width:'160px'
			},{
				title:'考试用时',
				key:'ehtime',
				customSlot:"ehtime",
				width:'100px'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"150px"
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
			dataSource:[],
			logSource:[],
			basic:{},
			face:{},
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{
				limit:20,
				current:1,
				total:0
			},
			logPage:{
				limit:20,
				current:1,
				total:0
			},
			search:{},
			basicId:0,
			passport:"",
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
								await examApi.delExamHistory(this.resit);
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
		this.basicId = this.$route.params.basicid;
		this.passport = this.$route.params.passport;
		this.basic = await examApi.getBasic(this.basicId);
		await this.getData()
	},
	components:{},
	methods:{
		getData:async function(){
			await this.execute( async () =>{
				const data = await examApi.getExamHistoryList({
					search:{
						...this.search,
						ehstatus:1
					},
                    basicId:this.basicId,
					limit:this.page.limit,
					page:this.page.current
				})
				this.page.current = data.page;
				this.page.limit = data.limit;
				this.page.total = data.total;
				this.dataSource = data.data;
			},null,null);
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
				const data = await examApi.getExamHistoryLog(this.logEhId);
				this.logSource = data.data;
				this.logPage.current = data.page;
				this.logPage.limit = data.limit;
				this.logPage.total = data.total;
			},null,null);
		},
		openCamera:async function(ehId){
			this.showFacePage = true;
			this.face = await examApi.getExamHistoryFace(ehId);
		},
		readPaper:function(ehId){
			this.$router.push('/desktop/master/exam/view/' + ehId);
		},
		examStats:function(){
			this.$router.push('/desktop/master/exam/stats/' + this.basicid)
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		exportScore:async function(){
			const id = layer.load(0);
			const data = await examApi.exportExamHistory({
				search:this.search,
                basicId:this.basicId,
			})
			const a = document.createElement("a");
			a.download = "data.xlsx";
			// 创建二进制对象
			const blob = new Blob([data]);
			const downloadURL = (window.URL || window.webkitURL).createObjectURL(blob);
			a.href = downloadURL;
			a.click();
			URL.revokeObjectURL(downloadURL);
			layer.close(id);
		},
	}
}
</script>