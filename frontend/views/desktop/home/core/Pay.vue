<template>
	<lay-card class="pagecontent">
		<lay-card>
			<lay-tab v-model="tabCurrent" type="brief">
				<lay-tab-item id="1">
					<template #title>
						<span class="tabtitle">订单支付</span>
					</template>
					<lay-container>
						<lay-card>
							<table class="table">
								<thead>
									<tr>
										<th>订单号</th>
										<th>订单信息</th>
										<th>订单金额</th>
										<th style="width: 100px;">下单时间</th>
										<th>支付状态</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>{{ order.ordersn }}</td>
										<td>{{ order.ordertitle }}</td>
										<td>{{ order.orderprice }}</td>
										<td>{{ order.ordercreatetime }}</td>
										<td>
											<span v-if="order.orderstatus === 1" style="color:#FF5722">待支付</span>
											<span v-else-if="order.orderstatus === 2" style="color:#16baaa">已支付</span>
											<span v-else-if="order.orderstatus === 99" style="color:#999999">已取消</span>
											<span v-else>-</span>
										</td>
									</tr>
								</tbody>
							</table>
						</lay-card>
						<lay-card style="padding:20px;text-align: center;">
							<lay-space size="lg" v-if="order.orderstatus === 1">
								<lay-space><lay-button type="primary" @click="wechatPay()" v-if="payInfo.wechatPay">微信支付</lay-button></lay-space>
								<lay-space><lay-button type="normal" @click="aliPay()" v-if="payInfo.aliPay">支付宝支付</lay-button></lay-space>
							</lay-space>							
						</lay-card>
					</lay-container>
				</lay-tab-item>
			</lay-tab>
		</lay-card>
		<lay-layer v-model="showCodePage" :area="['400px']" title="支付二维码">
			<lay-space direction="vertical" fill wrap style="padding: 20px;text-align: center;">
				<img :src="paycode" v-if="result && result.code_url">
				<lay-space v-else>{{ result.return_msg }}</lay-space>
				<lay-space><lay-button type="primary" @click="wxpay" v-if="payinfo.wxpay">更新支付二维码</lay-button></lay-space>
			</lay-space>
		</lay-layer>
	</lay-card>
</template>
<script>
import tradeApi from '@/framework/api/trade.js';
import {layer} from '@layui/layui-vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
	data() {
		return {
			tabCurrent:"1",
			orderSn:"",
			order:{},
			payInfo:{},
			payCode:{},
			result:{},
			showCodePage:false
		}
	},
	async mounted() {
		this.orderSn = this.$route.params.ordersn;
		await this.getData();
	},
	methods:{
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
				const data = await tradeApi.payOrder({
					orderSn:this.orderSn,
					type:'alipay'
				});
				const formHtml = data.data;
				const newWindow = window.open('about:blank');
				newWindow.document.body.innerHTML = formHtml;
				newWindow.document.body.style.display = 'none';
			},null,null);
		}
	}
}
</script>
<style scoped>

.layui-card.shadow{
	box-shadow: none;
}
.plandetailbox{
	width: 100%;
	background-color: #FFFFFF;
	height: 210px;
	border-radius: 5px;
}
.plandetailbox img{
	width: 100%;
	height:190px;
	border-radius: 5px;
}
.plandetailbox h3,.plandetailbox p{
	line-height: 30px;
}
.plandetailbox .desc{
	line-height: 20px;
	color:#999999;
}
.layui-loading-spinning{
	background-color:unset;
}
.table {
	border-collapse:collapse;
	border:1px solid #aaa;
	width:100%;
	text-align: center;
	margin-bottom:20px;
}
.table thead{
	background-color: #fafafa;
	font-weight: bold;
}
.table th {
	border:1px solid #ddd;
	padding:10px;
	width:80px;
}
.table td {
	padding:10px;
	border:1px solid #ddd;
	min-width:80px;
}
.table .left{
	text-align: left;
}
</style>