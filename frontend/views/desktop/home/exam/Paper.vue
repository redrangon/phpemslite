<template>
	<div>
		<lay-header>
			<div class="topbar" style="width: 100%;">
				<lay-row class="topheader">
					<lay-col md="12">
						<div class="logo">{{ session.title }}</div>
					</lay-col>
					<lay-col md="12" style="text-align:right">
						<lay-menu>
							<lay-menu-item>
								<lay-icon size="22px" type="layui-icon-time"></lay-icon>
								<span style="font-size: 21px;margin-left: 10px;letter-spacing: 2px;">{{timeString}}</span>
							</lay-menu-item>
							<lay-menu-item>
								<lay-button size="lg" type="danger" @click="submitPaperLayer()">
									<span style="color: #FFFFFF;font-size: 16px;font-weight: 400;letter-spacing: 4px;">
										<lay-icon size="18px" style="font-weight: 600;" type="layui-icon-release"></lay-icon>&nbsp;交卷
									</span>
								</lay-button>
							</lay-menu-item>
						</lay-menu>
					</lay-col>
				</lay-row>
			</div>
		</lay-header>
		<lay-layout v-if="isready" class="">
            <lay-side class="mySide" style="height: calc(100vh - 92px);" width="320">
				<div class="mylogo">
					<lay-avatar :autoFixSize="true" :src="user?user.userphoto:''" style="width:120px;height:168px;margin-top: 20px;"></lay-avatar>
					<h4 class="title">{{user?user.username:''}}</h4>
					<h4 class="title">{{user?user.userpassport:''}}</h4>
				</div>
				<div v-if="showType === 1" style="background: #FFFFFF;padding:15px 10px;border-radius: 5px;">
					<h4 class="cardTitle">答题卡</h4>
					<lay-space size="15px" wrap>
						<lay-space v-for="(question,qid) in questions" v:key="qid">
							<lay-button v-if="signs[question.questionid] && signs[question.questionid] !== ''" class="qindex" type="danger" @click="goAnchor(qid)">{{ Number(qid) + 1 }}</lay-button>
							<lay-button v-else-if="useranswer[question.questionid] && useranswer[question.questionid] !== ''" class="qindex" type="normal" @click="goAnchor(qid)">{{ Number(qid) + 1 }}</lay-button>
							<lay-button v-else class="qindex" v:key="qid" @click="goAnchor(qid)">{{ Number(qid) + 1 }}</lay-button>
						</lay-space>
					</lay-space>
				</div>
				<lay-collapse v-else v-model="openKeys" accordion class="collapse">
					<template v-for="(questid,index) in setting.questionTypeSort">
						<lay-collapse-item v-if="questions?.[questid]" :id="questid.toString()">
							<template #title>
								<span style="font-size:16px;font-weight:400;line-height:3;height:48px;display: block;">{{ questionTypes[questid].questype }}</span>
							</template>
							<lay-space size="15px" wrap>
								<lay-space v-for="(question,qid) in questions[questid]" v:key="qid">
									<lay-button v-if="signs[question.questionid] && signs[question.questionid] !== ''" class="qindex" type="danger" @click="goAnchor('#tag'+question.questionid)">{{ qid + 1 }}</lay-button>
									<lay-button v-else-if="useranswer[question.questionid] && useranswer[question.questionid] !== ''" class="qindex" type="normal" @click="goAnchor('#tag'+question.questionid)">{{ qid + 1 }}</lay-button>
									<lay-button v-else class="qindex" v:key="qid" @click="goAnchor('#tag'+question.questionid)">{{ qid + 1 }}</lay-button>
								</lay-space>
							</lay-space>
						</lay-collapse-item>
					</template>
				</lay-collapse>
			</lay-side>
			<lay-body class="myLayBody">			
				<lay-layout class="paperright" style="padding-right:0;height:calc(100vh - 82px)">
					<div class="lay-watermark-box">
						<lay-watermark v-if="setting.switch" :color="setting.color" :content="user.usertruename + ' ' + user.userpassport" :fontSize="setting.fontsize + 'px'" :rotate="setting.rotate" element-box=".lay-watermark-box"></lay-watermark>
						<div v-if="showType === 1" class="question">
							<swiper :initialSlide="currentId" :slides-per-view="1" :space-between="0" @slideChange="onSlideChange">
								<swiper-slide v-for="(question,qid) in questions" :key="qid">
									<div class="questionArea">
										<div class="title">
											<lay-space size="md" style="padding:0 15px;">
												<div style="font-weight: bold;">第{{Number(qid)+1}}题 【 {{questionTypes[question.questiontype].questype}} 】</div>
											</lay-space>
											<lay-space style="float:right;padding-right:10px;">
												<lay-button v-if="signs[question.questionid]" type="danger" @click="signQuestion(question.questionid)">取消标记</lay-button>
												<lay-button v-else type="default" @click="signQuestion(question.questionid)">添加标记</lay-button>
											</lay-space>
										</div>
										<question v-model:userAnswer="useranswer[question.questionid]" :childIndex="0" :index="qid" :question="question" :questionType="questionTypes[question.questiontype]" @saveAnswer="saveAnswer"></question>
									</div>
								</swiper-slide>
							</swiper>
							<div class="questionTool">
								<lay-button :disabled="currentId <= 0" type="normal" @click="goAnchor(currentId - 1)">上一题</lay-button>
								<lay-button :disabled="currentId >= questions.length - 1" type="normal" @click="goAnchor(currentId + 1)">下一题</lay-button>
							</div>
						</div>
						<template v-else>
							<lay-backtop></lay-backtop>
							<template v-for="(questid,index) in setting.questionTypeSort">
								<div v-if="questions?.[questid]" :key="questid" class="question">
									<h2 class="title">{{ questionTypes[questid].questype}}</h2>
									<div v-for="(question,qid) in questions[questid]" :id="`tag${question.questionid}`" :key="qid" style="padding:10px 15px;">
										<div class="title">
											<lay-space size="md" style="padding:0 15px;">
												<div style="font-weight: bold;">第{{qid+1}}题 【 {{questionTypes[question.questiontype].questype}} 】</div>
											</lay-space>
											<lay-space style="float:right;padding-right:10px;">
												<lay-button v-if="signs[question.questionid]" type="danger" @click="signQuestion(question.questionid)">取消标记</lay-button>
												<lay-button v-else type="default" @click="signQuestion(question.questionid)">添加标记</lay-button>
											</lay-space>
										</div>
										<question v-model:userAnswer="useranswer[question.questionid]" :childIndex="question.childindex" :index="qid" :parentquestype="questionTypes[questid]" :question="question" :questionType="questionTypes[question.questiontype]" @saveAnswer="saveAnswer"></question>
									</div>
								</div>
							</template>
						</template>
					</div>
					<lay-layer v-model="showFace" :area="['500px']" :close-btn="false" :shade="true" :shadeClose="false" shadeOpacity="0.6" title="实人确认">
						<myCamera :faceverify="faceVerify" style="width:494px;"></myCamera>
					</lay-layer>
				</lay-layout>
			</lay-body>
		</lay-layout>
	</div>
