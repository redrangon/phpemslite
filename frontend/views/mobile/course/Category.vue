<template>
	<div style="width:100%;">
		<!-- 导航条 -->
        <van-nav-bar :title="currentCategory?.catname??'课程列表'" fixed left-arrow  placeholder @click-left="$router.go(-1)">
            <template #right v-if="categories?.length > 0">
                <van-icon name="apps-o" size="18" @click="showRight = true"/>
            </template>
        </van-nav-bar>
		<div class="card-container">
			<van-space direction="vertical" fill>
				<van-list v-if="page.total > 0" v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoad">
                    <van-cell-group v-if="courses.length >= 1" class="menu-list">
                        <van-cell v-for="(lesson, index) in courses" :key="index" :is-link="true" :title="lesson.cstitle" center title-style="flex: 1;min-width: 0;" @click="$router.push(`/mobile/course/detail/${lesson.csid}`)">
                            <template #label>
                                <div class="detailDesc" v-html="lesson.csdescribe"></div>
                            </template>
                        </van-cell>
                    </van-cell-group>
				</van-list>
				<van-empty v-else description="当前没有课程"></van-empty>
			</van-space>
		</div>
        <van-popup v-model:show="showRight" position="right" :style="{ width: '50%', height: '100%' }">
            <van-cell>
                <template #title>
                    <span style="font-weight: bold;padding:10px 0;">
                        <van-icon name="apps-o" size="18"/>
                        分类列表
                    </span>
                </template>
            </van-cell>
            <van-cell v-for="category in categories" :key="category.catid">
                <template #title>
                    <router-link :to="`/mobile/course/category/${category.catid}`">
                        <span style="padding:10px 0 10px 20px;">
                            {{category.catname}}
                        </span>
                    </router-link>
                </template>
            </van-cell>
        </van-popup>
	</div>
</template>
<style scoped>
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
            categoryId:0,
            currentCategory:{},
            categories:[],
            courses:[],
			loading: false,
			finished: false,
			page:{
				current:1,
				total:0,
				limit:10
			},
            showRight:false
		}
	},
    async mounted() {
        this.categoryId = this.$route.params.catid??0;
        this.currentCategory = await courseApi.getCategory(this.categoryId);
        await this.getCategroies();
        await this.getData();
    },
	methods:{
        getData:async function(){
	        await this.execute( async () =>{
                const data = await courseApi.getSubjectList({
                    page:this.page.current,
                    limit:this.page.limit,
                    catId:this.categoryId
                });
                this.courses = data.data;
                this.page.current = data.page;
                this.page.limit = data.limit;
                this.page.total = data.total;
            },null,null)
		},
		onLoad:async function() {
			// 加载更多数据
			let data = await courseApi.getSubjectList({
				page:this.page.current + 1,
				limit:this.page.limit,
                catId:this.categoryId
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
		getCategroies:async function(){
			await this.execute( async () =>{
				this.categories = await courseApi.getCategoryList(this.categoryId);
			},null,null);
		}
	}

}
</script>