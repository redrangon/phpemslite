<template>
	<lay-card class="pagecontent">
		<lay-card style="position: relative;">
			<lay-page-header :content="basic.basic" @back="$router.go(-1)" class="planbread"></lay-page-header>			
			<lay-tab v-model="tabCurrent" type="brief">
				<lay-tab-item id="1">
					<template #title>
						<span class="tabtitle">模拟考试</span>
					</template>
					<lay-container>
						<div class="desc">
							<p class="title">考试须知：</p>
							<p>1、点击考试名称按钮进入答题界面，考试开始计时。</p>
							<p>2、在随机考试过程中，您可以通过顶部的考试时间来掌握自己的做题时间。</p>
							<p>3、提交试卷后，可以通过“查看答案和解析”功能进行总结学习。</p>
							<p>4、系统自动记录模拟考试的考试记录，学员考试结束后可以进入“答题记录”功能进行查看。</p>
						</div>
					</lay-container>
					<lay-container v-if="papers">
						<div v-if="examSession">
							<lay-card>
								<div style="overflow:hidden;padding:10px">
									<lay-col md="18">
										<lay-space direction="vertical">
											<lay-space><h3>{{examSession.examsession}}</h3></lay-space>
											<lay-space size="lg" style="color:#999999;">您于{{examSession.examsessionstarttime}} 开启了一场未完成的考试！</lay-space>
										</lay-space>
									</lay-col>
									<lay-col md="6" style="padding:10px">
										<lay-space>
											<lay-button type="normal" @click="recoverExam()" :disabled="disabled">继续考试</lay-button>
											<lay-button type="danger" @click="dropExam()">放弃考试</lay-button>
										</lay-space>
									</lay-col>
								</div>
							</lay-card>
						</div>
						<div v-else>
							<lay-card>
								<lay-row v-for="(paper,pid) in papers" :key="pid">
									<lay-line theme="#F5F5F5" v-if="pid > 0" style="min-width: auto;width: auto;margin-top: 20px;clear: both;margin-bottom: 20px;"></lay-line>
									<lay-col md="20" xs="20" sm="20">
										<h2 class="title">{{ paper.exam }}</h2>
										<lay-space size="lg" style="color:#999999;margin-top: 20px;">
											<lay-space>试卷总分：{{ paper.examtotalscore }}分</lay-space>
											<lay-space>及格分：{{ paper.exampassmark }}分</lay-space>
											<lay-space>考试时间：{{ paper.examtotaltime }}分钟</lay-space>
										</lay-space>
									</lay-col>
									<lay-col md="4" xs="4" sm="4" style="text-align: right;padding-top:20px;">
										<lay-button type="normal" @click="goExamPaper(paper.examid)" :disabled="disabled">开始考试</lay-button>
									</lay-col>
								</lay-row>
							</lay-card>
						</div>
					</lay-container>
					<lay-empty v-else>
						暂无模拟考试
					</lay-empty>
				</lay-tab-item>
			</lay-tab>
		</lay-card>
		<lay-page v-if="page.total > page.limit" v-model="page.current" theme="blue" :limit="page.limit" :total="page.total" style="float:right;" @change="changePage"></lay-page>
	</lay-card>
</template>
<script>
import examApi from '@/framework/api/exam.js';
import {useAuthStore} from "@/stores/auth.js";
import baseMixin from "@/framework/mixins/baseMixin.js";
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
	setup() {
		const authStore = useAuthStore();
		return {authStore};
	},
	mixins:[baseMixin],
	data() {
		return {
			tabCurrent:"1",
			page:{current:1,limit:10,total:1},
			papers:[],
			examSession:null,
			disabled:false,
			basic:{}
		}
	},
	async mounted(){
		this.basic = this.authStore.basic;
		await this.getData();
	},
	methods:{
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
				this.$router.push('/desktop/home/exam/paper/'+res.sessionid);
			},'正在加载试卷', null);
		},
		changePage:function({current,limit}){
			this.page.current = current;
			this.page.limit = limit;
			this.getData();
		},
		recoverExam:async function(){
			this.disabled = true;
			const id = layer.load(0);
			try{
				await examApi.recoverExamSession(this.examSession.examsessionid);
				this.$router.push('/desktop/home/exam/paper/'+this.examSession.examsessionid);
			}catch(e){
				this.disabled = false;
			}finally{
				layer.close(id);
			}
		},
		dropExam:function(){
			this.confirmDelete(async ()=>{
				await examApi.dropExamSession(this.examSession.examsessionid);
			},this.getData);
		}
	}
}
</script>
<style scoped>
.desc{
	padding:15px;
	font-size: 16px;
	line-height: 2;
	margin-bottom: 20px;
}
</style>