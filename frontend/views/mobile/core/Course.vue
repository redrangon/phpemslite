<template>
	<div style="width:100%;">
		<!-- 导航条 -->
        <van-nav-bar title="我的课程" fixed left-arrow  placeholder @click-left="$router.go(-1)"/>
		<div class="card-container">
			<van-space direction="vertical" fill>
				<van-list v-if="page.total > 0" v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoad">
                    <van-cell-group v-if="courses.length >= 1" class="menu-list">
                        <van-cell v-for="(lesson, index) in courses" :key="index" :is-link="true" :title="lesson.cstitle" center title-style="flex: 1;min-width: 0;" @click="setCourse(lesson.csid)">
                            <template #label>
                                <div class="detailDesc" v-html="lesson.csdescribe"></div>
                            </template>
                        </van-cell>
                    </van-cell-group>
				</van-list>
				<van-empty v-else description="当前没有课程"></van-empty>
			</van-space>
		</div>
	</div>
</template>
<style scoped>
.my-swipe .van-swipe-item {
	color: #fff;
	font-size: 20px;
	line-height: 180px;
	text-align: center;
	background-color: #39a9ed;
}
.thumb{
    width: 60px; 
    height: 48px; 
    object-fit: cover;
    margin:5px 15px 5px 0px;
}
.menu-list div{
	padding:15px 20px;
	font-size: 16px;
	background: transparent;
}
.detailDesc{
	padding:10px 0!important;
	font-size: 14px!important;
	line-height: 1.75;
	max-height: 60px;
	overflow: hidden
}
</style>
<script>
import { ref } from 'vue';
import courseApi from '@/framework/api/course.js';
import examApi from '@/framework/api/exam.js';
import userApi from '@/framework/api/user.js';
import {useAuthStore} from "@/stores/auth.js";
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
    name: 'MobilePlanDetail',
	mixins: [baseMixin],
	setup() {
		const store = useAuthStore();
		return {store}
	},
	data() {
		return {
            courses:[],
			loading: false,
			finished: false,
			page:{
				current:1,
				total:0,
				limit:10
			},
		}
	},
    async mounted() {
        await this.getData();
    },
    async activated(){
        //
    },
	methods:{
        getData:async function(){
	        await this.execute( async () =>{
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
		onLoad:async function() {
			// 加载更多数据
			let data = await userApi.getMyCourse({
				page:this.page.current + 1,
				limit:this.page.limit,
			});
			this.page.current = data.page;
			this.page.total = data.total;
			this.page.limit = data.limit;
			for(let i in data.data){
				this.courses.push(data.data[i])
			}
			if(this.courses.length >= this.page.total){
				this.finished = true;
			}
		},
		setCourse:async function(csid){
			await this.execute( async () =>{
				await courseApi.setCourseSession(csid);
				this.$router.push('/mobile/course/course');
			},null,null);
		}
	}

}
</script>