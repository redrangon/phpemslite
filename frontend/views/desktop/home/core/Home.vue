<template>
    <lay-card class="pagecontent">
        <lay-card>
            <lay-tab v-model="tabCurrent1" type="brief">
                <lay-tab-item id="tc1_1">
                    <template #title>
                        <span class="tabtitle">我的积分</span>
                    </template>
                    <div style="margin-top: 10px;">
                        <lay-card class="points-card">
                            <lay-row space="20">
                                <lay-col sm="16" xs="24">
                                    <div class="points-info">
                                        <div class="points-content">
                                            <div class="points-value">{{ userPoints || 0 }}</div>
                                        </div>
                                    </div>
                                </lay-col>
                                <lay-col sm="8" xs="24" style="display: flex; align-items: center; justify-content: center;">
                                    <lay-button type="primary" size="lg" @click="showRechargeDialog" class="recharge-btn">
                                        <lay-icon type="layui-icon-cart"></lay-icon>
                                        积分充值
                                    </lay-button>
                                </lay-col>
                            </lay-row>
                        </lay-card>
                    </div>
                </lay-tab-item>
            </lay-tab>
        </lay-card>
        <lay-card>
            <lay-tab v-model="tabCurrent2" type="brief">
                <lay-tab-item id="tc2_1">
                    <template #title>
                        <span class="tabtitle">上次学习</span>
                    </template>
                    <div style="margin-top: 10px;">
                        <lay-container>
                            <lay-card v-if="this.lastStudy?.course || this.lastStudy?.exam">
                                <lay-row space="15">
                                    <lay-col class="planindexbox" sm="8" xs="8" v-if="this.lastStudy?.course">
                                        <div class="plan-card pointer" @click="setCourseSession(this.lastStudy?.course.csid)">
                                            <div class="plan-thumb">
                                                <img :src="this.lastStudy.course.csthumb"/>
                                            </div>
                                            <div class="plan-content">
                                                <h4 class="plan-title">{{ this.lastStudy.course.cstitle }}</h4>
                                                <div class="plan-status not-passed">
                                                    <lay-icon type="layui-icon-circle-dot"></lay-icon>
                                                    <span>去学习</span>
                                                </div>
                                            </div>
                                        </div>
                                    </lay-col>
                                    <lay-col class="planindexbox" sm="8" xs="8" v-if="this.lastStudy?.exam">
                                        <div class="plan-card pointer" @click="setExamSession(this.lastStudy?.exam.basicid)">
                                            <div class="plan-thumb">
                                                <img :src="this.lastStudy.exam.basicthumb"/>
                                            </div>
                                            <div class="plan-content">
                                                <h4 class="plan-title">{{ this.lastStudy.exam.basic }}</h4>
                                                <div class="plan-status not-passed">
                                                    <lay-icon type="layui-icon-circle-dot"></lay-icon>
                                                    <span>去练习</span>
                                                </div>
                                            </div>
                                        </div>
                                    </lay-col>
                                </lay-row>
                            </lay-card>
                            <lay-card v-else>
                                <lay-empty></lay-empty>
                            </lay-card>
                        </lay-container>
                    </div>
                </lay-tab-item>
            </lay-tab>
        </lay-card>
        <lay-layer v-model="rechargeDialog" :area="['800px']" title="积分充值">
            <div style="padding: 20px;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h3>选择充值金额：1元=10积分</h3>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px;">
                    <template v-for="item in coins">
                        <button :class="['recharge-option',rechargeValue === item?'active':'']" @click="rechargeValue = item">
                            <div style="font-size: 24px; font-weight: bold;">{{item}}</div>
                            <div style="font-size: 12px; color: #999;">积分</div>
                        </button>
                    </template>
                </div>
            </div>
            <template #footer>
                <div style="text-align: right;padding:0 20px 20px 20px">
                    <lay-button type="primary" @click="handleRecharge">充值</lay-button>
                </div>
            </template>
        </lay-layer>
    </lay-card>
</template>
<script>
import userApi from '@/framework/api/user.js';
import examApi from '@/framework/api/exam.js';
import {layer} from '@layui/layui-vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import courseApi from "@/framework/api/course.js";
import {useAuthStore} from "@/stores/auth.js";

export default {
    mixins: [baseMixin],
    setup() {
        const authStore = useAuthStore();
        return {authStore};
    },
    data() {
        return {
            tabCurrent1: "tc1_1",
            tabCurrent2: "tc2_1",
            lastStudy:{},
            contents:[],
            userPoints: 0,
            rechargeDialog:false,
            rechargeValue: 10,
            coins:[10,50,100,200,500,1000]
        }
    },
    async mounted() {
        await this.getLastStudy();
        await this.getUserPoints();
    },
    methods: {
        getUserPoints: async function () {
            await this.execute(async () => {
                const user = await userApi.getCurrentUser();
                this.userPoints = user.usercoin || 0;
            }, null, null);
        },
        showRechargeDialog: function () {
            this.rechargeDialog = true;
        },
        handleRecharge: async function () {
            await this.execute(async () => {
                const data = await userApi.recharge(this.rechargeValue);
                this.$router.push('/desktop/home/core/pay/' + data.ordersn);
            }, null, null);
        },
        getLastStudy: async function () {
            await this.execute(async () => {
                this.lastStudy = await userApi.getLastStudy();
                this.contents = [];
            }, null, null);
        },
        setExamSession: async function (examId) {
            await this.execute(async () => {
                await examApi.setExamSession(examId);
                await this.authStore.setBasic();
                this.$router.push('/desktop/home/exam/basic');
            }, null, null)
        },
        setCourseSession: async function (csId) {
            await this.execute(async () => {
                await courseApi.setCourseSession(csId);
                this.$router.push('/desktop/home/course/course');
            }, null, null)
        },
    }
}
</script>
<style scoped>
.points-info {
    display: flex;
    align-items: center;
    padding:20px;
}

.points-content {
    margin-left: 20px;
}

.points-value {
    font-size: 42px;
    color: #FFB800;
}

.recharge-btn {
    height: 50px;
    font-size: 16px;
}
.recharge-option {
    padding: 20px;
    border: 2px solid #e8e8e8;
    border-radius: 8px;
    background: #fff;
    cursor: pointer;
    transition: all 0.3s;
}
.recharge-option:hover,.recharge-option.active {
    border-color: #1e9fff;
    background: #f0f9ff;
}
</style>