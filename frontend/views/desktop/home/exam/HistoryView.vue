<template>
	<div>
		<lay-header>
			<div class="topbar" style="width: 100%;">
				<div class="paperscore">{{ Number(score).toFixed(0) }}</div>
				<lay-row class="topheader">
					<lay-col md="12">
						<div class="logo">{{ title}}</div>
					</lay-col>
					<lay-col md="12" style="text-align:right">
						<lay-menu>
							<lay-menu-item>
								<lay-button size="lg" type="primary" @click="$router.go(-1)">
									<span style="color: #FFFFFF;font-size: 16px;font-weight: 400;letter-spacing: 4px;">
										<lay-icon size="18px" style="font-weight: 600;" type="layui-icon-release"></lay-icon>&nbsp;返回
									</span>
								</lay-button>
							</lay-menu-item>
						</lay-menu>
					</lay-col>
				</lay-row>
			</div>
		</lay-header>
		<lay-layout>
			<lay-side class="mySide" style="height: calc(100vh - 92px);padding-top: 10px;">
				<lay-collapse v-model="openKeys" accordion class="collapse">
					<template v-for="(questid,index) in setting.questionTypeSort">
						<lay-collapse-item v-if="questions?.[questid]" :id="questid.toString()">
							<template #title>
								<span style="font-size:16px;font-weight:400;line-height:3;height:48px;display: block;">{{ questionTypes[questid]?.questype }}</span>
							</template>
							<lay-space size="15px" wrap>
								<lay-space v-for="(question,qid) in questions[questid]" v:key="qid">
									<lay-button v-if="scores[question.questionid]" class="qindex" type="normal" @click="goAnchor('#tag'+question.questionid)">{{ qid + 1 }}</lay-button>
									<lay-button v-else class="qindex" type="danger" v:key="qid" @click="goAnchor('#tag'+question.questionid)">{{ qid + 1 }}</lay-button>
								</lay-space>
							</lay-space>
						</lay-collapse-item>
					</template>
				</lay-collapse>
			</lay-side>
			<lay-body class="myLayBody">
				<lay-layout>
                    <div class="paperright">
                        <lay-backtop></lay-backtop>
                        <template v-for="(questid,index) in setting.questionTypeSort">
                            <div v-if="questions?.[questid]" :key="questid" class="question">
                                <h2 class="type-title">{{ questionTypes[questid]?.questype}}</h2>
                                <div v-for="(question,qid) in questions[questid]" :id="'tag'+question.questionid" :key="qid" style="width: 100%;position: relative;">
                                    <div style="position: absolute;right:50px;bottom:20px;">
                                        <lay-icon v-if="question.questionanswer === userAnswers[question.questionid]" color="#16baaa" size="80px" type="layui-icon-ok-circle"></lay-icon>
                                        <lay-icon v-else color="#FF5722" size="80px" type="layui-icon-error"></lay-icon>
                                    </div>
                                    <div style="position: relative;">
                                        <h3 class="title">第{{qid+1}}题 【 {{questionTypes[question.questiontype]?.questype}} 】</h3>
                                        <span style="position:absolute;right:20px;top:3px;">
                                            <lay-button :type="favors[question.questionid]?'primary':'default'" @click="onFavor(question.questionid)">
                                                <lay-icon size="18px" style="font-weight: 600;" type="layui-icon-rate"></lay-icon>
                                                {{favors[question.questionid]?'取消收藏':'收藏'}}
                                            </lay-button>
                                            <lay-button :type="scores[question.questionid] > 0?'normal':'danger'">
                                                本题得分：{{scores[question.questionid]}}分
                                            </lay-button>
                                        </span>
                                    </div>
                                    <lay-card style="box-shadow: none;">
                                        <question :childIndex="question.childindex" :disabled="true" :index="qid+1" :question="question" :questionType="questionTypes[question.questiontype]" :userAnswer="userAnswers?.[question.questionid]" childindex="0"></question>
                                    </lay-card>
                                    <lay-quote>我的答案：</lay-quote>
                                    <div class="question box" v-html="userAnswers[question.questionid]"></div>
                                    <lay-quote>正确答案：</lay-quote>
                                    <div class="question box" v-html="question.questionanswer"></div>
                                    <lay-quote>试题解析：</lay-quote>
                                    <div class="question box" v-html="question.questiondescribe??'暂无解析'"></div>
                                </div>
                            </div>
                        </template>
                    </div>
				</lay-layout>
			</lay-body>
		</lay-layout>
	</div>
