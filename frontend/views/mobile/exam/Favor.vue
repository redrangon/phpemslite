<template>
	<div style="width:100%;">
        <van-nav-bar fixed left-arrow placeholder title="习题收藏" @click-left="$router.back()" />
	    <transition :mode="mode" :name="action">
	        <div v-if="isReady" class="card-container">
		        <template v-if="question && question.questionid">
                    <!-- 考场控制台信息 -->                    
                    <question v-model:userAnswer="userAnswer" :index="page.current - 1" :question="question" :questionType="questionType" @saveAnswer="saveAnswer"></question>
			        <div style="padding: 0 10px;">
                        <template v-if="questionType.questsort === 1 && !isShow">
                            <van-button block type="primary" @click="singleChange">查看答案</van-button>
                        </template>
                        <template v-else-if="(questionType.questchoice === 2 || questionType.questchoice === 3)  && !isShow">
                            <van-button block type="primary" @click="singleChange">选择完毕</van-button>
                        </template>
                        <template v-else-if="questionType.questchoice === 5 && !isShow">
                            <van-button type="primary">作答完毕</van-button>
                        </template>
                        <div v-if="isShow">
                            <template v-if="questionType.questsort === 1">
                                <div class="question" v-html="question.questionanswer??'无参考答案'"></div>
                            </template>
                            <template v-else>
                                <div v-if="userAnswer === question.questionanswer" class="question" style="background-color: #16b777;padding:10px 15px;color:whitesmoke;">恭喜您回答正确！</div>
                                <div v-else class="question" style="background-color: #FF5722;padding:10px 15px;color:whitesmoke;">回答错误，正确答案是{{question.questionanswer}}，您选择了{{userAnswer}}</div>
                            </template>
                            <div v-if="question.questiondescribe" class="question" v-html="question.questiondescribe"></div>
                        </div>      
                    </div>
                </template>
            </div>
            <div v-else class="peloading">
                <van-loading color="#FFFFFF" size="100" vertical>试题加载中...</van-loading>
            </div> 
        </transition>
        <van-popup v-model:show="showPicker" destroy-on-close position="bottom" round>
            <van-picker v-model="pickerValue" :columns="columns" @cancel="showPicker = false" @confirm="pickerConfirm" />
        </van-popup>
        <van-action-bar placeholder>
            <van-action-bar-button icon="arrow-left" text="上一题" type="primary" @click="prevPage"></van-action-bar-button>
            <van-action-bar-button icon="star" text="移除" type="primary" @click="onFavor"/>
            <van-action-bar-button icon="bookmark-o" text="跳题" type="primary" @click="showPicker = true"/>
            <van-action-bar-button icon="arrow" icon-position="right" text="下一题" type="primary" @click="nextPage"></van-action-bar-button>
        </van-action-bar>
    </div>
</template>
<script>
	import examApi from '@/framework/api/exam.js';
	import baseMixin from "@/framework/mixins/baseMixin.js";
    import myEditor from '@/components/mobile/Editor.vue';
    import question from '@/components/mobile/Question.vue';
    import {ref} from 'vue';
    import {showFailToast,showSuccessToast } from 'vant'
    export default {
	    mixins: [baseMixin],
	    data() {
	        return {
		        isReady:false,
		        isShow:false,
                page:{
					current:1,
	                limit:1,
	                total:1
                },
                showPicker:false,
                pickerValue:[],
                layout:['count', 'prev', 'next', 'refresh', 'skip'],
                basic:{},
                question:{},
                questionTypes:{},
		        questionType:{},
                userAnswer:null,
                total:0,
                action:'',
                mode:''
            };
        },
        components:{myEditor,question},
        async mounted() {
			this.questionTypes = await examApi.getQuestionTypes();
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
		            const data = await examApi.getFavorQuestionList({
			            page:this.page.current,
			            limit:1
		            });
		            this.question = data.data?.[0];
					this.page.current = data.page;
		            this.page.total = data.total;
		            this.total = data.total;
		            this.questionType = this.questionTypes[this.question.questiontype];
					this.userAnswer = null;
		            this.isShow = false;
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
	        saveAnswer:function(){
		        if(this.questionType.questsort !== 1 && (this.questionType.questchoice === 1 || this.questionType.questchoice === 4))
		        {
			        this.singleChange();
		        }
	        },
	        onFavor:async function(){
		        await examApi.unFavorQuestion(this.question.questionid);
		        await this.getData();
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
<style>


.question{
	line-height: 2;
	font-size:16px;
	background: #ffffff;
	padding: 15px 10px;
	border-radius: 5px;
	margin-bottom: 10px;
}
</style>