<template>
	<div style="width:100%;">
        <van-nav-bar title="章节练习" left-arrow @click-left="$router.back()" fixed placeholder />
        <transition :name="action" :mode="mode">
	        <div class="card-container" v-if="isReady">
	            <div style="padding-bottom:40px;background: #FFFFFF;">
	                <!-- 考场控制台信息 -->
	                <question :index="page.current-1" v-model:userAnswer="userAnswer" :question="question" :questionType="questionType" @saveAnswer="saveAnswer"></question>
	                <div style="padding: 0 10px;">
	                    <template v-if="questionType.questsort === 1 && !isShow">
	                        <van-button type="primary" @click="singleChange" block>查看答案</van-button>
	                    </template>
	                    <template v-else-if="(questionType.questchoice === 2 || questionType.questchoice === 3)  && !isShow">
	                        <van-button type="primary" @click="singleChange" block>选择完毕</van-button>
	                    </template>
	                    <template v-else-if="questionType.questchoice === 5 && !isShow">
	                        <van-button type="primary">作答完毕</van-button>
	                    </template>
	                    <div v-if="isShow">
	                        <template v-if="questionType.questsort === 1">
	                            <div class="question" v-html="question.questionanswer??'无参考答案'"></div>
	                        </template>
	                        <template v-else>
	                            <div class="question" style="background-color: #16b777;padding:10px 15px;color:whitesmoke;" v-if="userAnswer === question.questionanswer">恭喜您回答正确！</div>
	                            <div class="question" style="background-color: #FF5722;padding:10px 15px;color:whitesmoke;" v-else>回答错误，正确答案是{{question.questionanswer}}，您选择了{{userAnswer}}</div>
	                        </template>
	                        <div v-html="question.questiondescribe" class="question" v-if="question.questiondescribe"></div>
	                    </div>
	                </div>
	            </div>
	        </div>
            <div v-else>
                <van-loading size="100" color="#FFFFFF" vertical>试题加载中...</van-loading>
            </div>
        </transition>
		<van-popup v-model:show="showPicker" destroy-on-close round position="bottom">
            <van-picker v-model="pickerValue" :columns="columns" @cancel="showPicker = false" @confirm="pickerConfirm" />
        </van-popup>
		<van-action-bar placeholder>
			<van-action-bar-button icon="arrow-left" type="primary" text="上一题" @click="prevPage"></van-action-bar-button>
			<van-action-bar-button :icon="question.isfavor?'star':'star-o'" type="primary" text="收藏" @click="onFavor"/>
			<van-action-bar-button icon="bookmark-o" type="primary" text="跳题" @click="showPicker = true"/>
			<van-action-bar-button icon-position="right" icon="arrow" type="primary" text="下一题" @click="nextPage"></van-action-bar-button>
		</van-action-bar>
    </div>    
</template>
<script>
    import myEditor from '@/components/mobile/Editor.vue';
    import question from '@/components/mobile/Question.vue';
    import {ref} from 'vue';
    import {showFailToast,showSuccessToast,showLoadingToast  } from 'vant'
    import examApi from "@/framework/api/exam.js";
    import baseMixin from "@/framework/mixins/baseMixin.js";
    export default {
	    mixins: [baseMixin],
        data() {
            return {
                page:{current:1,limit:1,total:1},
                showPicker:false,
                pickerValue:[],
                layout:['count', 'prev', 'next', 'refresh', 'skip'],
                basic:{},
                question:{},
	            questionTypes:{},
                questionType:{},
	            pointid:0,
                userAnswer:null,
	            point:{},
                isShow:false,
	            isReady:false,
                selectors:['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'],
                columns:[],
                total:0,
                action:'',
                mode:''
            };
        },
        components:{myEditor,question},
        async mounted() {
			this.pointid = this.$route.params.pointid;
	        this.questionTypes = await examApi.getQuestionTypes();
	        const data = await examApi.getExerciseProgress(this.pointid);
	        this.page.current = data.number;
	        await this.getData();
		},
        methods: {
            getData:async function(){
	            this.isReady = false;
	            if(this.$refs.page)
	            {
		            this.$refs.page.scrollTo({top:0});
	            }
	            await this.execute( async () => {
		            const timestamp = Date.now();
		            const data = await examApi.getExerciseQuestion({
			            page:this.page.current,
			            pointid:this.pointid,
			            limit:1
		            });
		            this.question = data.question;
		            this.page.current = data.page;
		            this.page.total = data.total;
					this.total = data.total;
		            this.questionType = this.questionTypes[this.question.questiontype];
		            this.isShow = false;
					this.userAnswer = null;
		            const ltime = 500 + timestamp - Date.now();
		            if(ltime > 0)await this.delay(ltime);
		            this.isReady = true;
	            },null,null);
            },
            singleChange:function(){
				if(this.userAnswer && this.userAnswer !== '')this.isShow = true;
            },
            prevPage:function(){
                if(this.page.current>1)
                {
                    this.page.current--;
                    this.getData();
                }
                else
                {
                    showFailToast('已经是第一题了'); 	
                }
            },
            nextPage:function(){
                if(this.page.current<this.page.total)
                {
                    this.page.current++;
                    this.getData();
                }
                else
                {
                    showFailToast('已经是最后一题了'); 	
                }
            },
            pickerConfirm:function(){
                this.page.current = this.pickerValue[0];
                this.showPicker = false;
                this.getData();
            },
            onFavor:async function(){
	            if(this.question.isfavor){
		            await examApi.unFavorQuestion(this.question.questionid);
		            this.question.isfavor = false;
	            }else{
		            await examApi.favorQuestion(this.question.questionid);
		            this.question.isfavor = true;
	            }
            },
            saveAnswer:function(){
                if(this.questionType.questsort !== 1 && (this.questionType.questchoice === 1 || this.questionType.questchoice === 4))
                {
					this.singleChange();
                }
            }, 
            delay:function(ms){
                return new Promise(resolve => setTimeout(resolve, ms));
            }          
        },
        watch:{
        	total:function(){
                const columns = [];
                for(let i=1;i<=this.total;i++)
                {
                    columns.push({text:`第${i}题`,value:i});
                }
                this.columns = columns;
            },
            isReady:function(val){
                this.action = val?'':'slide-left';
            }
        }
    };
</script>
<style scoped>
.question {
	line-height: 2;
	font-size:16px;
	background: #ffffff;
	padding: 15px 10px;
	border-radius: 5px;
	margin-bottom: 10px;
}
</style>