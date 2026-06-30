<template>
	<lay-card class="pagecontent course-detail-page">
		<!-- 页面头部 -->

        <lay-quote style="position: relative">
            <lay-breadcrumb>
                <lay-breadcrumb-item title="课程" @click="$router.push('/desktop/home/course')"></lay-breadcrumb-item>
                <lay-breadcrumb-item :title="currentCategory?.catname" v-if="currentCategory?.catid"></lay-breadcrumb-item>
            </lay-breadcrumb>
            <lay-page-header :content="course.cstitle" @back="$router.go(-1)" class="planbread"></lay-page-header>
        </lay-quote>

		<lay-row :space="20" v-if="course.csid">
			<!-- 左侧：课程信息 -->
			<lay-col :xs="24" :sm="24" :md="16" :lg="16">
				<!-- 课程基本信息卡片 -->
				<lay-card :bordered="false" class="course-info-card">
					<!-- 课程标题和描述 -->
					<div class="course-header">
						<h1 class="course-title">{{ course.cstitle }}</h1>
						<div class="course-meta">
							<span class="meta-item" v-if="course.cstime">
								<lay-icon type="layui-icon-date"></lay-icon>
								{{ course.cstime }}
							</span>
							<span class="meta-item" v-if="course.csnumber">
								<lay-icon type="layui-icon-read"></lay-icon>
								{{ course.csnumber }} 人学习
							</span>
						</div>
					</div>

					<!-- 课程描述 -->
					<div class="course-describe" v-if="course.csdescribe">
						<div v-html="course.csdescribe" class="describe-content"></div>
					</div>
				</lay-card>

				<!-- 课程详情内容 -->
				<lay-card :bordered="false" class="course-content-card" v-if="course.cscontent || course.csdetail">
					<lay-quote>课程详情</lay-quote>
					<div class="detail-content">
						<div v-html="course.cscontent || course.csdetail"></div>
					</div>
				</lay-card>

				<!-- 课程课件列表 -->
				<lay-card :bordered="false" class="course-lessons-card" v-if="lessons && lessons.length > 0">
                    <courseTree :hideProgress="true" v-model:data="lessons"></courseTree>
				</lay-card>

				<lay-empty 
					v-else-if="lessons && lessons.length === 0" 
					description="暂无课件"
					class="lessons-empty"
				></lay-empty>
			</lay-col>

			<!-- 右侧：操作区域 -->
			<lay-col :xs="24" :sm="24" :md="8" :lg="8">
				<lay-card :bordered="false" class="course-action-card">
					<!-- 课程状态 -->
					<div class="course-status">
						<lay-result
							v-if="!isPurchased"
							title="未开通课程"
							sub-title="购买后即可开始学习"
                            describe=""
                            status="failure"
						>
							<template #icon>
								<lay-icon type="layui-icon-star" size="48px" color="#1E9FFF"></lay-icon>
							</template>
						</lay-result>
						<lay-result
							v-else
							title="已开通课程"
							sub-title="开始您的学习之旅吧"
                            describe="开始您的学习之旅吧"
						>
							<template #icon>
								<lay-icon type="layui-icon-ok-circle" size="48px" color="#5FB878"></lay-icon>
							</template>
						</lay-result>
					</div>

					<!-- 操作按钮 -->
					<div class="action-buttons" style="text-align: center;margin-bottom: 10px;">
                        <lay-checkcard-group v-model="multiple" single v-if="!isPurchased">
                            <template v-for="price in prices" :key="price.cpid">
                                <lay-checkcard :value="price.cpid">
                                    <template #title>
                                        <div style="font-size: 18px;font-weight: 600;"> {{price.cpdays}}天</div>
                                    </template>
                                    <template #description>
                                        <div style="text-align: left">仅需{{price.cpamount}}积分</div>
                                    </template>
                                </lay-checkcard>
                            </template>
                        </lay-checkcard-group>
                        <lay-button type="primary" block size="lg" @click="handlePurchase" v-if="!isPurchased">
							<lay-icon type="layui-icon-cart" style="margin-right: 5px;"></lay-icon>
							立即购买
						</lay-button>
						<lay-button type="primary" block size="lg" @click="setCourse()" v-else >
							<lay-icon type="layui-icon-play" style="margin-right: 5px;"></lay-icon>
							开始学习
						</lay-button>
					</div>
				</lay-card>
			</lay-col>
		</lay-row>

		<!-- 回到顶部 -->
		<lay-backtop></lay-backtop>
	</lay-card>
