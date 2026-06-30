<template>
	<lay-card class="pagecontent">
		<lay-card style="position: relative;">
			<lay-page-header :content="basic.basic" class="planbread" @back="$router.go(-1)"></lay-page-header>
			<lay-tab v-model="tabCurrent" activeBarTransition type="brief">
				<lay-tab-item id="1">
					<template #title>						
						<span class="tabtitle">课后练习</span>
					</template>
					<lay-container>
						<div class="title">
							第{{page.current}}题 【{{questionType.questype}}】
							<lay-space style="float:right">
								<lay-button :type="question.isfavor?'primary':'default'" @click="onFavor(question.questionid)">
									<lay-icon size="18px" style="font-weight: 600;" type="layui-icon-rate"></lay-icon>
									{{question.isfavor?'取消收藏':'收藏'}}
								</lay-button>
								<lay-button type="default" @click="reportError(question.questionid)">
									<lay-icon size="18px" style="font-weight: 600;" type="layui-icon-upload-drag"></lay-icon>
									错题反馈
								</lay-button>
							</lay-space>
						</div>							
						<lay-space v-if="question && question.questionid" class="question" direction="vertical" fill wrap>
							<question v-model:userAnswer="userAnswer" :childIndex="0" :index="page.current - 1" :question="question" :questionType="questionType" @saveAnswer="saveAnswer"></question>
							<template v-if="questionType.questsort === 1 && !isshow">
								<lay-button type="primary" @click="singleChange">查看答案</lay-button>
							</template>
							<template v-else-if="(questionType.questchoice === 2 || questionType.questchoice === 3)  && !isshow">
								<lay-button type="primary" @click="singleChange">选择完毕</lay-button>
							</template>
							<template v-else-if="questionType.questchoice === 5 && !isshow" class="selector">
								<lay-button type="primary">作答完毕</lay-button>
							</template>
							<div v-if="isshow && userTrueAnswer">
								<template v-if="questionType.questsort === 1">
									<div v-html="question.questionanswer"></div>
								</template>
								<template v-else>
									<div v-if="userTrueAnswer === question.questionanswer" style="background-color: #16b777;padding:10px;color:whitesmoke;border-radius:2px;">
										恭喜您回答正确！
									</div>
									<div v-else style="background-color: #FF5722;padding:10px;color:whitesmoke;">
										回答错误，正确答案是{{question.questionanswer}}，您选择了{{userTrueAnswer}}
									</div>
								</template>
								<lay-field v-if="question.questiondescribe" style="margin-top:20px;" title="试题解析">
									<template #title>
										<span class="legend">试题解析</span>
									</template>
									<div v-html="question.questiondescribe"></div>
								</lay-field>
							</div>
						</lay-space>
						<lay-space v-else>
							没有试题
						</lay-space>
						<lay-page v-if="page.total > page.limit && page.current > 0" v-model="page.current" :layout="layout" :limit="page.limit" :total="page.total" style="float:right;margin-top:20px;" theme="blue" @change="changePage">
							<template #prev>上一题</template>
							<template #next>下一题</template>
						</lay-page>
					</lay-container>
				</lay-tab-item>
			</lay-tab>
		</lay-card>
		<lay-layer v-model="showErrorPage" :area="['600px']" :btn="errorPageBtns" title="错题反馈">
			<lay-card>
				<lay-form ref="errorPageFrom" :model="model" :pane="false" class="form" labelWidth="100" size="md">
					<lay-form-item label="错误类型" prop="fbtype" required>
						<lay-select v-model="model.fbtype" placeholder="请选择错误类型">
							<lay-select-option label="答案错误" value="答案错误"></lay-select-option>
							<lay-select-option label="题干错误" value="题干错误"></lay-select-option>
							<lay-select-option label="知识点归类错误" value="知识点归类错误"></lay-select-option>
							<lay-select-option label="图片错误" value="图片错误"></lay-select-option>
							<lay-select-option label="解析错误" value="解析错误"></lay-select-option>
							<lay-select-option label="其他" value="其他"></lay-select-option>
						</lay-select>
					</lay-form-item>
					<lay-form-item label="错误描述" prop="fbcontent" required>
						<lay-textarea v-model="model.fbcontent"></lay-textarea>
					</lay-form-item>
				</lay-form>
			</lay-card>
		</lay-layer>
	</lay-card>
