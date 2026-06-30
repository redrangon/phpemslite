<template>
	<lay-card class="pagecontent">
		<lay-card>
			<lay-tab v-model="tabCurrent" type="brief">
				<lay-tab-item id="1">
					<template #title>
						<span class="tabtitle">我的订单</span>
					</template>
					<div style="margin-top: 10px;">
						<lay-card>
							<lay-table :columns="columns" :data-source="orders">
								<template #orderstatus="{row}">
									<template v-if="row.orderstatus === 1">
										待支付
									</template>
									<template v-else-if="row.orderstatus === 2">
										已支付
									</template>
									<template v-else-if="row.orderstatus === 99">
										已取消
									</template>
									<template v-else>
										未知状态
									</template>
								</template>
								<template #operator="{row}">
									<template v-if="row.orderstatus === 1">
										<lay-button type="primary" size="xs" @click="payOrder(row.ordersn)">支付</lay-button>
										<lay-button type="danger" size="xs" @click="cancelOrder(row.ordersn)">取消</lay-button>
									</template>
									<template v-if="row.orderstatus === 2">
										<lay-button type="primary" size="xs">已支付</lay-button>
									</template>
								</template>
							</lay-table>
						</lay-card>
						<lay-page v-if="orders && orders.lenth > page.limit" v-model="page.current"  :layout="layout" v-model:limit="page.limit" :total="page.total"  @change="pageChange" theme="blue" style="float:right">
						</lay-page>
					</div>
				</lay-tab-item>
			</lay-tab>
		</lay-card>		
	</lay-card>
</template>
<script>
import tradeApi from '@/framework/api/trade.js';
import {layer} from '@layui/layui-vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {ref} from 'vue';
export default {
	mixins: [baseMixin],
	data() {
		return {
			orders:[],
			columns:[{
				title:'订单号',
				key:'ordersn',
				width:'200px'
			},{
				title:'订单信息',
				key:'ordertitle'
			},{
				title:'订单金额',
				key:'orderprice',
				width:'100px'
			},{
				title:'下单时间',
				key:'ordercreatetime',
				width:'170px'
			},{
				title:'订单状态',
				customSlot:'orderstatus',
				key:'orderstatus',
				width:'80px'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"100px"
			}],
			tabCurrent:"1",
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{current:1,total:1,limit:10}
		}
	},
	async mounted() {
		await this.getData()
	},
	methods:{
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
		cancelOrder:async function(orderSn){
			this.confirmOperate('确定要取消吗？',async () => {
				await tradeApi.cancelOrder(orderSn);
				await this.getData();
			})

		},
		payOrder:function(orderSn){
			this.$router.push('/desktop/home/core/pay/' + orderSn);
		},
		pageChange:function({current,limit}){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		showPageData:function(enroll){
			this.member = enroll
			this.showPage = true
		}
	}
}
</script>
<style scoped>
.tabtitle{
	font-size: 16px;;
	padding-left:20px;
	padding-right: 20px;
}
</style>