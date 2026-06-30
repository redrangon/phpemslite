<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 试题ID</span><lay-input v-model="search.fbquestionid"></lay-input>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" :data-source="dataSource"  @change="changePage" id="fbid" v-model:selected-keys="selectedKeys" even>
			<template #footer>
				<lay-button type="primary" size="sm" @click="signFeedback()" :disabled="selectedKeys.length < 1">标记办结</lay-button>
				<lay-button type="danger" size="sm" @click="delFeedback()" :disabled="selectedKeys.length < 1">删除选中数据</lay-button>
				<lay-page v-model="page.current" v-model:limit="page.limit" :total="page.total" :layout="layout" @change="changePage" style="float:right;"></lay-page>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="showQuestion(row.fbquestionid)">试题</lay-button>
				<lay-button size="xs" type="primary" @click="signFeedback(row.fbid)">办结</lay-button>
				<lay-button size="xs" type="danger" @click="delFeedback(row.fbid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
</template>
<style scoped></style>
<script>
import examApi from '@/framework/api/admin/exam.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
	mixins: [baseMixin],
	data() {
		return {
			columns:[{
				title: "选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			}, {
				title: 'ID',
				key: 'fbid',
				width: '20px'
			}, {
				title: '试题ID',
				key: 'fbquestionid',
				width: '60px'
			}, {
				title: '反馈类型',
				key: 'fbtype',
				width: '120px'
			}, {
				title: '反馈内容',
				key: 'fbcontent'
			}, {
				title: '反馈时间',
				key: 'fbtime',
				width: '160px'
			}, {
				title: '处理人ID',
				key: 'fbdoneuserid',
				width: '80px'
			}, {
				title: '处理时间',
				key: 'fbdonetime',
				width: '160px'
			}, {
				title: '操作',
				customSlot: "operator",
				key: "operator",
				width: "150px"
			}],
			dataSource:[],
			search:{},
			selectedKeys:[],
			page:{ current: 1, limit: 20, total: 0 },
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			questionTypes:{}
		}
	},
	emits:['setVal'],
	async mounted() {
		await this.getQuestionTypes();
		await this.getData();
	},
	methods:{
		getQuestionTypes:async function(){
			this.questionTypes = await examApi.getAllQuestionTypes();
		},
		getData:async function(){
			await this.execute(async () => {
				const data = await examApi.getFeedBackList({
					search:this.search,
					page:this.page.current,
					limit:this.page.limit
				});
				this.page.current = data.page;
				this.page.total = data.total;
				this.page.limit = data.limit;
				this.dataSource = data.data;
			},null,null)
		},
		showQuestion:async function(id){
			const data = await examApi.getQuestion(id);
			if(data.questionparent > 0)this.$router.push('/desktop/master/exam/rowsquestions/' + data.questionparent);
			else this.$router.push({path:'/desktop/master/exam/questions',query:{questionid:id}});
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		delFeedback:function(id){
			this.confirmDelete(async ()=>{
				let ids = this.selectedKeys;
				if(id){
					ids = [id];
				}
				await examApi.delFeedback(ids,this.getData);
			},this.getData);

		},
		signFeedback:function(id){
			this.confirmOperate('确定已经处理完成了吗？',async ()=>{
				let ids = this.selectedKeys;
				if(id){
					ids = [id];
				}
				await examApi.signFeedback(ids,this.getData);
			},this.getData);
		}
	}
}
</script>