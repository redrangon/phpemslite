<template>
	<lay-card>
		<lay-table id="ehid" ref="tableRef" :columns="columns" :data-source="dataSource" :default-toolbar="false" even>
			<template #toolbar>
				试卷批改
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="showDecide(row.ehid)">评分</lay-button>
			</template>
            <template v-slot:ehtype="{ row }">
                <span v-if="row.ehtype === 2">正式考试</span>
                <span v-else>模拟考试</span>
            </template>
            <template #footer>
                <lay-page v-model="page.current" v-model:limit="page.limit" :layout="layout" :total="page.total" style="float:right;" @change="changePage"></lay-page>
            </template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showDecidePage" :area="['800px','90vh']" :btn="showDecidePageBtns" :shade="true" title="评分">
		<div style="padding: 20px 20px 20px 20px;">
            <lay-form ref="decidePageForm" :model="examScore" :pane="false" class="form" label-width="40" size="md">
                <template v-for="(question,qid) in questions" :key="qid">
                    <div v-if="questionTypes[question.questiontype].questsort === 1">
                        <lay-field :title="'第' + (qid+1) + '题'">
                            <div v-if="question.parent" class="question" v-html="question.parent"></div>
                            <div class="question" v-html="question.question"></div>
                            <lay-quote>
                                标准答案：
                            </lay-quote>
                            <div class="question" v-html="question.questionanswer"></div>
                            <lay-quote>
                                考生答案：
                            </lay-quote>
                            <div class="question" v-html="userAnswers?.[question.questionid]"></div>
                            <lay-quote>
                                评分：（本题满分：{{maxScores[question.questionid]}}分）
                            </lay-quote>
                            <lay-form-item :prop="question.questionid.toString()" label="" required>
                                <lay-space as="div">
                                    <lay-input-number v-model="examScore[question.questionid.toString()]" :max="maxScores[question.questionid]" :min="0" :placeholder="`本题满分${maxScores[question.questionid]}分,请填写分数`" :step="0.5" size="md" style="height:41px;margin-top: 5px;"></lay-input-number>
                                    <lay-button size="md" type="primary" @click="AiAsnalysis({...question,maxscore:maxScores[question.questionid],answer:userAnswers?.[question.questionid]??null})">AI建议</lay-button>
                                </lay-space>
                            </lay-form-item>
                        </lay-field>
                    </div>
                </template>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped>
.question{
    font-size: 16px;
    line-height: 2;
    padding:10px 0 20px 0;
}
</style>
<script>
import examApi from '@/framework/api/admin/exam.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {layer} from "@layui/layui-vue";

export default {
    mixins:[baseMixin],
	data() {
		return {
            basicId:0,
			basic:{},
			ehId:0,
			examScore:{},
            maxScores:{},
            userAnswers:{},
            layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{
                current: 1,
                limit: 20,
                total: 0
            },
            dataSource:[],
            questions:[],
			questionTypes:{},
			showDecidePage:false,
            columns:[{
                title:'ID',
                key:'ehid',
                width:'80px'
            },{
                title:'试卷名称',
                key:'ehexam'
            },{
                title:'类型',
                key:'ehtype',
                customSlot:"ehtype",
                width:'120px'
            },{
                title:'开考时间',
                key:'ehstarttime',
                width:'180px'
            },{
                title:'耗时（秒）',
                key:'ehtime',
                width:'120px'
            },{
                title:'操作',
                customSlot:"operator",
                key:"operator",
                width:"60px"
            }],
			showDecidePageBtns:[
                {
                    text: "确认",
                    callback: () => {
                        this.$refs['decidePageForm'].validate().then((res) => {
                            this.showDecidePage = false;
                            this.setScore();
                        }).catch( res => {});
                    }
                },
                {
                    text: "取消",
                    callback: () => {
                        this.showDecidePage = false;
                    }
                }
            ]
		}
	},
	async mounted() {
		this.basicId = this.$route.params.basicid;
		await this.getBasic();
		await this.getQuestionTypes();
		await this.getData();
	},
	methods:{
        getQuestionTypes:async function(){
            this.questionTypes = await examApi.getAllQuestionTypes();
        },
		getBasic:async function(){
            this.basic = await examApi.getBasic(this.basicId);
		},
		getData:async function(){
			await this.execute(async () => {
                const data = await examApi.getExamHistoryList({
                    search: {
                        ...this.search,
                        ehstatus:0
                    },
                    limit: this.page.limit,
                    page: this.page.current,
                    basicId: this.basicId
                });
                this.page.current = data.page;
                this.page.limit = data.limit;
                this.page.total = data.total;
                this.dataSource = data.data;
            },null,null);
		},
		showDecide:async function(id){
            await this.execute(async () => {
                const data = await examApi.getExamHistoryDetail(id);
                this.ehid = data.ehid;
                this.questions = Object.values(data.detail.ehdquestion).flat();
                this.maxScores = data.detail.ehdsetting.questionScore??{};
                this.userAnswers = data.detail.ehdanswer??{};
                this.examScore = {};
                this.showDecidePage = true;
            },null,null);
		},
		setScore:async function(){
			await this.base(async () => {
                await examApi.decideHistory({
                    ehid:this.ehid,
                    examScore:this.examScore
                });
            });
		},
        AiAsnalysis:async function(question){
            if(!question.answer)
            {
                layer.confirm('本题未做答，建议分数：0分',{title:'分析结果'});
                return ;
            }
            await this.execute(async () => {
                const data = await examApi.getAiAsnalysis(question);
                layer.confirm(data,{title:'分析结果',area:['500px']});
            });
        },
        changePage:function({ current, limit }){
            this.page.current = current
            this.page.limit = limit
            this.getData()
        },
	}
}
</script>