<template>
	<lay-card class="pagecontent">
		<lay-card style="position: relative;">
			<lay-page-header content="我的考试" @back="$router.go(-1)" class="planbread"></lay-page-header>
			<lay-tab v-model="tabCurrent" type="brief" :activeBarTransition="true">
                <lay-tab-item id="1" v-if="exams?.length > 0">
                    <template #title>
                        <span class="tabtitle">我的考试</span>
                    </template>
                    <div style="margin-top: 10px;">
                        <lay-container>
                            <lay-row space="15">
                                <lay-col sm="8" xs="8" class="planindexbox" v-for="(basic,bid) in exams" :key="bid">
                                    <div class="plan-card pointer" @click="setExam(basic.basicid)">
                                        <div class="plan-thumb">
                                            <img :src="basic.basicthumb" />
                                        </div>
                                        <div class="plan-content">
                                            <h4 class="plan-title">{{ basic.basic }}</h4>
                                            <div class="plan-status not-passed">
                                                <lay-icon type="layui-icon-circle-dot"></lay-icon>
                                                <span>{{basic.emendtime}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </lay-col>
                            </lay-row>
                        </lay-container>
                        <lay-page v-if="exams && exams.lenth > page.limit" v-model="page.current"  :layout="layout" v-model:limit="page.limit" :total="page.total"  @change="pageChange" theme="blue" style="float:right">
                        </lay-page>
                    </div>
                </lay-tab-item>
                <lay-tab-item id="1" v-else>
                    <template #title>
                        <span class="tabtitle">无学习任务</span>
                    </template>
                    <div style="margin-top: 10px;">
                        <lay-empty description="当前计划下无学习任务"></lay-empty>
                    </div>
                </lay-tab-item>
			</lay-tab>
		</lay-card>
	</lay-card>
</template>
<script>
import userApi from '@/framework/api/user.js';
import examApi from '@/framework/api/exam.js';
import {useAuthStore} from "@/stores/auth.js";
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
    setup() {
        const store = useAuthStore();
        return {store}
    },
	data() {
		return {
			tabCurrent:"1",
            exams:[],
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{
				current:1,
				total:1,
				limit:5
			}
		}
	},
	async mounted() {
		await this.getData();
	},
	methods:{
		getData:async function(){
            await this.execute(async () => {
                const data = await userApi.getMyBasic({
                    page:this.page.current,
                    limit:this.page.limit,
                });
                this.exams = data.data;
            },null,null);
		},
		pageChange:function({current,limit}){
			this.page.current = current
			this.page.limit = limit
			this.getData();
		},
		setExam:async function(examId){
			await this.execute( async () =>{
				await examApi.setExamSession(examId);
				await this.store.setBasic();
				this.$router.push('/desktop/home/exam/basic');
			},null,null);
		}
	}
}
</script>
<style>
.notice p{
	text-indent: 2em;
	padding:10px;
	line-height: 2;
}
</style>