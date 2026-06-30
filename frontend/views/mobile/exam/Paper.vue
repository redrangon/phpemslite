<template>
	<div style="width:100%;">
		<div v-if="isReady">
            <!-- 导航栏 -->
			<van-nav-bar :title="session.title" fixed left-arrow placeholder @click-left="$router.go(-1)">
                <template #right>
                    <span style="color: red;font-size: 16px;font-weight: 600;">{{timeString}}</span>
                </template>
            </van-nav-bar>
	        <div class="card-container" style="padding:0;">
	            <template v-if="showFace">
	                <div style="margin-top: 60px;"></div>
	                <van-cell-group inset>
	                    <van-cell title="真人校验" title-style="text-align:center"></van-cell>
	                    <van-cell>
	                        <myCamera :faceverify="faceVerify"></myCamera>
	                    </van-cell>
	                    <van-cell title="请人脸识别校验后继续考试" title-style="text-align:center"></van-cell>
	                </van-cell-group>
	            </template>
	            <template v-else>
	                <swiper :initialSlide="currentId" :modules="[Virtual]" :slides-per-view="1" :space-between="50" virtual @slideChange="onSlideChange" @swiper="onSwiper">
	                    <swiper-slide v-for="(question,qid) in questions" :key="qid" :virtualIndex="qid" style="overflow-y: auto;">
	                        <div style="width:100%;height:calc(100vh - 135px);overflow-y: auto;">
		                        <question :key="question.questionid" v-model:userAnswer="userAnswer[question.questionid]" :index="qid" :question="question" :questionType="questionTypes[question.questiontype]" @saveAnswer="saveAnswer"></question>
	                        </div>
	                    </swiper-slide>
	                </swiper>
	            </template>
	        </div>
			<van-popup v-model:show="showPopup" closeable destroy-on-close position="bottom" round>
				<div style="padding: 20px;margin-top: 20px;">
					<van-space v-for="(question,qid) in questions" v:key="qid">
						<van-button v-if="signs[qid] && signs[qid] !== ''" class="qindex" type="danger" @click="toCard(qid)">{{ Number(qid) + 1 }}</van-button>
						<van-button v-else-if="userAnswer[question.questionid] && userAnswer[question.questionid] !== ''" class="qindex" type="primary" @click="toCard(qid)">{{ Number(qid) + 1 }}</van-button>
						<van-button v-else class="qindex" v:key="qid" @click="toCard(qid)">{{ Number(qid) + 1 }}</van-button>
					</van-space>
				</div>
			</van-popup>
			<van-tabbar v-if="!showFace" :border="true" :placeholder="true" active-color="#323233" safe-area-inset-bottom>
				<van-tabbar-item icon="arrow-left" @click="prevPage">
					上一题
				</van-tabbar-item>
				<van-tabbar-item :icon="signs?.[questions?.[currentId]?.questionid] ? 'star' : 'star-o'" @click="signQuestion(questions[currentId].questionid)">
					标记
				</van-tabbar-item>
				<van-button round style="width:35vw;margin-top: 3px;;" text="交卷" type="primary" @click="submitPaperLayer" />
				<van-tabbar-item icon="bookmark-o" @click="showPopup = !showPopup">
					答题卡
				</van-tabbar-item>
				<van-tabbar-item icon="arrow" @click="nextPage">
					下一题
				</van-tabbar-item>
			</van-tabbar>
        </div>
        <div v-else class="peloading">
	        <van-loading color="#FFFFFF" size="100" vertical>试题加载中...</van-loading>
        </div> 
    </div>
</template>

<script>
import baseMixin from "@/framework/mixins/baseMixin.js";
import examApi from '@/framework/api/exam.js';
import storage from '@/framework/utils/storage.js';
import clock from '@/framework/utils/clock.js';
import {showConfirmDialog, showFailToast, showToast} from 'vant';
import {ref} from 'vue';
import question from '@/components/mobile/ExamQuestion.vue';
import myCamera from '@/components/mobile/Camera.vue';
import {Virtual} from 'swiper/modules';
import {Swiper, SwiperSlide} from 'swiper/vue';
import 'swiper/css';

