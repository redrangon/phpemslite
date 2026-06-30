<template>
	<div style="width:100%;">
        <!-- 导航条 -->
        <van-nav-bar title="订单详情" left-arrow @click-left="$router.go(-1)" placeholder fixed/>
		<div class="card-container">
	        <!-- 报名列表 -->
	        <van-cell-group style="padding:20px 0;">
	            <van-cell title="订单号" :value="order.ordersn" />
	            <van-cell title="订单信息" :value="order.ordertitle" />
	            <van-cell title="订单金额" :value="'￥' + order.orderprice" />
	            <van-cell title="订单状态">
	                <span v-if="order.orderstatus === 1" style="color:#FF5722">待支付</span>
					<span v-else-if="order.orderstatus === 2" style="color:#16baaa">已支付</span>
					<span v-else-if="order.orderstatus === 99" style="color:#999999">已取消</span>
	            </van-cell>
	            <van-cell title="下单时间" :value="order.ordercreatetime" />
	            <van-cell title="支付时间" :value="order.orderpaytime" v-if="order.orderstatus === 2"/>
	        </van-cell-group>
	        <van-cell-group style="background: none;padding:10px;">
	            <div v-if="order.orderstatus === 1 && payInfo.wechatPay">
	                <van-button type="success" block @click="wechatPay()" style="margin-top:10px;">微信支付</van-button>
	            </div>
	            <van-button type="primary" block v-if="order.orderstatus === 1 && payInfo.aliPay" @click="aliPay()" style="margin-top:10px;">支付宝支付</van-button>
	            <van-button type="primary" block v-if="order.orderstatus === 2" style="margin-top:10px;">已完成支付</van-button>
	            <van-button type="primary" block v-if="order.orderstatus === 99" style="margin-top:10px;">订单已取消</van-button>
	        </van-cell-group>
        </div>
		<van-popup :show="showAliPay" style="background: transparent">
			<div style="width:100px;margin:auto;">
				<van-loading size="60" color="#FFFFFF" vertical>加载中...</van-loading>
			</div>
		</van-popup>
    </div>
</template>

<script>
import { ref } from 'vue';
import tradeApi from '@/framework/api/trade.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {showFailToast } from 'vant'

export default {
	mixins: [baseMixin],
    data() {
        return {
            order:{},
            orderSn:'',
            payInfo:{},
            result:{},
            agent:'',
            jsApiParameters:'',
	        aliPayForm:'',
	        showAliPay:false,
        };
    },
    async mounted() {
        this.orderSn = this.$route.params.ordersn;
		await this.getData()
	},
    methods: {
	    getData:async function(){
		    await this.execute(async () => {
			    const data = await tradeApi.getOrder(this.orderSn);
			    this.order = data.order??{};
			    this.payInfo = data.info??{};
		    },null,null);
	    },
	    wechatPay:async function(){
		    await this.execute(async () => {
			    const data = await tradeApi.payOrder({
				    orderSn:this.orderSn,
				    type:'wechatpay'
			    });
			    this.paycode = data.paycode
			    this.result = data.result
			    this.showCodePage = true;
		    },null,null);
	    },
	    aliPay:async function(){
		    await this.execute(async () => {
			    this.aliPayForm = '';
			    const data = await tradeApi.payOrder({
				    orderSn:this.orderSn,
				    type:'alipay'
			    });
                const div = document.createElement('div');
                this.showAliPay = true;
                div.innerHTML = data.data;
                document.body.appendChild(div);
                document.forms['alipay_submit'].submit();
		    },null,null);
	    }
    }
};
</script>

<style scoped>
.thumb {
    width: 60px;
    height: 48px;
    object-fit: cover;
    margin: 5px 15px 5px 0px;
}
</style>
