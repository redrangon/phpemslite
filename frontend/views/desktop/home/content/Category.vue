<template>
	<lay-card class="pagecontent category-page">
		<!-- 统计信息 -->
        <lay-quote style="position: relative">
            <lay-breadcrumb>
                <lay-breadcrumb-item title="新闻" @click="$router.push('/desktop/home/content')"></lay-breadcrumb-item>
                <lay-breadcrumb-item :title="currentCategory?.catname" v-if="currentCategory?.catid"></lay-breadcrumb-item>
            </lay-breadcrumb>
            <lay-page-header :content="currentCategory?.catname??'所有分类'" @back="$router.go(-1)" class="planbread"></lay-page-header>
        </lay-quote>
		<lay-checkcard-group v-model="multiple" single @change="onCategoryChange()">
			<lay-checkcard class="category-card" :key="currentCategory.catid"
			               :value="-1"
			               title="返回上级"
			               description="返回父分类新闻列表" v-if="currentCategory?.catid">
			</lay-checkcard>
			<template v-if="categories?.length > 0">
				<lay-checkcard class="category-card" v-for="(category,catId) in categories" :key="catId"
				               :value="category.catid"
				               :title="category.catname"
				               :description="category.catdes">
				</lay-checkcard>
			</template>
		</lay-checkcard-group>
		<!-- 资讯列表 - 列表视图 -->
		<lay-card :bordered="false" class="list-card" v-if="viewMode === 'list'">
			<div v-if="contents.length > 0" class="news-list">
				<div v-for="(content,index) in contents" :key="index" class="news-item" @click="viewContentDetail(content)">
					<div class="news-index">{{ index + 1 + ((page.current - 1) * page.limit) }}</div>
					<div class="news-info">
						<h3 class="news-title">
							{{ content.contenttitle }}
						</h3>
						<p class="news-summary">{{ content.summary }}</p>
						<div class="news-meta">
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
								{{ content.contentview }} 阅读
							</span>
						</div>
					</div>
				</div>
			</div>
			<lay-empty v-else description="暂无相关资讯"></lay-empty>
		</lay-card>

		<!-- 资讯列表 - 网格视图 -->
		<lay-card :bordered="false" class="grid-card" v-else-if="viewMode === 'grid'">
			<lay-row :space="15" v-if="contents.length > 0">
				<lay-col v-for="content in contents" :key="content.id" :xs="24" :sm="12" :md="8" :lg="6">
					<lay-card :bordered="false" class="grid-item" @click="viewcontentDetail(content)">
						<div class="grid-thumb">
							<img :src="content.thumbnail" :alt="content.title" />
							<lay-tag v-if="content.isTop" type="danger" size="sm" class="top-tag">置顶</lay-tag>
							<lay-tag v-if="content.isNew" type="success" size="sm" class="new-tag">最新</lay-tag>
						</div>
						<h4 class="grid-title">{{ content.title }}</h4>
						<p class="grid-summary">{{ content.summary }}</p>
						<div class="grid-meta">
							<lay-icon type="layui-icon-time"></lay-icon>
							<span>{{ content.publishTime }}</span>
							<lay-icon type="layui-icon-read" style="margin-left: 8px;"></lay-icon>
							<span>{{ content.views }}</span>
						</div>
					</lay-card>
				</lay-col>
			</lay-row>
			<lay-empty v-else description="暂无相关资讯"></lay-empty>
		</lay-card>

		<!-- 分页 -->
        <div class="pagination-wrapper" v-if="page.total > 0">
            <lay-page v-model="page.current"  :layout="layout" v-model:limit="page.limit" :total="page.total" @change="pageChange" theme="blue" style="float:right">
            </lay-page>
        </div>

		<!-- 回到顶部 -->
		<lay-backtop></lay-backtop>
	</lay-card>
</template>

<script>
import contentApi from '@/framework/api/content.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
	data() {
		return {
			categoryId:0,
			multiple:0,
			currentCategory: {},
			categories: [],
			viewMode: 'list', // list 或 grid
			contents: [],
            layout:['count', 'prev', 'page', 'next'],
            page:{
                current:1,
                total:0,
                limit:10
            },
		};
	},
	methods: {
		async getCategories(){
			this.categories = await contentApi.getCategoryList(this.categoryId);
		},
		async getCategory(){
			this.currentCategory = await contentApi.getCategory(this.categoryId);
		},
		// 加载新闻列表
		async getData() {
			await this.execute(async () => {
				const data = await contentApi.getContentList({
					catId:this.categoryId
				})
				this.contents = data.data;
				this.page.current = data.page;
				this.page.limit = data.limit;
				this.page.total = data.total;
			},null,null)
		},

		// 分类切换
		async onCategoryChange() {
			if(this.multiple === 0)return;
			if(this.multiple === -1)
			{
				this.$router.push(`/desktop/home/content/category/${this.currentCategory.catparent}`);
			}
			else
			{
				this.$router.push(`/desktop/home/content/category/${this.multiple}`);
				return;
				await this.execute(async () => {
					const data = await contentApi.getCategoryList(this.multiple);
					if(data?.length > 0){
						this.$router.push(`/desktop/home/content/category/${this.multiple}`);
					}
					else
					{
						this.categoryId = this.multiple;
						await this.getData();
					}
				},null,null)
			}
		},
		pageChange:async function({current,limit}){
			this.page.current = current
			this.page.limit = limit
			await this.getData()
		},
		// 查看新闻详情
		viewContentDetail(content) {
			this.$router.push(`/desktop/home/content/content/${content.contentid}`);
		}
	},
	watch: {
		'$route.params': {
			async handler() {
				this.categoryId = this.$route.params.categoryId;
				this.multiple = 0;
				await this.getCategory();
				await this.getCategories();
				await this.getData();
			},
			deep: true,
			immediate: true
		}
	}
};
</script>
<style src="@/assets/css/desktop/category.css"></style>