export default {
	mixins: [baseMixin],
    data() {
        return {
            isReady:false,
            mySwiper:{},
            splitTime:40,
            sessionId:'',
            questions:[],
            userAnswer:{},
	        signs:{},
            setting: {},
            session:{},
            user:{},
            member:{},
            questionTypes:{},
            openKeys:ref(),
            leftTime:0,
            clock:null,
            time:0,
            timeString:'',
            basic:ref(),
            leaveNumber:0,
            currentId:0,
            lastSaveTime:0,
            showPopup:false,
            showFace: false,
            timer: 0,
            storage:null,
            questionNumber:0
        }
    },
    components:{
		question,
        Swiper,
        SwiperSlide,
        myCamera
    },
    setup:function(){
        return {Virtual};
    },
    async mounted(){
        this.sessionId = this.$route.params.sessionid;
        this.storage = new storage(this.sessionId);
		await this.getQuestionTypes();
        await Promise.all([
	        this.getData(),
	        this.getLeftTime()
        ]);
        if(this.storage.question)
        {
	        this.userAnswer = this.storage.question??{};
        }
        else
        {
	        this.storage.syncData(this.userAnswer);
        }
        this.isReady = true;
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
        clearInterval(this.timer);
        this.clock ? this.clock.clear() : '';
        window.removeEventListener('visibilitychange',this.checkScreen);
    },
    methods:{
        onSwiper:function(swiper){
            this.mySwiper = swiper;
        },
        prevPage:function(){
            if(this.mySwiper.activeIndex > 0)
            {
                this.mySwiper.slidePrev();
            }
            else
            {
                showFailToast('已经是第一题了');
            }
        },
        nextPage:function(){
            if(this.mySwiper.activeIndex < this.questions.length - 1)
            {
                this.mySwiper.slideNext();
            }
            else
            {
                showFailToast('已经是最后一题了');
            }
        },
        getQuestionTypes:async function(){
            this.questionTypes = await examApi.getQuestionTypes();
        },
        getLeftTime:async function(){
	        const data = await examApi.getExamSessionLeftTime(this.sessionId);
	        this.leftTime = data.time;
        },
        getData:async function(){
            try{
	            const data = await examApi.getExamSession(this.sessionId);
	            this.basicExam = data.basicexam;
	            this.setting = data.examsessionsetting;
	            this.userAnswer = data.examsessionuserAnswer??{};
	            this.session = {
		            title:data.examsession
	            };
	            this.time = data.examsessionsetting.totalTime * 60;
	            this.signs = data.examsessionsign??{};
	            this.showType = Number(data.basicexam?.template);
	            this.questions = Object.values(data.examsessionquestion).flat();
                this.questionNumber = this.questions.length;
            }catch (e) {
	            this.$router.go(-1);
            }
        },
        submitPaperLayer:function(){
            showConfirmDialog({
                title: '交卷确认',
                message:
                    '您确定要交卷吗',
            }).then(() => {
                this.submitPaper();
            }).catch(() => { });
        },
        submitPaper:async function(){
            await this.execute( async () => {
	            const data = await examApi.finishExamSession({
		            sessionId:this.sessionId,
		            userAnswer:this.userAnswer
	            });
	            this.$router.replace('/mobile/exam/result/' + data.ehid);
            },null,null)
        },
        saveAnswer:function(){
            this.storage.syncData(this.userAnswer);
        },
        goAnchor:function(selector){
            if(this.basicExam.selftemplate === 1 || this.basicExam.autotemplate === 1){
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
        checkScreen:function(){
            if (document.visibilityState === 'visible') {
                if(this.leaveNumber >= this.basicExam.screennumber){
                    this.submitPaper();
                }else{
                    this.leaveNumber ++;
                    showToast('您已切屏'+ this.leaveNumber +'次，达到'+ this.basicExam.screennumber +'次，将自动交卷');
                }
            }
        },
        toCard:function(qid){
            this.currentId = Number(qid)
            this.mySwiper.slideTo(this.currentId);
            this.showPopup = false
        },
        onSlideChange:function(Change){
            this.currentId = Change.activeIndex
        },
        faceVerify:async function(face){
	        await this.execute( async () => {
		        await examApi.verifyExamFace({
			        sessionId:this.sessionId,
			        face
		        });
		        this.showFace = false;
	        },null,null);
        },
	    signQuestion:function(qid){
		    if(this.signs[qid]){
			    this.signs[qid] = '';
		    }else{
			    this.signs[qid] = qid;
		    }
	    },
    }
};
</script>
<style scoped>
.qindex{
	width: 40px;
	height:40px;
	line-height:40px;
	padding:0;
	margin:5px;
}
</style>
