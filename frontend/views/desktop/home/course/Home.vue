<template>
    <div class="pagecontent">
        <!-- 新闻列表 -->
        <lay-card :bordered="false" class="news-list-card">
            <lay-tab v-model="tabCurrent1" type="brief">
                <lay-tab-item id="tc1_1">
                    <template #title>
                        <span class="tabtitle">
                            <span v-if="currentCategory.catid > 0">{{currentCategory.catname}}</span>
                            <span v-else>最新课程</span>
                        </span>
                    </template>
                    <div v-if="page.total > 0" class="news-list">
                        <div v-for="course in courses" :key="course.csid" class="news-item" @click="viewCourseDetail(course)">
                            <div class="news-image">
                                <img :src="course.csthumb" :alt="course.cstitle" />
                            </div>
                            <div class="news-content">
                                <h3 class="news-title">{{ course.cstitle }}</h3>
                                <p class="news-desc" v-html="course.csdescribe"></p>
                                <div class="news-footer">
                                    <lay-button type="primary" size="md">
                                        学习课程
                                    </lay-button>
                                </div>
                            </div>
                        </div>
                        <div style="text-align: center;padding:10px;">
                            <router-link to="/desktop/home/course/category/0">
                                <lay-button>更多课程 &gt;&gt;</lay-button>
                            </router-link>
                        </div>
                    </div>
                    <lay-empty v-else description="暂无课程数据"></lay-empty>
                </lay-tab-item>
            </lay-tab>
        </lay-card>
    </div>
</template>

<script>
import { ref } from 'vue';
import courseApi from "@/framework/api/course.js";
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
	data() {
		return {
            activeCarousel:0,
            banners: [
                {
                    id: 1,
                    title: '2026年度考试计划正式发布',
                    description: '全年共安排各类考试50余场，涵盖职业资格、技能等级等多个类别',
                    url: '/desktop/home/exam'
                },
                {
                    id: 2,
                    title: '在线学习平台全新升级',
                    description: '优化用户体验，新增视频课程、文本课程等功能',
                    url: '/desktop/home/course'
                },
                {
                    id: 3,
                    title: '证书查询系统上线',
                    description: '持证人可在线查询和下载电子证书，便捷高效',
                    url: '/desktop/home/core/cert'
                }
            ],
            currentCategory:{
                catid:0,
                catname:'全部分类'
            },
            tabCurrent1:"tc1_1",
            layout:['count', 'prev', 'page', 'next'],
            page:{
                current:1,
                total:0,
                limit:10
            },
            courses: [],
            categories: [],
		};
	},
    async mounted() {
        //
    },
    async activated() {
        await this.getCategory();
        await this.getData();
	},
	methods: {
        async getData() {
            await this.execute(async () => {
                const data = await courseApi.getSubjectList({
                    page:this.page.current,
                    limit:this.page.limit,
                    catId:this.selectedKey??0
                });
                this.page.current = data.page;
                this.page.limit = data.limit;
                this.page.total = data.total;
                this.courses = data.data;
            },null,null);
        },
        async getCategory(){
            await this.execute(async () => {
                this.categories = await courseApi.getCategoryList(this.selectedKey??0);
                if(this.selectedKey <= 0)this.currentCategory = {
                    catid:0,
                    catname:'全部分类'
                };
                else this.currentCategory = await courseApi.getCategory(this.selectedKey);
            },null,null);
        },
        pageChange:async function({current,limit}){
            this.page.current = current
            this.page.limit = limit
            await this.getData()
        },
        changeSelectedKey:async function(catId){
            this.selectedKey = catId;
            await this.getCategory();
            await this.getData();
        },
        viewCourseDetail:function(course){
            this.$router.push({
                name:'home.course.detail',
                params:{
                    csId:course.csid
                }
            })
        },
        // 查看轮播图详情
        viewDetail(banner) {
            this.$router.push(banner.url);
        },
	}
};
</script>

<style scoped>
/* 热门资讯 */
.hot-news-card {
	margin-bottom: 20px;
}

.hot-news-item {
	cursor: pointer;
	transition: all 0.3s ease;
	margin-bottom: 15px;
}

.hot-news-item:hover {
	transform: translateY(-5px);
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.news-thumb {
	position: relative;
	height: 150px;
	overflow: hidden;
	border-radius: 4px;
	margin-bottom: 10px;
}

.news-thumb img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	transition: transform 0.3s ease;
}

.hot-news-item:hover .news-thumb img {
	transform: scale(1.1);
}

.news-title {
    font-size: 16px;
	font-weight: bold;
	color: #333;
	line-height: 1.5;
	margin-bottom: 8px;
	overflow: hidden;
	text-overflow: ellipsis;
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
    height: 24px;
}

.news-meta {
	font-size: 14px;
	color: #999;
	display: flex;
	align-items: center;
	gap: 5px;
    height: 52px;
    overflow: hidden;
}

.news-list {
	display: flex;
	flex-direction: column;
	gap: 20px;
    margin-bottom: 10px;
}

.news-item {
	display: flex;
	gap: 20px;
	padding: 30px 20px;
	border-bottom: 1px solid #f0f0f0;
	cursor: pointer;
	transition: all 0.3s ease;
	background: #fff;
}

.news-item:hover {
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
	transform: translateX(5px);
}

.news-image {
	flex-shrink: 0;
	width: 280px;
	height: 180px;
	border-radius: 6px;
	overflow: hidden;
}

.news-image img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	transition: transform 0.3s ease;
}

.news-item:hover .news-image img {
	transform: scale(1.05);
}

.news-content {
	flex: 1;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
}

.news-content .news-title {
	font-size: 20px;
	font-weight: bold;
	color: #333;
	margin-bottom: 12px;
	line-height: 1.4;
}

.news-desc {
	font-size: 14px;
	color: #666;
	line-height: 1.8;
	margin-bottom: 15px;
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
	overflow: hidden;
}

.news-footer {
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.news-info {
	display: flex;
	align-items: center;
	gap: 8px;
	font-size: 13px;
	color: #999;
}

/* 分页 */
.pagination-wrapper {
	margin-top: 30px;
	display: flex;
	justify-content: center;
}
</style>
