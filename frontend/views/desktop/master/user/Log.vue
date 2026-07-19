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
		<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" :data-source="dataSource" v-model:selected-keys="selectedKeys" id="ulogid">
            <template #footer>
				<lay-page v-model="page.current" :layout="layout" v-model:limit="page.limit" :total="page.total"  @change="changePage" style="float:right;"></lay-page>
			</template>
        </lay-table>
	</lay-card>
</template>
<script>
import userApi from "@/framework/api/admin/user.js";
import baseMixin from "@/framework/mixins/baseMixin.js";
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
    mixins:[baseMixin],
	data() {
		return {
			dataSource:[],
            columns:[{
                title:'ID',
                key:'ulid',
                width:'60px'
            },{
                title:'登录时间',
                key:'ultime',
                width:'200px'
            },{
                title:'登录IP',
                key:'ulip',
                width:'200px'
            },{
                title:'登录设备',
                key:'ulclient'
            }],
            userId:0,
            selectedKeys:[],
			layout:ref(['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip']),
			page:ref({limit:20,current:1,total:0}),
			search:{},
		}
	},
	emits: ['setVal'],
	created() {
		this.userId = this.$route.params.userid;
		this.getData()
	},
	methods:{
		getData:function(){
			this.execute(async () => {
                const userData = await userApi.getUserLogList({
                    userid:this.userId,
                    limit:this.page.limit,
                    page:this.page.current,
                    search:this.search
                });
                this.page.current = userData.page;
                this.page.limit = userData.limit;
                this.page.total = userData.total;
                this.dataSource = userData.data;
			}, null, null);
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		}
	}
}
</script>