</template>

<script>
import courseApi from '@/framework/api/course.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
import courseTree from '@/components/desktop/CourseTree.vue';
import examApi from "@/framework/api/exam.js";

export default {
	mixins: [baseMixin],
	data() {
		return {
			course: {},
			lessons: [],
			isPurchased: false,
			csId: 0,
            multiple:0,
            prices:[{
                cpid:1,
                cpdays:30,
                cpamount:100
            },{
                cpid:2,
                cpdays:180,
                cpamount:300
            },{
                cpid:3,
                cpdays:360,
                cpamount:500
            }]
		};
	},
    components:{
        courseTree
    },
	async mounted() {
		this.csId = this.$route.params.csId || this.$route.query.csId || 0;
		if (this.csId) {
			await this.getData();
		}
	},
	methods: {
		// 获取课程详情
		async getData() {
			await this.execute(async () => {
                this.course = await courseApi.getSubject(this.csId);
                await this.getCourseTree();
                this.isPurchased = this.course.isPurchased??false;
                this.prices = await courseApi.getSubjectPrice(this.csId);
			}, null, '获取课程详情失败');
		},
        getCourseTree: async function() {
            const data = await courseApi.getSubjectCourses(this.csId);
            this.lessons = await courseApi.buildCourseTree(data);
        },

		// 购买课程
        handlePurchase() {
	        this.confirmOperate('确定要开通本课程吗？',async () => {
                await courseApi.buySubject(this.multiple);
                await this.getData();
            },null,null);
        },

		// 开始学习
		setCourse() {
            this.execute( async () =>{
                await courseApi.setCourseSession(this.csId);
                this.$router.push('/desktop/home/course/course');
            },null,null);
        },
	}
};
</script>

<style scoped>
.course-detail-page {
	min-height: 100vh;
	background: #f5f7fa;
}

/* 课程信息卡片 */
.course-info-card {
	margin-bottom: 20px;
	background: #fff;
	border-radius: 4px;
    padding:10px;
}

.course-header {
	margin-bottom: 20px;
}

.course-title {
	font-size: 20px;
	font-weight: 600;
	color: #333;
	margin-bottom: 15px;
	line-height: 2;
}

.course-meta {
	display: flex;
	align-items: center;
	gap: 20px;
	font-size: 14px;
	color: #999;
}

.meta-item {
	display: flex;
	align-items: center;
	gap: 5px;
}

.course-describe {
	margin-top: 20px;
}

.describe-content {
	font-size: 15px;
	line-height: 1.8;
	color: #666;
	padding: 10px 0;
}

/* 课程详情内容 */
.course-content-card {
	margin-bottom: 20px;
	background: #fff;
	border-radius: 4px;
}

.detail-content {
	font-size: 15px;
	line-height: 1.8;
	color: #333;
	padding: 10px 0;
}

.detail-content :deep(img) {
	max-width: 100%;
	height: auto;
}

.detail-content :deep(p) {
	margin-bottom: 10px;
}

/* 课件列表 */
.course-lessons-card {
	margin-bottom: 20px;
	background: #fff;
	border-radius: 4px;
}

.lessons-empty {
    margin: 0 0 20px;
    background: #fff;
	padding: 40px 0;
}

/* 操作区域 */
.course-action-card {
	background: #fff;
	border-radius: 4px;
	position: sticky;
	top: 20px;
}

.course-status {
	margin-bottom: 20px;
	text-align: center;
}

.action-buttons {
	margin-bottom: 20px;
}

/* 加载状态 */
.loading-spin {
	display: flex;
	justify-content: center;
	align-items: center;
	min-height: 400px;
}
</style>
