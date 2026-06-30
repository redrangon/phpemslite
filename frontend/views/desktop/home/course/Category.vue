<template>
	<lay-card class="pagecontent category-page">
		<!-- 页面标题 -->
        <lay-quote style="position: relative">
            <lay-breadcrumb>
	            <lay-breadcrumb-item title="课程" @click="$router.push('/desktop/home/course')"></lay-breadcrumb-item>
	            <lay-breadcrumb-item :title="currentCategory?.catname" v-if="currentCategory?.catid"></lay-breadcrumb-item>
            </lay-breadcrumb>
            <lay-page-header :content="currentCategory?.catname" @back="$router.go(-1)" class="planbread"></lay-page-header>
        </lay-quote>
		<lay-checkcard-group v-model="multiple" single v-if="categories?.length > 0" @change="onCategoryChange()">
            <lay-checkcard class="category-card" v-for="(category,catId) in categories" :key="catId"
                :value="category.catid"
                :title="category.catname"
                :description="category.catdes">
            </lay-checkcard>
            <lay-checkcard class="category-card" :key="currentCategory.catid"
                :value="-1"
                title="返回上级"
                description="返回父分类课程列表" v-if="currentCategory?.catid">
            </lay-checkcard>
        </lay-checkcard-group>
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
</template>

<script>
import courseApi from '@/framework/api/course.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
	data() {
		return {
			categoryId:0,
			multiple:0,
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
		async onCategoryChange() {
			if(this.multiple === 0)return;
			if(this.multiple === -1)
			{
				this.$router.push(`/desktop/home/course/category/${this.currentCategory.catparent}`);
			}
			else
			{
				await this.execute(async () => {
					const data = await courseApi.getCategoryList(this.multiple);
					if(data?.length > 0){
						this.$router.push(`/desktop/home/course/category/${this.multiple}`);
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
