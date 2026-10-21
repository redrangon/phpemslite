<template>
    <lay-side class="mySide" style="margin-top: 10px;">
        <lay-menu :tree="true" class="myMenu" :selected-key="selectedKey" theme="light" @changeSelectedKey="onCategoryChange">
            <lay-menu-item :id="category.catid" v-for="category in categories" :key="category.catid">
                <lay-icon type="layui-icon-down" v-if="category?.isRoot"></lay-icon>
                <lay-icon type="layui-icon-up" v-else-if="category?.isTop"></lay-icon>
                <lay-icon type="layui-icon-file" v-else></lay-icon>
                {{category.catname}}
            </lay-menu-item>
        </lay-menu>
    </lay-side>
    <lay-body class="frontLayBody">
        <lay-card class="pagecontent category-page">
            <!-- 页面标题 -->
            <lay-quote style="position: relative">
                <lay-breadcrumb>
                    <lay-breadcrumb-item title="课程" @click="$router.push('/desktop/home/course')"></lay-breadcrumb-item>
                    <lay-breadcrumb-item :title="currentCategory?.catname" v-if="currentCategory?.catid"></lay-breadcrumb-item>
                </lay-breadcrumb>
                <lay-page-header :content="currentCategory?.catname??'全部分类'" @back="$router.go(-1)" class="planbread"></lay-page-header>
            </lay-quote>
            <lay-card :bordered="false" class="list-card" v-if="viewMode === 'list'">
                <div v-if="courses.length > 0" class="news-list">
                    <div v-for="(course,index) in courses" :key="course.csid" class="news-item" @click="viewCourseDetail(course)">
                        <div class="news-index">{{ index + 1 + max((page.current - 1) * page.limit,0) }}</div>
                        <div class="news-info">
                            <h3 class="news-title">
                                {{ course.cstitle }}
                            </h3>
                            <p class="news-summary">{{ course.csdescribe }}</p>
                            <div class="news-meta">
                                <span class="meta-item">
                                    <lay-icon type="layui-icon-time"></lay-icon>
                                    {{ course.cstime }}
                                </span>
                                <span class="meta-item">
                                    <lay-icon type="layui-icon-read"></lay-icon>
                                    {{ course.csdemo }} 阅读
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <lay-empty v-else description="暂无相关资讯"></lay-empty>
            </lay-card>

            <!-- 资讯列表 - 网格视图 -->
            <lay-card :bordered="false" class="grid-card" v-else-if="viewMode === 'grid'">
                <lay-row :space="15" v-if="courses.length > 0">
                    <lay-col v-for="course in courses" :key="course.csid" :xs="24" :sm="12" :md="8" :lg="6">
                        <lay-card :bordered="false" class="grid-item" @click="viewCourseDetail(course)">
                            <div class="grid-thumb">
                                <img :src="course.csthumb" :alt="course.cstitle" />
                            </div>
                            <h4 class="grid-title">{{ course.cstitle }}</h4>
                            <div class="grid-meta">
                                <lay-icon type="layui-icon-time"></lay-icon>
                                <span>{{ course.cstime }}</span>
                                <lay-icon type="layui-icon-read" style="margin-left: 8px;"></lay-icon>
                                <span>{{ course.cscatid }}</span>
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
    </lay-body>
</template>

<script>
import courseApi from '@/framework/api/course.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
	data() {
		return {
			categoryId:0,
            selectedKey:0,
			currentCategory: {},
			categories: [],
			viewMode: 'grid', // list 或 grid
			filter: {
				timeRange: 'all',
				sortBy: 'latest',
				keyword: ''
			},
			courses: [],
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
			this.categories = await courseApi.getCategoryList(this.categoryId);
		},
		async getCategory(){
			this.currentCategory = await courseApi.getCategory(this.categoryId);
		},
		// 加载新闻列表
		async getData() {
			await this.execute(async () => {
				const data = await courseApi.getSubjectList({
					catId:this.categoryId
				})
				this.courses = data.data;
				this.page.current = data.page;
				this.page.limit = data.limit;
				this.page.total = data.total;
			},null,null)
		},

		// 分类切换
		async onCategoryChange(catId) {
            this.selectedKey = catId;
            await this.execute(async () => {
                const data = await courseApi.getCategoryList(this.selectedKey);
                if(data?.length > 0){
                    this.$router.push(`/desktop/home/course/category/${this.selectedKey}`);
                }
                else
                {
                    this.categoryId = this.selectedKey;
                    await this.getData();
                }
            },null,null)
		},
		pageChange:async function({current,limit}){
			this.page.current = current
			this.page.limit = limit
			await this.getData()
		},
		// 查看新闻详情
		viewCourseDetail(course) {
			this.$router.push({
				name: 'home.course.detail',
				params: {
					csId: course.csid
				}
			});
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
