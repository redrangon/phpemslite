<template>
	<lay-card>
		<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" :data-source="dataSource" id="questid">
			<template #toolbar>
				<lay-button type="primary" size="sm" @click="showAddPage = true">添加题型</lay-button>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delQuestionType(row.questid)">删除</lay-button>
			</template>
			<template v-slot:questsortname="{ row }">
				{{questionTypeSorts[row.questsort]}}
			</template>
			<template v-slot:questchoicename="{ row }">
				{{questionTypeChoices[row.questsort][row.questchoice]}}
			</template>
			<template #footer>
				<lay-page v-model="page.current" :layout="layout" v-model:limit="page.limit" :total="page.total" @change="changePage" style="float:right;"></lay-page>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :shade="false" :area="['720px']" :btn="addPageBtns" title="添加题型">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="model" :pane="false" size="md" labelWidth="100" class="form" ref="addPageFrom">
				<lay-form-item label="题型名称" prop="questype" required>
					<lay-input v-model="model.questype" placeholder="请输入题型名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="题型分类" prop="questsort" required>
					<lay-select v-model="model.questsort" placeholder="请选择">
						<lay-select-option :value="Number(id)" :label="qs" v-for="(qs,id) in questionTypeSorts" :key="id"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="答题方式" prop="questchoice" required>
					<template v-if="model.questsort === 1">
						<lay-radio v-model="model.questchoice" :value="Number(id)" :label="qc" v-for="(qc,id) in questionTypeChoices[1]" :key="id"></lay-radio>
					</template>
					<template v-else>
						<lay-radio v-model="model.questchoice" :value="Number(id)" :label="qc" v-for="(qc,id) in questionTypeChoices[0]" :key="id"></lay-radio>
					</template>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :shade="false" :area="['720px']" :btn="modifyPageBtns" title="修改题型">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modelModify" :pane="false" size="md" labelWidth="100" class="form" ref="modifyPageFrom">
				<lay-form-item label="题型名称" prop="questype" required>
					<lay-input v-model="modelModify.questype" placeholder="请输入题型名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="题型分类" prop="questsort" required>
					<lay-select v-model="modelModify.questsort" placeholder="请选择">
						<lay-select-option :value="Number(id)" :label="qs" v-for="(qs,id) in questionTypeSorts" :key="id"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="答题方式" prop="questchoice" required>
					<template v-if="modelModify.questsort == 1">
						<lay-radio v-model="modelModify.questchoice" :value="Number(id)" :label="qc" v-for="(qc,id) in questionTypeChoices[1]" :key="id"></lay-radio>
					</template>
					<template v-else>
						<lay-radio v-model="modelModify.questchoice" :value="Number(id)" :label="qc" v-for="(qc,id) in questionTypeChoices[0]" :key="id"></lay-radio>
					</template>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import examApi from '@/framework/api/admin/exam.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import {withConfirm, withLayer} from "@/framework/utils/decorators.js";
export default {
	data() {
		return {
			columns:[{
				title: 'ID',
				key: 'questid',
				width: '20px'
			}, {
				title: '题型',
				key: 'questype'
			}, {
				title: '题型分类',
				key: 'questsortname',
				customSlot: 'questsortname',
				width: '120px'
			}, {
				title: '答题方式',
				key: 'questchoicename',
				customSlot: 'questchoicename',
				width: '120px'
			}, {
				title: '操作',
				customSlot: "operator",
				key: "operator",
				width: "120"
			}],
			dataSource:[],
			page:{ current: 1, limit: 20, total: 0 },
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			showAddPage:false,
			showModifyPage:false,
			model:{
				questsort:0
			},
			modelModify:{},
			questionTypeSorts:examApi.questionSort,
			questionTypeChoices:examApi.questionChoice,
			addPageBtns:[
				{
					text: "确认",
					callback: () => {
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showAddPage = false;
							this.addQuestionType();
						}).catch( res => {
							console.log(res);
						});
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showAddPage = false;
					}
				}
			],
			modifyPageBtns:[
				{
					text: "确认",
					callback: () => {
						this.$refs['modifyPageFrom'].validate().then((res) => {
							this.showModifyPage = false;
							this.modifyQuestionType();
						}).catch( res => {
							console.log(res);
						});
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showModifyPage = false;
					}
				}
			]
		}
	},
	async mounted() {
		await this.getData();
	},
	methods:{
		base:async function(fn){
			await withLayer(fn,	null,this.getData);
		},
		getData:async function(){
			await withLayer(
					async () => {
						const data = await examApi.getQuestionTypeList();
						this.page.page = data.page;
						this.page.total = data.total;
						this.page.limit = data.limit;
						this.dataSource = data.data;
					},[null,null]
			);
		},
		showModify:function(row){
			this.modelModify = JSON.parse(JSON.stringify(row));
			this.showModifyPage = true;
		},
		delQuestionType:function(id){
			withConfirm('确定要删除吗？', async () => {
				await examApi.delQuestionTypes([id]);
			},this.getData)
		},
		addQuestionType:async function(){
			this.base( async() => {
				await examApi.addQuestionType(this.model);
			});
		},
		modifyQuestionType:async function(){
			this.base( async() => {
				await examApi.modifyQuestionType(this.modelModify);
			});
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
	}
}
</script>