</template>
<script>
import baseMixin from "@/framework/mixins/baseMixin.js";
import examApi from '@/framework/api/exam.js';
import myCamera from '@/components/desktop/Camera.vue';
import storage from '@/framework/utils/storage.js';
import clock from '@/framework/utils/clock.js';
import {layer} from '@layui/layui-vue';
import {computed, ref} from 'vue';
import { useAuthStore} from '@/stores/auth.js';
import question from '@/components/desktop/ExamQuestion.vue';
import {Swiper, SwiperSlide} from 'swiper/vue';
import 'swiper/css';

export default {
	setup(){
		const authStore = useAuthStore();
		const user = computed(() => authStore.userInfo);
		return {user}
	},
	mixins: [baseMixin],
	data() {
		return {
			isready:false,
			splitTime:40,
			sessionid:'',
			questions:[],
			useranswer: {},
			signs:{},
			setting:{},
			session:{},
			questionTypes:{},
			openKeys:[],
			leftTime:0,
			clock:null,
			time:0,
			timeString:'',
			basicExam:{},
			leaveNumber:0,
			currentId:0,
			lastSaveTime:0,
			showType:0,
			showFace:false,
			storage:null
		}
	},
	components:{question,Swiper,SwiperSlide,myCamera},
	async mounted(){
		this.sessionid = this.$route.params.sessionid;
		this.storage = new storage(this.sessionid);
		await Promise.all([
			this.getData(),
			this.getQuestionTypes(),
			this.getLeftTime()
		]);
		if(this.storage.question)
		{
			this.useranswer = this.storage.question??{};
		}
		else
		{
			this.storage.syncData(this.useranswer);
		}
		this.isready = true;
		const options = {
			time:this.time,
			leftTime:0,
			show:(h,m,s) => {
				this.timeString = h + ':' + m + ':' + s;
				this.saveAnswer();
			},
			finish:() => {
				this.submitPaper();
			}
		}
		this.clock = new clock(options);
		this.clock.refresh(this.leftTime);
		if(this.basicExam.screenSwitchNumber && this.basicExam.screenSwitchNumber > 0){
			window.addEventListener('blur',this.checkScreen);
		}
	},
	beforeRouteLeave:function(){
		this.clock?this.clock.clear():'';
		window.removeEventListener('blur',this.checkScreen);
	},
	methods:{
		getQuestionTypes:async function(){
			this.questionTypes = await examApi.getQuestionTypes();
		},
		getLeftTime:async function(){
			const data = await examApi.getExamSessionLeftTime(this.sessionid);
			this.leftTime = data.time;
		},
		getData:async function(){
			try{
				const data = await examApi.getExamSession(this.sessionid);
				this.basicExam = data.basicexam;
				this.setting = data.examsessionsetting;
				this.useranswer = data.examsessionuseranswer??{};
				this.session = {
					title:data.examsession
				};
				this.time = data.examsessionsetting.totalTime * 60;
				this.signs = data.examsessionsign??{};
				this.showType = Number(data.basicexam?.template);
				this.openKeys = Object.keys(data.examsessionquestion);
				if(this.showType === 1){
					this.questions = Object.values(data.examsessionquestion).flat()
				}
				else
				{
					this.questions = data.examsessionquestion;
				}
			}catch (e) {
				layer.msg(e.message)
				this.$router.go(-1);
			}
		},
		faceVerify:async function(face){
			await this.execute( async () => {
				await examApi.verifyExamFace({
					sessionid:this.sessionid,
					face
				});
				this.showFace = false;
			},null,null);
		},
		submitPaperLayer:function(){
			layer.confirm("您确定要交卷吗？", {
				title:'交卷确认',
				btn: [
					{
						text:'确定', 
						callback: (layerid) => { 
							layer.close(layerid);
							this.submitPaper();	
						}
					},
					{
						text:'取消', 
						callback: (layerid) => { 
							layer.close(layerid); 
						}
					}
				]
			});
		},
		submitPaper:async function(){
			await this.execute( async () => {
				const data = await examApi.finishExamSession({
					sessionId:this.sessionid,
					userAnswer:this.useranswer
				});
				this.$router.replace('/desktop/home/exam/result/' + data.ehid);
			},null,null)
		},
		saveAnswer:async function(){
			this.lastSaveTime++;
			if(this.lastSaveTime >= this.splitTime)
			{
				this.lastSaveTime = 0;
				this.storage.syncData(this.useranswer);
				const data = await examApi.saveExamSessionAnswer({
					sessionId:this.sessionid,
					userAnswer:this.useranswer,
					signs:this.signs,
				});
				if(data?.faceVerify === 1)
				{
					this.showFace = true;
				}
			}
		},
		goAnchor:function(selector){
			if(this.showType === 1){
				this.currentId = Number(selector);
				const swiper = document.querySelector('.swiper').swiper;
				swiper.activeIndex = this.currentId - 1
				swiper.slideNext();
			}else{
				document.querySelector(selector).scrollIntoView({
					behavior: "smooth",
					block: "start"
				});
			}
		},
		signQuestion:function(qid){
			if(this.signs[qid]){
				this.signs[qid] = '';
			}else{
				this.signs[qid] = qid;
			}
		},
		checkScreen:function(){
			if(this.leavenumber >= this.basicExam.screenSwitchNumber){
				this.submitPaper();	
			}else{
				this.leavenumber ++;
				layer.confirm('您已切屏'+ this.leavenumber +'次，达到'+ this.basicExam.screenSwitchNumber +'次，将自动交卷')						
			}
		},
		onSlideChange:function(Change){
			this.currentId = Change.activeIndex
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
	height: calc(100vh - 102px);
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
}
.question .title{
	overflow: hidden;
	font-size: 16px;
	line-height: 2.25;
	background: #F5F5F5;
	border-radius: 5px;
	padding: 10px 15px;
	margin-bottom: 15px;
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
</style>