</template>
<script>
import examApi from '@/framework/api/exam.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import question from '@/components/desktop/Question.vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {useAuthStore} from "@/stores/auth.js";
export default {
	setup() {
		const basic = ref({});
		basic.value = useAuthStore().basic;
		return {basic}
	},
	mixins: [baseMixin],
	data() {
		return {
			page:{current:1,limit:1,total:0},
			layout:['count', 'prev', 'next', 'refresh', 'skip'],
			question: {},
			questionTypes:{},
			questionType:{},
			pointid:0,
			userAnswer:null,
			point:{},
			isshow:false,
			selectors:['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'],
			single:false,
			showErrorPage:false,
			tabCurrent:"1",
			model:{},
			errorPageBtns:[
				{
					text: "确认",
					callback: () => {
						this.$refs['errorPageFrom'].validate().then((res) => {
							this.showErrorPage = false;
							this.addError();
						}).catch( res => {
							//
						});
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showErrorPage = false;
					}
				}
			]
		}
	},
	async mounted(){
		this.pointid = this.$route.params.pointid;
		this.questionTypes = await examApi.getQuestionTypes();
		const data = await examApi.getExerciseProgress(this.pointid);
		this.page.current = data.number;
		await this.getData();
	},
	components:{
		question
	},
	computed:{
		userTrueAnswer:function(){
			if(this.userAnswer === null){
				return null;
			}
			else if(typeof this.userAnswer === 'string')
			{
				return this.userAnswer;
			}
			else
			{
				const m = [];
				for(let x in this.userAnswer)
				{
					if(this.userAnswer[x])
					{
						m.push(this.selectors[x]);
					}
				}
				return m.join('');
			}			
		}
	},
	methods:{
		getData:async function(){
			await this.execute( async () => {
				const data = await examApi.getExerciseQuestion({
					page:this.page.current,
					pointid:this.pointid,
					limit:1
				});
				this.question = data.question;
				this.page.current = data.page;
				this.page.total = data.total;
				this.questionType = this.questionTypes[this.question.questiontype];
				this.isshow = false;
			},null,null);
		},
		changePage:async function({current,limit}){
			this.page.current = current;
			this.page.limit = limit;
			this.userAnswer = null;
			this.isshow = false;
			await this.getData();
		},
		singleChange:function(){
			if(this.userAnswer && this.userAnswer !== '')this.isshow = true;
		},
		saveAnswer:function(){
			if(this.questionType.questsort !== 1 && (this.questionType.questchoice === 1 || this.questionType.questchoice === 4))
			{
				this.singleChange();
			}
		},
		onFavor:async function(questionId){
			if(this.question.isfavor){
				await examApi.unFavorQuestion(questionId);
				this.question.isfavor = false;
			}else{
				await examApi.favorQuestion(questionId);
				this.question.isfavor = true;
			}
		},
		reportError:function(qid){
			this.model.fbquestionid = qid;
			this.showErrorPage = true;
		},
		addError:async function(){
			await this.execute(async () => {
				await examApi.addFeedBack(this.model);
				layer.msg('添加成功')
			},null,null)
		}
	}
}
</script>
<style scoped>
.title{
	overflow: hidden;
	font-size: 16px;
	line-height: 2.25;
	background: #F5F5F5;
	border-radius: 5px;
	padding: 10px 15px;
}
.selector,.selector .layui-checkcard{
	font-size: 16px;
	width: 100%;
}
.question{
	font-size: 16px;
	line-height: 2;
	padding:10px 5px;
}
.layui-checkcard-desc{
	line-height: 2;
}
.layui-checkcard-checked{
	background-color: rgb(22, 186, 170);
}
.layui-checkcard-checked .layui-checkcard-right .layui-checkcard-desc{
	color:#FFFFFF!important;
}
</style>