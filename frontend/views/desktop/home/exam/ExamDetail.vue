<template>
	<lay-card class="pagecontent basic-detail-page">
		<!-- 页面头部 -->

        <lay-quote style="position: relative">
            <lay-breadcrumb>
                <lay-breadcrumb-item title="考试" @click="$router.push('/desktop/home/exam')"></lay-breadcrumb-item>
            </lay-breadcrumb>
            <lay-page-header :content="basic?.basic??'所有考场'" @back="$router.go(-1)" class="planbread"></lay-page-header>
        </lay-quote>
		<lay-row :space="20" v-if="basic.basicid">
			<!-- 左侧：课程信息 -->
			<lay-col :xs="24" :sm="24" :md="16" :lg="16">
				<!-- 课程基本信息卡片 -->
				<lay-card :bordered="false" class="basic-info-card">
					<!-- 课程缩略图 -->
					<!-- 课程标题和描述 -->
					<div class="basic-header">
						<h1 class="basic-title">{{ basic.basic }}</h1>
					</div>
                    <div class="basic-describe" v-if="basic.basicdescribe">
                        <div v-html="basic.basicdescribe" class="describe-content"></div>
                    </div>
				</lay-card>

				<!-- 课程详情内容 -->
				<lay-card :bordered="false" class="basic-content-card" v-if="basic?.basictext">
					<div class="detail-content">
						<div v-html="basic.basictext"></div>
					</div>
				</lay-card>
			</lay-col>

			<!-- 右侧：操作区域 -->
			<lay-col :xs="24" :sm="24" :md="8" :lg="8">
				<lay-card :bordered="false" class="basic-action-card">
					<!-- 课程状态 -->
					<div class="basic-status">
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
                            <template v-for="price in prices" :key="price.epid">
                                <lay-checkcard :value="price.epid">
                                    <template #title>
                                        <div style="font-size: 18px;font-weight: 600;"> {{price.epdays}}天</div>
                                    </template>
                                    <template #description>
                                        <div style="text-align: left">仅需{{price.epamount}}积分</div>
                                    </template>
                                </lay-checkcard>
                            </template>
                        </lay-checkcard-group>
                        <lay-button type="primary" block size="lg" @click="handlePurchase" v-if="!isPurchased">
							<lay-icon type="layui-icon-cart" style="margin-right: 5px;"></lay-icon>
							立即购买
						</lay-button>
						<lay-button type="primary" block size="lg" @click="setExam()" v-else>
							<lay-icon type="layui-icon-edit" style="margin-right: 5px;"></lay-icon>
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
            multiple:0,
            prices:[{
                epid:1,
                epdays:30,
                epamount:100
            },{
                epid:2,
                epdays:180,
                epamount:300
            },{
                epid:3,
                epdays:360,
                epamount:500
            }]
		};
	},
	async mounted() {
		this.basicId = this.$route.params.basicId || this.$route.query.basicId || 0;
		if (this.basicId) {
			await this.getData();
		}
	},
	methods: {
		// 获取课程详情
		async getData() {
			await this.execute(async () => {
                this.basic = await examApi.getBasic(this.basicId);
                this.isPurchased = this.basic.isPurchased??false
                this.prices = await examApi.getBasicPrice(this.basicId);
			}, null, '获取考场详情失败');
		},

		// 购买课程
        handlePurchase() {
			this.confirmOperate('确定要开通本考场吗？',async () => {
                await examApi.buyBasic(this.multiple);
                await this.getData();
            },null);
		},

		// 开始学习
		setExam() {
            this.execute( async () =>{
                await examApi.setExamSession(this.basicId);
                await this.store.setBasic();
                this.$router.push('/desktop/home/exam/basic');
            },null,null);
        },
	}
};
</script>

<style scoped>
.basic-detail-page {
	min-height: 100vh;
	background: #f5f7fa;
}

/* 课程信息卡片 */
.basic-info-card {
	margin-bottom: 20px;
	background: #fff;
	border-radius: 4px;
    padding:10px;
}

.basic-header {
	margin-bottom: 20px;
}

.basic-title {
	font-size: 20px;
	font-weight: 600;
	color: #333;
	margin-bottom: 15px;
	line-height: 2;
}

.basic-meta {
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

/* 课程详情内容 */
.basic-content-card {
	margin-bottom: 20px;
	background: #fff;
	border-radius: 4px;
}

.detail-content {
	font-size: 15px;
	line-height: 1.8;
	color: #333;
	padding: 10px;
}

.detail-content :deep(img) {
	max-width: 100%;
	height: auto;
}

.detail-content :deep(p) {
	margin-bottom: 10px;
}

/* 操作区域 */
.basic-action-card {
	background: #fff;
	border-radius: 4px;
	position: sticky;
	top: 20px;
}

.basic-status {
	margin-bottom: 20px;
	text-align: center;
}

.action-buttons {
	margin-bottom: 20px;
}
</style>
