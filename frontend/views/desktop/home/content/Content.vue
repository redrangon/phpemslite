<template>
	<lay-card class="pagecontent content-detail">
        <lay-card style="position: relative">
            <lay-breadcrumb style="line-height: 32px">
                <lay-breadcrumb-item title="新闻" @click="$router.push('/desktop/home/content')"></lay-breadcrumb-item>
                <lay-breadcrumb-item :title="category?.catname" @click="$router.push('/desktop/home/content/category/'+category?.catid)"></lay-breadcrumb-item>
            </lay-breadcrumb>
            <lay-page-header :content="category?.catname??'所有分类'" @back="$router.go(-1)" class="planbread"></lay-page-header>
        </lay-card>

		<!-- 内容加载中 -->
		<lay-card v-if="loading" :bordered="false" class="loading-card">
			<lay-loading></lay-loading>
		</lay-card>

		<!-- 内容详情 -->
		<lay-card v-else-if="content" :bordered="false" class="content-card">
			<!-- 标题区域 -->
			<div class="content-header">
				<h1 class="content-title">
                    {{ content.contenttitle }}
                </h1>
				<div class="content-meta">
					<span class="meta-item">
						<lay-icon type="layui-icon-username"></lay-icon>
						{{ content.contentusername }}
					</span>
					<span class="meta-item">
						<lay-icon type="layui-icon-time"></lay-icon>
						{{ content.contentinputtime }}
					</span>
					<span class="meta-item">
						<lay-icon type="layui-icon-read"></lay-icon>
						{{ content.contentview }} 次阅读
					</span>
					<lay-tag v-if="content.category" type="primary" size="sm" style="margin-left: 10px;">
						{{ content.category }}
					</lay-tag>
				</div>
			</div>

			<!-- 摘要 -->
			<div v-if="content.summary" class="content-summary">
				<lay-icon type="layui-icon-note" style="color: #ff5722;"></lay-icon>
				{{ content.contentdescribe }}
			</div>

			<!-- 封面图 -->
			<div v-if="content.contentthumb" class="content-cover" style="display: none;">
				<img :src="content.contentthumb" :alt="content.contenttitle" />
			</div>

			<!-- 分割线 -->
			<lay-line></lay-line>

			<!-- 正文内容 -->
			<div class="content-body" v-html="content.contenttext"></div>

			<!-- 标签 -->
			<div v-if="content.tags && content.tags.length > 0" class="content-tags" style="display: none;">
				<span class="tag-label">标签：</span>
				<lay-tag v-for="tag in content.tags" :key="tag" type="normal" size="sm" style="margin-right: 8px;">
					{{ tag }}
				</lay-tag>
			</div>

			<!-- 分享区域 -->
			<div class="content-share" style="display: none;">
				<span class="share-label">分享到：</span>
				<lay-button size="sm" type="primary">
					<lay-icon type="layui-icon-share"></lay-icon>
					微信
				</lay-button>
				<lay-button size="sm" type="danger">
					<lay-icon type="layui-icon-share"></lay-icon>
					微博
				</lay-button>
				<lay-button size="sm" type="normal">
					<lay-icon type="layui-icon-share"></lay-icon>
					QQ
				</lay-button>
			</div>
		</lay-card>

		<!-- 上一篇/下一篇 -->
		<lay-card v-if="content" :bordered="false" class="nav-card" style="display: none">
			<lay-row :space="15">
				<lay-col :xs="24" :sm="12">
					<div class="nav-item prev" @click="goToPrev" v-if="prevArticle">
						<span class="nav-label">上一篇</span>
						<h4 class="nav-title">{{ prevArticle.title }}</h4>
					</div>
					<div class="nav-item disabled" v-else>
						<span class="nav-label">上一篇</span>
						<p class="nav-text">没有了</p>
					</div>
				</lay-col>
				<lay-col :xs="24" :sm="12">
					<div class="nav-item next" @click="goToNext" v-if="nextArticle">
						<span class="nav-label">下一篇</span>
						<h4 class="nav-title">{{ nextArticle.title }}</h4>
					</div>
					<div class="nav-item disabled" v-else>
						<span class="nav-label">下一篇</span>
						<p class="nav-text">没有了</p>
					</div>
				</lay-col>
			</lay-row>
		</lay-card>

		<!-- 相关资讯 -->
		<lay-card v-if="content" :bordered="false" class="related-card" style="display: none">
			<template #title>
				<lay-icon type="layui-icon-align-left" style="color: #1e9fff; margin-right: 8px;"></lay-icon>
				<span>相关资讯</span>
			</template>
			<lay-row :space="15">
				<lay-col v-for="article in relatedArticles" :key="article.id" :xs="24" :sm="12" :md="8">
					<lay-card :bordered="false" class="related-item" @click="viewArticle(article)">
						<div class="related-thumb">
							<img :src="article.thumbnail" :alt="article.title" />
						</div>
						<h4 class="related-title">{{ article.title }}</h4>
						<div class="related-meta">
							<lay-icon type="layui-icon-time"></lay-icon>
							<span>{{ article.publishTime }}</span>
						</div>
					</lay-card>
				</lay-col>
			</lay-row>
		</lay-card>
	</lay-card>
</template>

