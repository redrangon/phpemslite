<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 订单号</span><lay-input v-model="search.ordersn" allow-clear style="width: 180px;"></lay-input>
			</lay-space>
			<lay-space>
				<span style='width:70px'> 身份证号</span><lay-input v-model="search.orderpassport" allow-clear style="width: 180px;"></lay-input>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-quote>
			<p>已支付状态的订单不能被取消和删除。手动支付的订单，程序不做逻辑操作，请管理员自行为用户开通培训。</p>
		</lay-quote>
		<lay-table id="ordersn" ref="tableRef" v-model:selected-keys="selectedKeys" :columns="columns" :data-source="dataSource" :default-toolbar="false">
			<template #toolbar>
				订单管理
			</template>
			<template v-slot:orderstatus="{row}">
				<span v-if="row.orderstatus === 2" style="color:#16baaa">已支付</span>
				<span v-else-if="row.orderstatus === 1" style="color:#FFB800">待支付</span>
				<span v-else-if="row.orderstatus === 99" style="color:#999999">已取消</span>
			</template>
			<template v-slot:orderpaytype="{row}">
				<template v-if="row.orderstatus === 2">
					<span v-if="row.orderpaytype === 'alipay'" style="color:#16baaa">支付宝</span>
					<span v-else-if="row.orderpaytype === 'wechat'" style="color:#FFB800">微信</span>
					<span v-else style="color:#999999">线下</span>
				</template>
				<template v-else>
					<span style="color:#999999">未支付</span>
				</template>
			</template>
			<template #footer>
				<lay-button :disabled="selectedKeys.length < 1" size="sm" type="primary" @click="payMent()">已付款</lay-button>
				<lay-button :disabled="selectedKeys.length < 1" size="sm" type="primary" @click="cancelOrder()">取消订单</lay-button>
				<lay-button :disabled="selectedKeys.length < 1" size="sm" type="danger" @click="delOrder()">删除订单</lay-button>
				<lay-page v-model="page.current"  v-model:limit="page.limit" :layout="layout" :total="page.total"  style="float:right;" @change="changePage"></lay-page>
			</template>
			<template v-slot:operator="{ row }">
				<div v-if="row.orderstatus === 1">
					<lay-button size="xs" type="primary" @click="payMent(row.ordersn)">确认</lay-button>
					<lay-button size="xs" type="danger" @click="cancelOrder(row.ordersn)">取消</lay-button>
				</div>
				<div v-if="row.orderstatus === 2">
					<lay-button size="xs">已付款</lay-button>
				</div>
				<div v-if="row.orderstatus === 99">
					<lay-button size="xs" type="danger" @click="delOrder(row.ordersn)">删除</lay-button>
				</div>
			</template>
		</lay-table>
	</lay-card>
</template>
<script>
import tradeApi from '@/framework/api/admin/trade.js';
import {layer} from '@layui/layui-vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {ref} from 'vue';
export default {
	mixins: [baseMixin],
	data() {
		return {
			dataSource:[],
			columns:[{
				title:"选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			},{
				title:'订单号',
				key:'ordersn',
				width:'200px'
			},{
				title:'订单信息',
				key:'ordertitle'
			},{
				title:'身份证号/用户名',
				key:'orderpassport',
				width:'200px'
			},{
				title:'订单金额',
				key:'orderprice',
				width:'120px'
			},{
				title:'下单时间',
				key:'ordercreatetime',
				width:'160px'
			},{
				title:'支付时间',
				key:'orderpaytime',
				width:'160px'
			},{
				title:'支付方式',
				customSlot:'orderpaytype',
				key:'orderpaytype',
				width:'100px'
			},{
				title:'订单状态',
				customSlot:'orderstatus',
				key:'orderstatus',
				width:'100px'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"100px"
			}],
			tableRef:null,
			selectedKeys:[],
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{
				limit:20,
				current:1,
				total:0
			},
			search:{},
		}
	},
	async mounted() {
		await this.getData()
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await tradeApi.getOrderList({
					search:this.search,
					limit:this.page.limit,
					page:this.page.current
				})
				this.page.current = data.page;
				this.page.limit = data.limit;
				this.page.total = data.total;
				this.dataSource = data.data;
			},null,null);
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		delOrder:function(orderSn){
			this.confirmOperate('确定要删除订单吗？',async () => {
				let ids = this.selectedKeys;
				if(orderSn){
					ids = [orderSn]
				}
				await tradeApi.deleteOrder(ids);
			},this.getData)
		},
		cancelOrder:function(orderSn){
			this.confirmOperate('确定要取消订单吗？',async () => {
				let ids = this.selectedKeys;
				if(orderSn){
					ids = [orderSn]
				}
				await tradeApi.cancelOrder(ids);
			},this.getData)
		},
		payMent:function(orderSn){
			this.confirmOperate('确定要设置为已支付吗？',async () => {
				let ids = this.selectedKeys;
				if(orderSn){
					ids = [orderSn]
				}
				await tradeApi.payOrder(ids);
			},this.getData)
		},
	}
}
</script>
<style scoped>
	.table {
		border-collapse:collapse;
		border:1px solid #aaa;
		width:100%;
		text-align: center;
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