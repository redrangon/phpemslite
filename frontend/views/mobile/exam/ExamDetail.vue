<template>
	<div style="width:100%;">
		<!-- 页面头部导航 -->
		<van-nav-bar :title="basic.basic || '考场详情'" left-arrow @click-left="$router.go(-1)" fixed placeholder />
        <div class="card-container">
            <!-- 课程详情内容 -->
            <template v-if="basic.basicid">
                <!-- 课程基本信息卡片 -->
                <van-cell-group inset class="course-info-card">
                    <!-- 课程标题 -->
                    <div class="course-header">
                        <h1 class="course-title">{{ basic.basic }}</h1>
                        <!-- 课程元信息 -->
                        <div class="course-meta">
                            <span class="meta-item">
                                <van-icon name="friends-o" />
                                {{ basic.basicnumber }} 人学习
                            </span>
                        </div>
                    </div>
                </van-cell-group>
                <!-- 课程描述 -->
                <van-cell-group inset class="course-describe-card" v-if="basic.basicdescribe">
                    <van-cell title="考场介绍" title-style="font-weight: bold; font-size: 16px;" />
                    <div class="describe-content" v-html="basic.basicdescribe"></div>
                </van-cell-group>

                <!-- 底部操作栏 -->
                <div class="bottom-action-bar">
                    <!-- 价格选择 -->
                    <van-cell-group inset v-if="!isPurchased && prices.length > 0">
                        <van-radio-group v-model="selectedPrice">
                            <van-cell-group>
                                <van-cell v-for="price in prices" :key="price.epid" @click="selectedPrice = price.epid" class="price-cell" >
                                    <template #title>
                                        <div class="price-info">
                                            <span class="price-days">{{ price.epdays }}天</span>
                                            <span class="price-amount">仅需 {{ price.epamount }} 积分</span>
                                        </div>
                                    </template>
                                    <template #right-icon>
                                        <van-radio :name="price.epid" />
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
                        <van-button v-else type="success" block size="large" @click="setExam" >
                            开始学习
                        </van-button>
                    </div>
				</div>
            </template>
        </div>
	</div>
</template>

<script>
import examApi from '@/framework/api/exam.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {useAuthStore} from "@/stores/auth.js";

export default {
	mixins: [baseMixin],
    setup() {
        const store = useAuthStore();
        return {store}
    },
	data() {
		return {
            basic: {},
            isPurchased: false,
            basicId: 0,
			selectedPrice: null,
			prices: []
		};
	},
	async mounted() {
		this.basicId = this.$route.params.basicid || 0;
		if (this.basicId) {
			await this.getData();
		}
	},
	methods: {
		// 获取课程详情
		async getData() {
			await this.execute(async () => {
				this.basic = await examApi.getBasic(this.basicId);
				this.isPurchased = this.basic.isPurchased ?? false;
				this.prices = await examApi.getBasicPrice(this.basicId);
				// 默认选择第一个价格选项
				if (this.prices.length > 0) {
					this.selectedPrice = this.prices[0].cpid;
				}
			}, null, '获取考场详情失败');
		},
		// 购买课程
		handlePurchase() {
            this.confirmOperate('确定要开通本课程吗？',async () => {
                await examApi.buyBasic(this.selectedPrice);
                await this.getData();
            },null,null);
		},
		// 开始学习
        setExam() {
            this.execute( async () =>{
                await examApi.setExamSession(this.basicId);
                await this.store.setBasic();
                this.$router.push('/mobile/exam/basic');
            },null,null);
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
