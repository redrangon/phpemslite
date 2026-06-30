<template>
	<div style="width:100%;">
        <!-- 导航栏 -->
        <van-nav-bar :title="title" fixed left-arrow placeholder @click-left="$router.go(-1)"/>
		<div v-if="isReady" class="card-container">
            <swiper :initialSlide="currentId" :modules="[Virtual]" :slides-per-view="1" :space-between="50" virtual @slideChange="onSlideChange" @swiper="onSwiper">
                <swiper-slide v-for="(question,qid) in questions" :key="qid" :virtualIndex="qid" style="overflow-y: auto;">
                    <div style="width:100%;height:calc(100vh - 96px);overflow-y: auto;">
                        <question :disabled="true" :index="qid" :question="question" :questionType="questionTypes[question.questiontype]" :userAnswer="userAnswers[question.questionid]"></question>
                        <div style="margin-top:10px;">
                            <template v-if="questionTypes[question.questiontype].questsort === 1">
                                <van-cell>
                                    <template #title>
                                        <span style="font-size: 16px">我的答案</span>
                                    </template>
                                </van-cell>
                                <div class="question" style="padding-left:20px;padding-right:20px;" v-html="userAnswers[question.questionid]??'未做答'"></div>
                                <van-cell>
                                    <template #title>
                                        <span style="font-size: 16px">参考答案</span>
                                    </template>
                                </van-cell>
                                <div class="question" style="padding-left:20px;padding-right:20px;" v-html="question.questionanswer??'无参考答案'"></div>
                            </template>
                            <template v-else>
                                <div v-if="userAnswers[question.questionid] === question.questionanswer" class="question" style="background-color: #16b777;padding:10px 15px;color:whitesmoke;margin:10px;">
                                    本题答案是{{question.questionanswer}}，恭喜您回答正确！得{{scores[question.questionid]}}分
                                </div>
                                <div v-else class="question" style="background-color: #FF5722;padding:10px 15px;color:whitesmoke;margin:10px;">
                                    回答错误，正确答案是{{question.questionanswer}}，您{{userAnswers[question.questionid]?'选择了'+userAnswers[[question.questionid]]:'未作答'}}
                                </div>
                            </template>
                            <div v-if="question.questiondescribe" class="question" v-html="question.questiondescribe"></div>
                        </div>
                    </div>
                </swiper-slide>
            </swiper>
		</div>
		<div v-else class="peloading">
			<van-loading color="#FFFFFF" size="100" vertical>试题加载中...</van-loading>
		</div>
		<van-popup v-model:show="showPopup" closeable destroy-on-close position="bottom" round>
            <div style="padding: 20px;margin-top: 20px;">
                <van-space v-for="(question,qid) in questions" v:key="qid">
                    <van-button v-if="setting.questionScore[question.questionid] !== scores[question.questionid]" class="qindex" type="danger" @click="toCard(qid)">
                        {{ Number(qid) + 1 }}
                    </van-button>
                    <van-button v-else class="qindex" type="primary" @click="toCard(qid)">{{ Number(qid) + 1 }}</van-button>
                </van-space>
            </div>
        </van-popup>
        <van-action-bar placeholder>
            <van-action-bar-button icon="arrow-left" text="上一题" type="primary" @click="prevPage"></van-action-bar-button>
            <van-action-bar-button :icon="isFavor?'star':'star-o'" text="收藏" type="primary" @click="onFavor"/>
            <van-action-bar-button icon="bookmark-o" text="答题卡" type="primary" @click="showPopup = !showPopup"/>
            <van-action-bar-button icon="arrow" icon-position="right" text="下一题" type="primary" @click="nextPage"></van-action-bar-button>
        </van-action-bar>
    </div>
</template>

<script>
	import examApi from '@/framework/api/exam.js';
	import baseMixin from "@/framework/mixins/baseMixin.js";
	import { showConfirmDialog,showDialog,showFailToast  } from 'vant';
    import { ref } from 'vue';
    import question from '@/components/mobile/Question.vue';
    import { Virtual } from 'swiper/modules';
    import { Swiper, SwiperSlide } from 'swiper/vue';
    import 'swiper/css';
    export default {
	    mixins:[baseMixin],
        data() {
            return {
                isReady:false,
                mySwiper:null,
                questions:[],
	            userAnswers:{},
                setting:{},
                questionTypes:{},
                favors:{},
                basic:{},
                currentId:0,
                showPopup:false,
	            ehId:0,
	            scores:{},
	            title:'',
	            isFavor:false
            }
        },
        components:{question,Swiper,SwiperSlide},
        setup:function(){
            return {Virtual};
        },        
        async mounted(){
            this.ehId = this.$route.params.ehid;
	        this.questionTypes = await planApi.getQuestionTypes();
		    await this.getData();
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
            getData:async function(){
	            await this.execute(async () => {
		            const data = await examApi.getHistoryDetail(this.ehId);
		            this.setting = data.detail.ehdsetting;
		            this.userAnswers = data.detail.ehdanswer??{};
		            this.scores = data.detail.ehdscores??{};
		            this.questions = Object.values(data.detail.ehdquestion).flat();
					this.score = data.ehscore;
		            this.favors = data.favors;
		            this.isFavor = this.favors[this.questions[0].questionid];
					this.title = data.ehexam;
		            this.isReady = true;
	            },null,null);
            },
            goAnchor:function(selector){
                if(this.basic.basicexam.selftemplate === 1 || this.basic.basicexam.autotemplate === 1){
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
            onFavor:async function(){
	            await this.execute(async () => {
		            const questionId = this.questions[this.mySwiper.activeIndex].questionid;
					if(this.favors[questionId]){
			            await examApi.unFavorQuestion(questionId);
			            this.favors[questionId] = false;
						this.isFavor = false;
		            }else{
			            await examApi.favorQuestion(questionId);
			            this.favors[questionId] = true;
						this.isFavor = true;
		            }
	            },null,null);
            },
            toCard:function(qid){
                this.currentId = Number(qid)
                this.mySwiper.slideTo(this.currentId);
                this.showPopup = false
            },
            onSlideChange:function(Change){
                this.currentId = Change.activeIndex
                this.isFavor = !!this.favors[this.questions[this.currentId].questionid];
            } 
        }
    };
</script>
<style scoped>
.qindex{
	width: 40px;
	height:40px;
	line-height:40px;
	padding:0px;
	margin:5px;
}
.question {
	line-height: 2;
	font-size:16px;
	background: #ffffff;
	padding: 15px 10px;
	border-radius: 5px;
	margin-bottom: 10px;
}
</style>
