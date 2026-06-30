<template>
	<div class="pagecontent">
		<!-- 轮播图区域 -->
        <lay-carousel v-model="activeCarousel" :autoplay="true" :interval="4000" arrow="always" indicator="none" style="margin-bottom: 20px;">
            <lay-carousel-item v-for="(banner, index) in banners" :key="index" :id="index">
                <div class="banner-item">
                    <div class="banner-overlay">
                        <div class="banner-content">
                            <h2 class="banner-title">{{ banner.title }}</h2>
                            <p class="banner-desc">{{ banner.description }}</p>
                            <lay-button type="primary" size="lg" @click="viewDetail(banner)">
                                查看详情
                            </lay-button>
                        </div>
                    </div>
                </div>
            </lay-carousel-item>
        </lay-carousel>

		<!-- 新闻列表 -->
		<lay-card :bordered="false" class="news-list-card">
            <lay-tab v-model="tabCurrent1" type="brief">
                <lay-tab-item id="tc1_1">
                    <template #title>
                        <span class="tabtitle">
                            <span>最新资讯</span>
                        </span>
                    </template>
                    <div v-if="page.total > 0" class="news-list">
                        <div v-for="news in newsList" :key="news.id" class="news-item" @click="viewNewsDetail(news)">
                            <div class="news-image">
                                <img :src="news.contentthumb" :alt="news.contenttitle" />
                            </div>
                            <div class="news-content">
                                <h3 class="news-title">{{ news.contenttitle }}</h3>
                                <p class="news-desc">{{ news.contentdescribe }}</p>
                                <div class="news-footer">
                                    <div class="news-info">
                                        <lay-icon type="layui-icon-username"></lay-icon>
                                        <span>{{ news.contentusername }}</span>
                                        <lay-icon type="layui-icon-time" style="margin-left: 15px;"></lay-icon>
                                        <span>{{ news.contentinputtime }}</span>
                                        <lay-icon type="layui-icon-read" style="margin-left: 15px;"></lay-icon>
                                        <span>{{ news.contentview }} 阅读</span>
                                    </div>
                                    <lay-button v-if="news.catname" type="primary" size="md" @click.stop="viewCategoryDetail(news.contentcatid)">
                                        {{ news.catname }}
                                    </lay-button>
                                </div>
                            </div>
                        </div>
                        <div style="text-align: center;padding:10px;">
                            <router-link to="/desktop/home/content/category/0">
                                <lay-button>更多资讯 &gt;&gt;</lay-button>
                            </router-link>
                        </div>
                    </div>
                    <lay-empty v-else description="暂无资讯数据"></lay-empty>
                </lay-tab-item>
            </lay-tab>
		</lay-card>
	</div>
</template>

<script>
import { ref } from 'vue';
import contentApi from "@/framework/api/content.js";
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
    mixins: [baseMixin],
	data() {
		return {
            tabCurrent1:"tc1_1",
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
			// 分类数据
			categories: [
				{ id: 'all', name: '全部' },
				{ id: 'notice', name: '通知公告' },
				{ id: 'exam', name: '考试动态' },
				{ id: 'policy', name: '政策法规' },
				{ id: 'training', name: '培训信息' }
			],
			// 热门资讯
			hotNews: [],
			// 新闻列表数据
			newsList: [],
			// 分页
            layout:['count', 'prev', 'page', 'next'],
            page:{
                current:1,
                total:0,
                limit:10
            },
		};
	},
	async mounted() {
		await this.getData();
	},
    methods: {
		// 加载新闻列表
        async getData() {
            await this.execute(async () => {
                const data = await contentApi.getContentList({
                    page:this.page.current,
                    limit:this.page.limit
                });
                this.page.current = data.page;
                this.page.limit = data.limit;
                this.page.total = data.total;
                this.newsList = data.data;
            },null,null);
        },
        pageChange:async function({current,limit}){
            this.page.current = current
            this.page.limit = limit
            await this.getData()
        },
		// 查看轮播图详情
		viewDetail(banner) {
            this.$router.push(banner.url);
		},
        viewCategoryDetail(categoryId) {
            this.$router.push(`/desktop/home/content/category/${categoryId}`);
        },
		// 查看新闻详情
		viewNewsDetail(news) {
			this.$router.push(`/desktop/home/content/content/${news.contentid}`);
		}
	}
};
</script>

<style scoped>
.banner-item {
	height: 300px;
	background-size: cover;
	background-position: center;
	position: relative;
}

.banner-overlay {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: linear-gradient(to bottom, rgba(17, 123, 203, 0.81), rgba(17, 123, 203, 1));
	display: flex;
	align-items: center;
	justify-content: center;
}

.banner-content {
	text-align: center;
	color: #fff;
	max-width: 800px;
	padding: 20px;
}

.banner-title {
	font-size: 36px;
	font-weight: bold;
	margin-bottom: 15px;
	text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.banner-desc {
	font-size: 18px;
	margin-bottom: 25px;
	line-height: 1.6;
	text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

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

.top-tag {
	position: absolute;
	top: 8px;
	right: 8px;
}

.news-title {
	font-size: 14px;
	font-weight: bold;
	color: #333;
	line-height: 1.5;
	margin-bottom: 8px;
	overflow: hidden;
	text-overflow: ellipsis;
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
}

.news-meta {
	font-size: 12px;
	color: #999;
	display: flex;
	align-items: center;
	gap: 5px;
}

/* 新闻列表 */

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
	box-shadow: 0 4px 4px rgba(0, 0, 0, 0.1);
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