<script>
import baseMixin from "@/framework/mixins/baseMixin.js";
import contentApi from "@/framework/api/content.js";
export default {
    mixins: [baseMixin],
	data() {
		return {
			loading: true,
            category:{},
            contentId:0,
			content: null,
			prevArticle: null,
			nextArticle: null,
			relatedArticles: []
		};
	},
	async mounted() {
        this.contentId  = this.$route.params.contentId;
		await this.getData();
	},
	methods: {
		// 加载内容
		async getData() {
            await this.execute(async () => {
                this.loading = true;

                const data = await contentApi.getContent(this.contentId)
                this.category = await contentApi.getCategory(data.contentcatid);
                    // 模拟上一篇
                const mockPrev = {
                    id: data.contentid - 1,
                    title: '2026年下半年职业资格考试计划安排公告'
                };

                // 模拟下一篇
                const mockNext = {
                    id: data.contentid + 1,
                    title: '新版职业技能等级认定标准正式发布'
                };

                // 模拟相关资讯
                const mockRelated = [
                    {
                        id: 2,
                        title: '2026年下半年职业资格考试计划安排公告',
                        thumbnail: 'https://picsum.photos/300/200?random=21',
                        publishTime: '2026-06-19'
                    },
                ];

                this.content = data;
                this.prevArticle = data.contentid > 1 ? mockPrev : null;
                this.nextArticle = mockNext;
                this.relatedArticles = mockRelated;
                this.loading = false;
            }, null,null);
		},

		// 返回
		goBack() {
			this.$router.go(-1);
		},

		// 查看上一篇
		goToPrev() {
			if (this.prevArticle) {
				this.$router.push(`/desktop/home/content/content/${this.prevArticle.id}`);
			}
		},

		// 查看下一篇
		goToNext() {
			if (this.nextArticle) {
				this.$router.push(`/desktop/home/content/detail/${this.nextArticle.id}`);
				this.loadContent();
			}
		},

		// 查看相关文章
		viewArticle(article) {
			this.$router.push(`/desktop/home/content/detail/${article.id}`);
			this.loadContent();
		}
	}
};
</script>

<style scoped>

.content-detail {
	margin-bottom: 20px;
}

/* 返回按钮 */
.back-bar {
	margin-bottom: 20px;
}

/* 加载中 */
.loading-card {
	text-align: center;
	padding: 60px 0;
}

/* 内容卡片 */
.content-card {
	padding: 30px;
}

.content-header {
	margin-bottom: 25px;
}

.content-title {
	font-size: 28px;
	font-weight: bold;
	color: #333;
	line-height: 1.4;
	margin-bottom: 15px;
	text-align: center;
}

.content-meta {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 20px;
	color: #999;
	font-size: 14px;
	flex-wrap: wrap;
}

.meta-item {
	display: flex;
	align-items: center;
	gap: 5px;
}

/* 摘要 */
.content-summary {
	background: #fff8e1;
	border-left: 4px solid #ff9800;
	padding: 15px 20px;
	margin-bottom: 25px;
	color: #666;
	line-height: 1.8;
	font-size: 15px;
}

/* 封面图 */
.content-cover {
	margin-bottom: 25px;
	border-radius: 8px;
	overflow: hidden;
}

.content-cover img {
	width: 100%;
	height: auto;
	display: block;
}

/* 正文内容 */
.content-body {
	line-height: 2;
	font-size: 16px;
	color: #333;
	padding: 20px 0;
}

.content-body h3 {
	font-size: 20px;
	font-weight: bold;
	color: #333;
	margin: 25px 0 15px 0;
	padding-bottom: 10px;
	border-bottom: 2px solid #1e9fff;
}

.content-body p {
	margin-bottom: 15px;
	text-indent: 2em;
}

.content-body ul {
	margin-left: 40px;
	margin-bottom: 15px;
}

.content-body li {
	margin-bottom: 8px;
}

/* 标签 */
.content-tags {
	margin-top: 30px;
	padding-top: 20px;
	border-top: 1px solid #f0f0f0;
	display: flex;
	align-items: center;
	flex-wrap: wrap;
}

.tag-label {
	font-size: 14px;
	color: #666;
	margin-right: 10px;
	font-weight: bold;
}

/* 分享 */
.content-share {
	margin-top: 20px;
	padding-top: 20px;
	border-top: 1px solid #f0f0f0;
	display: flex;
	align-items: center;
	gap: 10px;
}

.share-label {
	font-size: 14px;
	color: #666;
	margin-right: 5px;
}

/* 上下篇导航 */
.nav-card {
	margin-top: 20px;
}

.nav-item {
	padding: 20px;
	background: #f9f9f9;
	border-radius: 6px;
	cursor: pointer;
	transition: all 0.3s ease;
	min-height: 80px;
}

.nav-item:hover:not(.disabled) {
	background: #e6f7ff;
	box-shadow: 0 2px 8px rgba(30, 159, 255, 0.2);
}

.nav-item.disabled {
	cursor: not-allowed;
	opacity: 0.5;
}

.nav-label {
	display: block;
	font-size: 12px;
	color: #999;
	margin-bottom: 8px;
}

.nav-title {
	font-size: 14px;
	color: #333;
	line-height: 1.5;
	margin: 0;
}

.nav-text {
	font-size: 14px;
	color: #999;
	margin: 0;
}

/* 相关资讯 */
.related-card {
	margin-top: 20px;
}

.related-item {
	cursor: pointer;
	transition: all 0.3s ease;
	margin-bottom: 15px;
}

.related-item:hover {
	transform: translateY(-5px);
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.related-thumb {
	height: 120px;
	overflow: hidden;
	border-radius: 4px;
	margin-bottom: 10px;
}

.related-thumb img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	transition: transform 0.3s ease;
}

.related-item:hover .related-thumb img {
	transform: scale(1.1);
}

.related-title {
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

.related-meta {
	font-size: 12px;
	color: #999;
	display: flex;
	align-items: center;
	gap: 5px;
}

/* 响应式 */
@media (max-width: 768px) {
	.content-title {
		font-size: 22px;
	}

	.content-meta {
		gap: 10px;
		font-size: 12px;
	}

	.content-body {
		font-size: 15px;
	}

	.content-card {
		padding: 20px;
	}
}
</style>
