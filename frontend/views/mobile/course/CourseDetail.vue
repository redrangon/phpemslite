<template>
	<div style="width:100%;">
		<!-- 页面头部导航 -->
		<van-nav-bar 
			:title="course.cstitle || '课程详情'" 
			left-arrow 
			@click-left="$router.go(-1)" 
			fixed 
			placeholder
		/>
        <div class="card-container">
            <!-- 课程详情内容 -->
            <template v-if="course.csid">
                <!-- 课程基本信息卡片 -->
                <van-cell-group inset class="course-info-card">
                    <!-- 课程标题 -->
                    <div class="course-header">
                        <h1 class="course-title">{{ course.cstitle }}</h1>
                        <!-- 课程元信息 -->
                        <div class="course-meta">
                            <span class="meta-item" v-if="course.cstime">
                                <van-icon name="clock-o" />
                                {{ course.cstime }}
                            </span>
                            <span class="meta-item" v-if="course.csnumber">
                                <van-icon name="friends-o" />
                                {{ course.csnumber }} 人学习
                            </span>
                        </div>
                    </div>
                </van-cell-group>
                <!-- 课程描述 -->
                <van-cell-group inset class="course-describe-card" v-if="course.csdescribe">
                    <van-cell title="课程介绍" title-style="font-weight: bold; font-size: 16px;" />
                    <div class="describe-content" v-html="course.csdescribe"></div>
                </van-cell-group>

                <!-- 课程详情内容 -->
                <van-cell-group inset class="course-detail-content" v-if="course.cscontent || course.csdetail">
                    <van-cell title="课程详情" title-style="font-weight: bold; font-size: 16px;" />
                    <div class="detail-content" v-html="course.cscontent || course.csdetail"></div>
                </van-cell-group>

                <!-- 课件列表 -->
                <van-cell-group inset class="lessons-card" v-if="lessons && lessons.length > 0">
                    <van-cell title="课件列表" title-style="font-weight: bold; font-size: 16px;" />
                    <course-tree :hideProgress="true" v-model:data="lessons"></course-tree>
                </van-cell-group>

                <!-- 空状态 -->
                <van-empty v-else-if="lessons && lessons.length === 0" description="暂无课件" class="lessons-empty" />

                <!-- 底部操作栏 -->
                <div class="bottom-action-bar">
                    <!-- 价格选择 -->
                    <van-cell-group inset v-if="!isPurchased && prices.length > 0">
                        <van-radio-group v-model="selectedPrice">
                            <van-cell-group>
                                <van-cell v-for="price in prices" :key="price.cpid" @click="selectedPrice = price.cpid" class="price-cell" >
                                    <template #title>
                                        <div class="price-info">
                                            <span class="price-days">{{ price.cpdays }}天</span>
                                            <span class="price-amount">仅需 {{ price.cpamount }} 积分</span>
                                        </div>
                                    </template>
                                    <template #right-icon>
                                        <van-radio :name="price.cpid" />
                                    </template>
                                </van-cell>
                            </van-cell-group>
                        </van-radio-group>
                    </van-cell-group>

                    <!-- 操作按钮 -->
                    <div class="action-buttons">
                        <van-button v-if="!isPurchased" type="primary" block size="large" @click="handlePurchase" :disabled="!selectedPrice">
                            立即购买
                        </van-button>
                        <van-button v-else type="success" block size="large" @click="setCourse" >
                            开始学习
                        </van-button>
                    </div>
				</div>
            </template>
        </div>
	</div>
</template>

<script>
import courseApi from '@/framework/api/course.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
import courseTree from '@/components/mobile/CourseTree.vue';

export default {
	mixins: [baseMixin],
	data() {
		return {
			course: {},
			lessons: [],
			isPurchased: false,
			csId: 0,
			selectedPrice: null,
			prices: []
		};
	},
	components: {
		courseTree
	},
	async mounted() {
		this.csId = this.$route.params.csid || 0;
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
				this.isPurchased = this.course.isPurchased ?? false;
				this.prices = await courseApi.getSubjectPrice(this.csId);
				// 默认选择第一个价格选项
				if (this.prices.length > 0) {
					this.selectedPrice = this.prices[0].cpid;
				}
			}, null, '获取课程详情失败');
		},
		
		getCourseTree: async function() {
			const data = await courseApi.getSubjectCourses(this.csId);
			this.lessons = await courseApi.buildCourseTree(data);
		},

		// 购买课程
		handlePurchase() {
            this.confirmOperate('确定要开通本课程吗？',async () => {
                await courseApi.buySubject(this.selectedPrice);
                await this.getData();
            },null,null);
		},

		// 开始学习
		setCourse() {
			this.execute(async () => {
				await courseApi.setCourseSession(this.csId);
				this.$router.push('/mobile/course/course');
			}, null, null);
		},
	}
};
</script>

<style scoped>
.course-detail-page {
    width: 100%;
	min-height: 100vh;
	background: #f5f7fa;
	padding-bottom: 180px;
}

/* 课程信息卡片 */
.course-info-card {
	margin: 10px 0;
	background: #fff;
}

.course-header {
	padding: 20px 16px;
}

.course-title {
	font-size: 20px;
	font-weight: 600;
	color: #333;
	margin-bottom: 15px;
	line-height: 1.4;
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

/* 课程描述卡片 */
.course-describe-card {
	margin: 10px 0;
	background: #fff;
}

.describe-content {
	font-size: 15px;
	line-height: 1.8;
	color: #666;
	padding: 10px 16px 20px;
}

.describe-content :deep(img) {
	max-width: 100%;
	height: auto;
}

/* 课程详情内容 */
.course-detail-content {
	margin: 10px 0;
	background: #fff;
}

.detail-content {
	font-size: 15px;
	line-height: 1.8;
	color: #333;
	padding: 10px 16px 20px;
}

.detail-content :deep(img) {
	max-width: 100%;
	height: auto;
}

.detail-content :deep(p) {
	margin-bottom: 10px;
}

/* 课件列表 */
.lessons-card {
	margin: 10px 0;
	background: #fff;
}

.lessons-empty {
	margin: 40px 0;
	background: #fff;
}

/* 底部操作栏 */
.bottom-action-bar {
	background: #fff;
	box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
	padding: 10px 0;
	z-index: 100;
}

/* 价格选择单元格 */
.price-cell {
	cursor: pointer;
}

.price-info {
	display: flex;
	flex-direction: column;
	gap: 5px;
}

.price-days {
	font-size: 16px;
	font-weight: 600;
	color: #333;
}

.price-amount {
	font-size: 14px;
	color: #ff6034;
}

/* 操作按钮 */
.action-buttons {
	padding: 0 16px;
}
</style>
