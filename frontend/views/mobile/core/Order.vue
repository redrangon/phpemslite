<template>
	<div style="width:100%;">
        <!-- 导航条 -->
        <van-nav-bar title="我的订单" left-arrow @click-left="$router.go(-1)"  placeholder fixed/>
		<div class="card-container">
	        <!-- 订单列表 -->
	        <van-list v-model="loading" :finished="finished" finished-text="没有更多订单了" @load="onLoad">
	            <template v-for="(order, index) in orders" :key="index">
	                <van-cell-group class="menu-list">
	                    <van-cell :is-link="false">
	                        <template #title>
	                            <span style="color: #666;font-size: 14px;">订单号: {{order.ordersn}}</span>
	                        </template>
	                        <template #right-icon>
	                            <van-button type="primary" size="small" v-if="order.orderstatus === 1" @click="planPay(order.ordersn)">
	                                去支付
	                            </van-button>
	                            <van-button type="primary" size="small" v-else-if="order.orderstatus === 2">
	                                已支付
	                            </van-button>
	                            <van-button type="default" size="small" v-else>
	                                已作废
	                            </van-button>
	                        </template>
	                    </van-cell>
	                    <van-cell :is-link="false">
		                    <template #title>
			                    <div>
				                    {{order.ordertitle}}
			                    </div>
		                    </template>
	                    </van-cell>
	                    <van-cell :is-link="false">
	                        <template #title>
	                            <span style="color: #666;font-size: 14px;">合计:</span>
	                        </template>
	                        <template #right-icon>
	                            <span style="color: #666;font-size: 14px;">{{ order.orderprice }}元</span>
	                        </template>
	                    </van-cell>
	                </van-cell-group>
	            </template>
	        </van-list>
		</div>
    </div>
</template>

<script>
import { ref } from 'vue';
import tradeApi from '@/framework/api/trade.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
    data() {
        return {
            orders:[],
            page:{
				current:1,
	            total:1,
	            limit:10
            },
            loading: false,
            finished: false
        };
    },
    async mounted() {
        await this.getData();
    },
    methods: {
        getData:async function(){
	        await this.execute(async () => {
		        const data = await tradeApi.getMyOrders({
			        page:this.page.current,
			        limit:this.page.limit
		        });
		        this.page.current = data.page;
		        this.page.limit = data.limit;
		        this.page.total = data.total;
		        this.orders = data.data;
	        },null,null);
		},
        planPay:function(orderSn){
	        this.$router.push('/mobile/core/pay/' + orderSn);
		},
        async onLoad() {
            if (this.page.current * this.page.limit > this.page.total) {
                this.finished = true; // 加载完成	
            }
            else {
                this.loading = true; // 加载中
                this.page.current++;
                await this.getData();	
                this.loading = false; // 加载完成;
            }
        }
    }
};
</script>

<style scoped>
.menu-list{
	margin-bottom: 10px;
}
.menu-list div{
	padding:10px 20px;
	font-size: 16px;
	background: transparent;
}
/* 可根据需要添加自定义样式 */
</style>
