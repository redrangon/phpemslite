<template>
	<lay-card class="pagecontent">
		<lay-card v-if="history" style="position: relative;">
			<lay-page-header :content="basic.basic" class="planbread" @back="$router.go(-1)"></lay-page-header>
			<lay-tab v-model="tabCurrent" :activeBarTransition="true" type="brief">
				<lay-tab-item id="1">
					<template #title>
						<span class="tabtitle">成绩单</span>
					</template>
                    <template v-if="history.ehstatus === 1">
                        <lay-container>
                            <lay-row>
                                <div style="width: 100%;padding: 10px;position: relative;">
                                    <h1 style="line-height: 3">{{history.ehexam}}</h1>
                                    <div v-if="history.ehispass === 1"><h2 style="color:#16baaa;font-weight: bold;"><lay-icon size="22px" type="layui-icon-face-smile"></lay-icon> 恭喜您通过考试！</h2></div>
                                    <div v-else><h2 style="color:#FF5722;font-weight: bold;"><lay-icon size="22px" type="layui-icon-face-cry"></lay-icon> 很遗憾，您没有通过考试！</h2></div>
                                    <div style="margin-top:20px;color:#999999;clear: both;">
                                        <lay-space>
                                            <lay-space>总分：{{ history.ehstats?.setting?.totalScore }}分</lay-space>
                                            <lay-space>合格分数线：{{ history.ehstats?.setting?.passMark }}分</lay-space>
                                            <lay-space>答卷耗时：{{ timeFormat(history.ehtime) }}</lay-space>
                                        </lay-space>
                                    </div>
                                    <div class="paperscore">{{ Number(history.ehscore).toFixed(0) }}</div>
                                </div>
                            </lay-row>
                        </lay-container>
                        <lay-container>
                            <h2 style="line-height: 3;font-size: 18px;font-weight: 500;margin-top:20px;">试卷分析</h2>
                            <table class="table">
                                <thead>
                                <th>题型</th>
                                <th>总题数</th>
                                <th>答对题数</th>
                                <th>总分</th>
                                <th>得分</th>
                                </thead>
                                <template v-for="(num,nid) in history.ehstats?.questionTypeAnalysis?.totalTypeNumber" :key="nid">
                                    <tr v-if="num > 0">
                                        <td>{{ questionTypes[nid]?.questype }}</td>
                                        <td>{{ num }}</td>
                                        <td>{{ history.ehstats?.questionTypeAnalysis?.rightTypeNumber[nid] }}</td>
                                        <td>{{ history.ehstats?.questionTypeAnalysis?.totalTypeScore[nid]}}</td>
                                        <td>{{ history.ehstats?.questionTypeAnalysis?.rightTypeScore[nid].toFixed(1) }}</td>
                                    </tr>
                                </template>
                            </table>
                            <table class="table">
                                <thead>
                                <th>知识点</th>
                                <th>总题数</th>
                                <th>答对题数</th>
                                <th>正确率</th>
                                </thead>
                                <template v-for="(stat,sid) in history.ehstats?.pointAnalysis?.totalPointNumber" :key="sid">
                                    <tr v-if="stat > 0">
                                        <td>{{ history.points[sid] }}</td>
                                        <td>{{ stat }}</td>
                                        <td>{{ history.ehstats?.pointAnalysis?.rightPointNumber[sid]??0 }}</td>
                                        <td>{{ (100 * (history.ehstats?.pointAnalysis?.rightPointNumber[sid]??0) / stat).toFixed(1) }}%</td>
                                    </tr>
                                </template>
                            </table>
                        </lay-container>
                    </template>
                    <template v-else>
                        <lay-container>
                            <div style="width: 100%;padding: 10px;">
                                <h1 style="line-height: 2.5">{{history.ehexam}}</h1>
                                <div style="margin:10px 0 20px 0;color:#999999;clear: both;">
                                    <lay-space>
                                        <lay-space>总分：{{ history.ehstats?.setting?.totalScore }}分</lay-space>
                                        <lay-space>合格分数线：{{ history.ehstats?.setting?.passMark }}分</lay-space>
                                        <lay-space>答卷耗时：{{ timeFormat(history.ehtime) }}</lay-space>
                                    </lay-space>
                                </div>
                                <lay-quote>您已成功交卷，因本试卷包含主观题，请等待教师评卷后显示分数！</lay-quote>
                            </div>
                        </lay-container>
                    </template>
				</lay-tab-item>
			</lay-tab>
		</lay-card>		
	</lay-card>
</template>
<script>
import {layer} from '@layui/layui-vue';
import examApi from "@/framework/api/exam.js";
import {useAuthStore} from "@/stores/auth.js";
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
	data() {
		return {
			tabCurrent:"1",
			ehid:0,
			history:[],
			setting:{},
			number:{},
			questionTypes:{},
			stats:{},
			right:{},
			score:{},
			basic:{}
		}
	},
	setup() {
		const authStore = useAuthStore();
		return {authStore};
	},
	async mounted(){
		this.ehid = this.$route.params.ehid;
		this.basic = this.authStore.basic;
		await Promise.all([
			this.getData(),
			this.getQuestionTypes(),
		]);
	},
	methods:{
		getData:async function(){
			await this.execute( async () => {
				this.history = await examApi.getHistoryStats(this.ehid);
			},null,null)
		},
		getQuestionTypes:async function(){
			this.questionTypes = await examApi.getQuestionTypes();
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
		}
	}
}
</script>
<style scoped>
.paperscore{
	line-height:100px;
	text-align: center;
	font-size:120px;
	font-weight: bold;
	margin:10px 20px;
	font-family: 'Mistral';
	position: absolute;
	top:10px;
	right:10px;
	color:#FF0000;
	letter-spacing: 5px;
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