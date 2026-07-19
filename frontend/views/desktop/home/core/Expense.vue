<template>
	<lay-card class="pagecontent">
		<lay-card>
			<lay-tab v-model="tabCurrent" type="brief">
				<lay-tab-item id="1">
					<template #title>
						<span class="tabtitle">消费记录</span>
					</template>
					<div style="margin-top: 10px;">
						<lay-card>
							<lay-table :columns="columns" :data-source="expenses"></lay-table>
						</lay-card>
						<lay-page v-if="expenses && expenses.length > page.limit" v-model="page.current"  :layout="layout" v-model:limit="page.limit" :total="page.total"  @change="pageChange" theme="blue" style="float:right">
						</lay-page>
					</div>
				</lay-tab-item>
			</lay-tab>
		</lay-card>		
	</lay-card>
</template>
<script>
import userApi from '@/framework/api/user.js';
import tradeApi from '@/framework/api/trade.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {ref} from 'vue';
export default {
	mixins: [baseMixin],
	data() {
		return {
			expenses:[],
			columns:[{
				title:'流水号',
				key:'ueid',
				width:'80px'
			},{
				title:'消费记录',
				key:'uedescribe'
			},{
				title:'消费积分',
				key:'ueamount',
				width:'100px'
			},{
				title:'消费时间',
				key:'uetime',
				width:'170px'
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
				const data = await userApi.getMyExpense({
					page:this.page.current,
					limit:this.page.limit
				});
				this.page.current = data.page;
				this.page.limit = data.limit;
				this.page.total = data.total;
				this.expenses = data.data;
			},null,null);
		},
		pageChange:function({current,limit}){
			this.page.current = current
			this.page.limit = limit
			this.getData()
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