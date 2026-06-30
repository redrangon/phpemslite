<template>
	<lay-card class="pagecontent">
		<lay-card style="position: relative;">
			<lay-page-header :content="basic.basic" class="planbread" @back="$router.go(-1)"></lay-page-header>
			<lay-tab v-model="tabCurrent" type="brief">
				<lay-tab-item id="1">
					<template #title>
						<span class="tabtitle">习题收藏</span>
					</template>
					<lay-card class="favorpage">
						<template v-for="(question,qid) in favorQuestions" :key="qid">
							<div class="title">
								第{{page.current}}题 【{{questionTypes[question.questiontype].questype}}】
								<lay-space style="float:right">
									<lay-button type="primary" @click="unFavor(question.questionid)">
										<lay-icon size="18px" style="font-weight: 600;" type="layui-icon-rate"></lay-icon>
										取消收藏
									</lay-button>
								</lay-space>
							</div>
							<question v-model:userAnswer="question.questionanswer" :childIndex="0" :disabled="true" :index="page.current - 1" :question="question" :questionType="questionTypes[question.questiontype]" childindex="0"></question>
							<div style="display: block;clear:both;margin:10px;">&nbsp;</div>
							<lay-field title="正确答案">
								<template #title>
									<span class="legend">正确答案</span>
								</template>
								<div class="question" v-html="question.questionanswer"></div>
							</lay-field>
							<div style="display: block;clear:both;margin:10px;">&nbsp;</div>
							<lay-field v-if="question.questiondescribe" title="试题解析">
								<template #title>
									<span class="legend">试题解析</span>
								</template>
								<div v-html="question.questiondescribe"></div>
							</lay-field>
						</template>
					</lay-card>
					<lay-page v-if="page.total > page.limit && page.current > 0" v-model="page.current" :layout="layout" :limit="page.limit" :total="page.total" style="float:right;margin-top:20px;" theme="blue" @change="changePage">
						<template #prev>上一题</template>
						<template #next>下一题</template>
					</lay-page>
				</lay-tab-item>
			</lay-tab>
		</lay-card>		
	</lay-card>
</template>
<script>
import examApi from '@/framework/api/exam.js';
import {layer} from '@layui/layui-vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {ref} from 'vue';
import {useAuthStore} from "@/stores/auth.js";
import question from '@/components/desktop/Question.vue';
export default {
	mixins: [baseMixin],
	setup() {
		const basic = ref({});
		basic.value = useAuthStore().basic;
		return {basic}
	},
	data() {
		return {
			tabCurrent:"1",
			page:{current:1,limit:1,total:1},
			layout:['count', 'prev', 'next', 'refresh', 'skip'],
			favorQuestions:[],
			questionTypes:{}
		}
	},
	components:{
		question
	},
	async mounted(){
		if(this.basic.basicexam.model === 2)
		{
			this.$router.replace('/desktop/home/exam/exam');
			return;
		}
		this.questionTypes = await examApi.getQuestionTypes();
		await this.getData();
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await examApi.getFavorQuestionList({
					page:this.page.current,
					limit:this.page.limit
				});
				this.favorQuestions = data.data;
				this.page.current = data.page;
				this.page.total = data.total;
				this.page.limit = data.limit;
			},null,null);
		},
		unFavor:function(questionId){
			this.confirmDelete(async () => {
				await examApi.unFavorQuestion(questionId);
			},this.getData);
		},
		changePage:function({current,limit}){
			this.page.current = current;
			this.page.limit = limit;
			this.getData();
		}
	}
}
</script>
<style scoped>
.favorpage{
	font-size: 16px;	
}
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
.selector{
	padding:20px 10px;
}
</style>