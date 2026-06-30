<template>
	<div style="width:100%;">
        <!-- 导航条 -->
        <van-nav-bar title="消费记录" left-arrow @click-left="$router.go(-1)"  placeholder fixed/>
		<div class="card-container">
	        <!-- 订单列表 -->
	        <van-list v-model="loading" :finished="finished" finished-text="没有更多订单了" @load="onLoad">
	            <template v-for="(expense, index) in expenses" :key="index">
	                <van-cell-group class="menu-list">
	                    <van-cell :is-link="false">
	                        <template #title>
	                            <span style="color: #666;font-size: 14px;">时间: {{expense.uetime}}</span>
	                        </template>
	                    </van-cell>
	                    <van-cell :is-link="false">
		                    <template #title>
			                    <div>
				                    {{expense.uedescribe}}
			                    </div>
		                    </template>
	                    </van-cell>
	                    <van-cell :is-link="false">
	                        <template #title>
	                            <span style="color: #666;font-size: 14px;">积分额:</span>
	                        </template>
	                        <template #right-icon>
	                            <span style="color: #666;font-size: 14px;">{{ expense.ueamount }}</span>
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
import userApi from '@/framework/api/user.js';
import tradeApi from '@/framework/api/trade.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
    data() {
        return {
            expenses:[],
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
