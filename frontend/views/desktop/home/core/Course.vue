<template>
	<lay-card class="pagecontent">
		<lay-card style="position: relative;">
            <lay-page-header content="我的课程" @back="$router.go(-1)" class="planbread"></lay-page-header>
            <lay-tab v-model="tabCurrent" type="brief" :activeBarTransition="true">
				<lay-tab-item id="1" v-if="courses?.length > 0">
					<template #title>
						<span class="tabtitle">我的课程</span>
					</template>
					<div style="margin-top: 10px;">
						<lay-container>
							<lay-row space="15">
								<lay-col sm="8" xs="8" class="planindexbox" v-for="(course,cid) in courses" :key="cid">
									<div class="plan-card pointer" @click="setCourse(course.csid)">
										<div class="plan-thumb">
											<img :src="course.csthumb" />
										</div>
										<div class="plan-content">
											<h4 class="plan-title">{{ course.cstitle }}</h4>
											<div class="plan-status not-passed">
												<lay-icon type="layui-icon-circle-dot"></lay-icon>
												<span>{{ course.cmendtime }}</span>
											</div>
										</div>
									</div>
								</lay-col>
							</lay-row>
						</lay-container>
                        <lay-page v-if="courses && courses.lenth > page.limit" v-model="page.current"  :layout="layout" v-model:limit="page.limit" :total="page.total"  @change="pageChange" theme="blue" style="float:right">
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
import courseApi from '@/framework/api/course.js';
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
			courses:[],
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
			await this.execute(async () =>{
                const data = await userApi.getMyCourse({
                    page:this.page.current,
                    limit:this.page.limit,
                });
                this.courses = data.data;
                this.page.current = data.page;
                this.page.limit = data.limit;
                this.page.total = data.total;
            },null,null)
		},
		pageChange:async function({current,limit}){
			this.page.current = current
			this.page.limit = limit
			await this.getData();
		},
		setCourse:function(csid){
			this.execute( async () =>{
				await courseApi.setCourseSession(csid);
				this.$router.push('/desktop/home/course/course');
			},null,null);
		},
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