<template>
	<div style="width:100%;">
        <van-nav-bar title="模拟考试" left-arrow @click-left="$router.back()" placeholder fixed/>
		<div class="card-container">
			<div v-if="examSession" style="text-align: center;">
				<div style="margin-top: 10px;"></div>
				<van-cell-group style="padding: 15px;">
					<van-space direction="vertical">
						<div>
							<h3>{{examSession.examsession}}</h3>
							<p class="desc">您于{{examSession.examsessionstarttime}} 开启了一场未完成的考试！请选择继续考试或者放弃。</p>
						</div>
						<div>
							<van-space :size="20">
								<van-button type="primary" @click="recoverExam()" :disabled="disabled">继续考试</van-button>
								<van-button type="danger" @click="dropExam()">放弃考试</van-button>
							</van-space>
						</div>
					</van-space>
				</van-cell-group>
			</div>
			<div v-else>
				<van-cell-group>
					<div class="desc">
						<p class="title">考试须知：</p>
						<p>1、点击考试名称按钮进入答题界面，考试开始计时。</p>
						<p>2、在随机考试过程中，您可以通过顶部的考试时间来掌握自己的做题时间。</p>
						<p>3、提交试卷后，可以通过“查看答案和解析”功能进行总结学习。</p>
						<p>4、系统自动记录模拟考试的考试记录，学员考试结束后可以进入“答题记录”功能进行查看。</p>
					</div>
				</van-cell-group>
				<div style="margin-top: 10px;"></div>
				<van-cell-group class="menu-list">
					<van-cell is-link center v-for="(paper,pid) in papers" :key="pid" @click="disabled?false:goExamPaper(paper.examid)">
						<template #title>
							<div>
								<span class="title">{{ paper.exam }}</span>
							</div>
						</template>
						<template #label>
							<van-space>
								<van-tag type="primary">总分：{{ paper.examtotalscore }}分</van-tag>
								<van-tag type="primary">及格：{{ paper.exampassmark }}分</van-tag>
								<van-tag type="primary">时间：{{ paper.examtotaltime }}分钟</van-tag>
							</van-space>
						</template>
					</van-cell>
				</van-cell-group>
			</div>
        </div>
    </div>
</template>

<script>
	import examApi from '@/framework/api/exam.js';
	import {useAuthStore} from "@/stores/auth.js";
	import baseMixin from "@/framework/mixins/baseMixin.js";
    import { showLoadingToast,closeToast  } from 'vant';
    import { ref } from 'vue';
    export default {
	    setup() {
		    const authStore = useAuthStore();
		    return {authStore};
	    },
	    mixins:[baseMixin],
        data() {
            return {
                loading: false,
                finished: false,
                page:{current:1,limit:20,total:1},
	            papers:[],
	            examSession:null,
	            disabled:false,
	            basic:{}
            };
        },
        async created() {
			await this.getData();
		},
        methods: {
            getData:async function(){
	            await this.execute(async () => {
		            const data = await examApi.getExamPaper();
		            this.papers = data.papers;
		            this.examSession = data.session;
	            }, null, null);
            },
	        goExamPaper:async function(paperId){
		        await this.execute(async () => {
			        const res = await examApi.drawExamPaper({paperId});
			        this.$router.push('/mobile/exam/paper/'+res.sessionid);
		        },null, null);
	        },
	        recoverExam:async function(){
		        this.disabled = true;
		        showLoadingToast();
		        try{
			        await examApi.recoverExamSession(this.examSession.examsessionid);
			        this.$router.push('/mobile/exam/paper/'+this.examSession.examsessionid);
		        }catch(e){
			        this.disabled = false;
		        }finally{
			        closeToast();
		        }
	        },
	        dropExam:function(){
		        this.confirmDelete(async ()=>{
			        await examApi.dropExamSession(this.examSession.examsessionid);
		        },this.getData);
	        }
        }
    };
</script>

<style scoped>
.menu-list div{
	padding:10px 10px;
	font-size: 16px;
	background: transparent;
}
.title{
	font-size: 18px;
}
.desc{
	padding:10px 15px;
	font-size: 16px;
	line-height: 2;
	color:#000000;
	text-align: left;
}
</style>