<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:130px'> 科目关键字：</span><lay-input v-model="search.keyword" :allow-clear="true"></lay-input>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-table id="subjectid" ref="tableRef" v-model:selected-keys="selectedKeys" :columns="columns" :data-source="dataSource" :default-toolbar="false" :page="page" even>
			<template #toolbar>
				<lay-button size="sm" type="primary" @click="showAddPage = true">添加科目</lay-button>
			</template>
			<template #footer>
				<lay-button :disabled="selectedKeys.length < 1" size="sm" type="primary" @click="refreshSubject()">更新缓存</lay-button>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="showSections(row.subjectid)">章节</lay-button>
				<lay-button size="xs" type="primary" @click="refreshSubject(row.subjectid)">更新缓存</lay-button>
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delSubject(row.subjectid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :area="['720px']" :btn="addPageBtns" :shade="false" title="添加题型">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form ref="addPageFrom" :model="model" :pane="false" class="form" labelWidth="100" size="md">
				<lay-form-item label="科目名称" prop="subject" required>
					<lay-input v-model="model.subject" placeholder="请输入科目名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="科目题型" prop="subjectsetting" required>
					<lay-checkbox-group v-model="model.subjectsetting">
						<lay-checkbox v-for="(questype,questid) in questionTypes" :key="questid" :value="questype['questid']" skin="primary">{{questype['questype']}}</lay-checkbox>
					</lay-checkbox-group>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :area="['720px']" :btn="modifyPageBtns" :shade="false" title="修改题型">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form ref="modifyPageFrom" :model="modelModify" :pane="false" class="form" labelWidth="100" size="md">
				<lay-form-item label="科目名称" prop="subject" required>
					<lay-input v-model="modelModify.subject" placeholder="请输入科目名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="科目题型" prop="subjectsetting" required>
					<lay-checkbox-group v-model="modelModify.subjectsetting">
						<lay-checkbox v-for="(questype,questid) in questionTypes" :key="questid" :value="questype['questid']" skin="primary">{{questype['questype']}}</lay-checkbox>
					</lay-checkbox-group>
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
import baseMixin from "@/framework/mixins/baseMixin.js";

export default {
    mixins:[baseMixin],
	data() {
		return {
			columns:[{
				title: "选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			}, {
				title: 'ID',
				key: 'subjectid',
				width: '20px'
			}, {
				title: '科目名称',
				key: 'subject'
			}, {
				title: '题数',
				key: 'questionnumber',
				width: '100px'
			}, {
				title: '试卷数',
				key: 'papernumber',
				width: '100px'
			}, {
				title: '操作',
				customSlot: "operator",
				key: "operator",
				width: "220"
			}],
			dataSource:[],
			search:{},
			selectedKeys:[],
			page:{ current: 1, limit: 20, total: 0 },
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			questionTypes:{},
			showAddPage:false,
			showModifyPage:false,
			model:{},
			modelModify:{},
			addPageBtns:[
				{
					text: "确认",
					callback: () => {
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showAddPage = false;
							this.addSubject();
						}).catch( res => {
							//console.log(res);
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
							this.modifySubject();
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
		await this.getQuestionTypes();
		await this.getData();
	},
	methods:{
		getQuestionTypes:async function(){
			this.questionTypes = await examApi.getAllQuestionTypes();
		},
		getData:async function(){
			await this.execute(
					async () => {
						const data = await examApi.getSubjectList({
							search: this.search,
							limit: this.page.limit,
							page: this.page.current
						});
						this.page.page = data.page;
						this.page.total = data.total;
						this.page.limit = data.limit;
						this.dataSource = data.data;
					},null,null
			);
		},
		showModify:function(row){
			this.modelModify = {...row};
			this.showModifyPage = true;
		},
		delSubject:function(id){
			this.confirmDelete( async () => {
				await examApi.delSubjects(id?[id]:this.selectedKeys);
			},this.getData)
		},
		refreshSubject:function(id){
			this.confirmOperate('刷新数据可能短暂卡顿，确定要刷新吗？', async () => {
				await examApi.refreshSubjectCache(id?[id]:this.selectedKeys);
				this.selectedKeys = [];
			},this.getData)
		},
		addSubject:function(){
			this.base( async() => {
				await examApi.addSubject(this.model);
			});
		},
		modifySubject:function(){
			this.base( async() => {
				await examApi.modifySubject(this.modelModify);
			});
		},
		showSections:function(id){
			this.$router.push('/desktop/master/exam/section/'+id);
		}
	}
}
</script>