<template>
	<lay-container>
		<lay-quote>本界面仅显示正在考试的正式考试会话</lay-quote>
		<lay-card>
			<lay-space size="lg">
				<lay-space>
					<span style='width:70px'> 学号：</span>
					<lay-input v-model="search.passport "></lay-input>
				</lay-space>
				<lay-space>
					<lay-button type="primary" @click="getData">搜索</lay-button>
				</lay-space>
			</lay-space>
		</lay-card>
		<lay-table :default-toolbar="false" :columns="columns" :data-source="dataSource" v-model:selected-keys="selectedKeys" id="examsessionid">
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="forceSubmitPaper(row.examsessionid)">强制交卷</lay-button>
				<lay-button size="xs" type="danger" @click="delExamSession(row.examsessionid)">删除试卷</lay-button>
			</template>
			<template #footer>
				<lay-page v-model="page.current" :layout="layout" v-model:limit="page.limit" :total="page.total" @change="changePage" style="float:right;"></lay-page>
			</template>
		</lay-table>
	</lay-container>
</template>
<style scoped></style>
<script>
import examApi from '@/framework/api/admin/exam.js';
import {layer} from '@layui/layui-vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {ref} from 'vue';
export default {
	mixins: [baseMixin],
	data() {
		return {
			dataSource:[],
			columns:[{
				title: 'ID',
				key: 'esid',
				width: '80px'
			}, {
				title: '试卷名称',
				key: 'examsession'
			}, {
				title: '考生考号',
				key: 'examsessionpassport',
				width: '180px'
			}, {
				title: '开考时间',
				key: 'examsessionstarttime',
				width: '180px'
			}, {
				title: 'IP',
				key: 'examsessionip',
				width: '150px'
			}, {
				title: '客户端环境',
				key: 'examsessionclient',
				width: '240px'
			},{
				title:'操作',
				customSlot:"operator",
				width:'150px'
			}],
			basicId:0,
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{
				limit:20,
				current:1,
				total:0
			},
            selectedKeys:[],
			search:{}
		}
	},
	async mounted() {
		this.basicId = this.$route.params.basicid;
		await this.getData()
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await examApi.getBasicMonitorList({
					basicId:this.basicId,
					limit:this.page.limit,
					page:this.page.current,
					search:this.search,
				})
				this.page.current = data.page;
				this.page.limit = data.limit;
				this.page.total = data.total;
				this.dataSource = data.data;
			},null,null)
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		forceSubmitPaper:function(examSessionId){
			this.confirmOperate('强制收卷可能导致分数偏低，确定要收卷吗？',async () => {
				await examApi.forceSavePaper(examSessionId);
			},this.getData)
		},
        delExamSession:function(row){
            layer.msg('稳定起见暂不开放', { icon : 2, time: 2000, shade:true, shadeClose:true})
        }
	}
}
</script>