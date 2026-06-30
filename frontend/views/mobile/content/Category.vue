<template>
	<div style="width:100%;">
		<!-- 导航条 -->
        <van-nav-bar :title="currentCategory?.catname??'新闻列表'" fixed left-arrow  placeholder @click-left="$router.go(-1)">
            <template #right v-if="categories?.length > 0">
                <van-icon name="apps-o" size="18" @click="showRight = true"/>
            </template>
        </van-nav-bar>
		<div class="card-container">
			<van-space direction="vertical" fill>
				<van-list v-if="page.total > 0" v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoad">
                    <van-cell-group v-if="contents.length >= 1" class="menu-list">
                        <van-cell v-for="(content, index) in contents" :key="index" :is-link="true" :title="content.contenttitle" center title-style="flex: 1;min-width: 0;" :to="`/mobile/content/content/${content.contentid}`">
                            <template #label>
                                <div class="detailDesc">{{content.contentinputtime}}</div>
                            </template>
                        </van-cell>
                    </van-cell-group>
				</van-list>
				<van-empty v-else description="当前没有新闻"></van-empty>
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
                    <router-link :to="`/mobile/content/category/${category.catid}`">
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
import contentApi from '@/framework/api/content.js';
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
            contents:[],
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
        this.currentCategory = await contentApi.getCategory(this.categoryId);
        await this.getCategroies();
        await this.getData();
    },
	methods:{
        getData:async function(){
	        await this.execute( async () =>{
                const data = await contentApi.getContentList({
                    page:this.page.current,
                    limit:this.page.limit,
                    catId:this.categoryId
                });
                this.contents = data.data;
                this.page.current = data.page;
                this.page.limit = data.limit;
                this.page.total = data.total;
            },null,null)
		},
		onLoad:async function() {
			// 加载更多数据
			let data = await contentApi.getContentList({
				page:this.page.current + 1,
				limit:this.page.limit,
                catId:this.categoryId
			});
			this.page.current = data.page;
			this.page.total = data.total;
			this.page.limit = data.limit;
            data.data.map(item=>{
                this.contents.push(item)
            });
			if(this.contents.length >= this.page.total){
				this.finished = true;
			}
		},
		getCategroies:async function(){
			await this.execute( async () =>{
				this.categories = await contentApi.getCategoryList(this.categoryId);
			},null,null);
		}
	}

}
</script>