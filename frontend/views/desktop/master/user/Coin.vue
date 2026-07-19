<template>
    <lay-card>
        <lay-space size="lg">
            <lay-space></lay-space>
            <lay-space>
                <span style='width:70px'> 起止时间：</span>
                <lay-date-picker v-model="search.range" range :placeholder="['开始日期','结束日期']" :allow-clear="true"></lay-date-picker>
            </lay-space>
            <lay-space>
                <lay-button type="primary" @click="getData">搜索</lay-button>
            </lay-space>
        </lay-space>
    </lay-card>
    <lay-card>
        <lay-quote>
            请注意，充值记录将以负值积分展示。
        </lay-quote>
        <lay-table :default-toolbar="false" @change="getData" :columns="columns" :data-source="expenses" ref="table" id="userid" v-model:selected-keys="selectedKeys" even>
            <template #toolbar>
                <lay-button type="primary" size="sm" @click="showRechargeDialog">积分充值</lay-button>
            </template>
            <template #footer>
                <lay-page v-model="page.current" v-model:limit="page.limit" :total="page.total" :layout="layout" @change="changePage" style="float:right;"></lay-page>
            </template>
        </lay-table>
    </lay-card>
    <lay-layer v-model="rechargeDialog" :area="['800px']" title="积分充值">
        <div style="padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h3>选择充值额度</h3>
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
</template>
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
<script>
import userApi from '@/framework/api/admin/user.js';
import {layer} from '@layui/layui-vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
    mixins: [baseMixin],
    data() {
        return {
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
            selectedKeys:[],
            expenses:[],
            layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
            page:{current:1,total:1,limit:10},
            search:{},
            rechargeDialog:false,
            coins:[10,50,100,200,500,1000],
            rechargeValue:10,
            passport:'',
        }
    },
    async mounted() {
        this.passport = this.$route.params.passport;
        await this.getData();
    },
    methods:{
        getData:async function(){
            await this.execute(async () => {
                const data = await userApi.getUserExpenseList({
                    limit:this.page.limit,
                    page:this.page.current,
                    passport:this.passport,
                    search:this.search
                });
                this.page.current = data.page;
                this.page.limit = data.limit;
                this.page.total = data.total;
                this.expenses = data.data;
            },null,null)
        },
        showRechargeDialog: function () {
            this.rechargeDialog = true;
        },
        handleRecharge: async function () {
            await this.execute(async () => {
                this.rechargeDialog = false;
                await userApi.recharge(this.passport,this.rechargeValue);
                await this.getData();
            }, null, null);
        },
        changePage:function({ current, limit }){
            this.page.current = current
            this.page.limit = limit
            this.getData()
        },
    }
}
</script>