</template>
<script>
import examApi from '@/framework/api/exam.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {useAuthStore} from "@/stores/auth.js";
import question from '@/components/master/Question.vue';
import {Swiper, SwiperSlide} from "swiper/vue";
export default {
	mixins:[baseMixin],
	setup() {
		const authStore = useAuthStore();
		return {authStore};
	},
	data() {
		return {
			setting:{},
			questions:{},
			questionTypes: {},
			userAnswers:{},
			scores:{},
			ehId:0,
			favors:{},
			openKeys:"1",
			title:'',
			score:0
		}
	},
	components:{question},
	async mounted(){
		this.ehId = this.$route.params.ehid;
		this.basic = this.authStore.basic;
		if(this.basic.basicexam.model === 2)this.$router.replace('/desktop/home/exam/exam');
		else
		{
			await this.getQuestionTypes();
			await this.getData();
		}
	},
	methods:{
		getQuestionTypes:async function(){
			this.questionTypes = await examApi.getQuestionTypes();
		},
		getData:async function(){
			await this.execute(async () => {
				const data = await examApi.getHistoryDetail(this.ehId);
				this.setting = data.detail.ehdsetting;
				this.userAnswers = data.detail.ehdanswer??{};
				this.scores = data.detail.ehdscores??{};
				this.openKeys = Object.keys(data.detail.ehdquestion);
				this.title = data.ehexam;
				this.questions = data.detail.ehdquestion;
				this.score = data.ehscore;
				this.favors = data.favors;
			},null,null);
		},
		goAnchor:function(selector){
			document.querySelector(selector).scrollIntoView({
				behavior: "smooth",
				block: "start"
			});
		},
		onFavor:async function(questionId){
			await this.execute(async () => {
				if(this.favors[questionId]){
					await examApi.unFavorQuestion(questionId);
					this.favors[questionId] = false;
				}else{
					await examApi.favorQuestion(questionId);
					this.favors[questionId] = true;
				}
			},null,null);
		}
	}
}
</script>
<style scoped>
.layui-header{
	background:#117bcb;
	height: 72px;
	min-width: 1200px;
}
.topheader{
	width: 100%;
	margin: auto;
}
.logo{
	line-height: 72px;
	font-size: 24px;
	color: #bde4ff;
	text-indent: 20px;
}
.layui-nav{
	margin-top: 6px;
}
.layui-header .layui-nav{
	background: transparent;
}
.mySide{
	align-items: unset;
	flex: 0 0 320px!important;
	width: 320px!important;
	margin-right: 15px;
	flex-direction:column;
}
.mylogo{
	text-align: center;
	margin-bottom: 10px;
	margin-top: 10px;
	border-radius: 5px;
	padding:10px;
	background: #FFFFFF;
}
.mylogo .title{
	font-size: 16px;
	line-height: 3;
}
.myLayOut{
	min-height: 90vh;
}
::v-deep(.layui-colla-title){
	font-size: 16px;
	height: 48px;
	line-height: 48px;
}
.paperright{
	border-radius: 5px;
	overflow-y: auto;
	overflow-x: hidden;
	background-color: #FFFFFF;
	margin-top: 10px;
	padding:25px;
	height: calc(100vh - 82px);
}
.collapse{
	border-radius: 10px;
}
.qindex{
	width: 35px;
	height:35px;
	line-height:35px;
	padding:0px;
}
.topbar{
	height:40px;
	line-height: 40px;
	width: 1200px;
	margin:0 auto;
}
.lay-watermark-box{
	position: relative;
}
.question{
	font-size: 16px;
	line-height: 3!important;
	width: 100%;
}
.question.box{
    border:1px solid #F5F5F5;
    margin-bottom: 15px;
    padding:15px;
}
.question .type-title{
    border-left: 10px solid #117bcb;
}
.question .title,.question .type-title{
	overflow: hidden;
	font-size: 16px;
	line-height: 2.25;
	background: #F5F5F5;
	border-radius: 5px;
	padding: 10px 15px;
	margin-bottom: 15px;
	font-weight: 600;
}
.cardTitle{
	line-height: 2.5;
	font-size: 16px;
	text-indent: 10px;
	border-bottom:1px solid #F5F5F5;
	margin-bottom: 15px;
}
.questionArea{
	max-height: calc(100vh - 140px);
	overflow-x: hidden;
	overflow-y:auto;
	padding-bottom:120px;
	padding-right:30px;
}
.questionTool{
	position: fixed;
	width: calc(100% - 320px);
	text-align: center;
	bottom: 80px;
	right:0;
	z-index:99
}
.paperscore{
	font-size:140px;
	font-weight: bold;
	font-family: 'Mistral';
	position: absolute;
	top:60px;
	left:360px;
	color:#FF0000;
	letter-spacing: 8px;
	z-index:99;
	transform: rotate(-5deg);
}
</style>