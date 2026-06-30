<template>
	<lay-card>
		<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" :data-source="dataSource" id="taskid">
			<template #toolbar>
				<lay-button type="primary" size="sm" @click="showAddPage = true">添加任务</lay-button>
			</template>
			<template v-slot:taskstatusName="{ row }">
				<div style="max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ taskStatusMap[row.taskstatus] }}</div>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="uploadData(row.taskid)">上传</lay-button>
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delTask(row.taskid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :shade="false" :area="['720px']" :btn="addPageBtns" title="添加任务">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="model" :pane="false" size="md" labelWidth="100" class="form" ref="addPageFrom">
				<lay-form-item label="科目选择" prop="tasksubjectid" required>
					<lay-select v-model="model.tasksubjectid" placeholder="请选择科目" :showSearch="true">
						<lay-select-option :value="value" :label="label" v-for="(label, value) in subjects" :key="value"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="任务名称" prop="taskname" required>
					<lay-input v-model="model.taskname" placeholder="请输入任务名称"></lay-input>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :shade="false" :area="['720px']" :btn="modifyPageBtns" title="修改任务">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modelModify" :pane="false" size="md" labelWidth="100" class="form" ref="modifyPageFrom">
				<lay-form-item label="科目选择" prop="tasksubjectid" required>
					<lay-select v-model="modelModify.tasksubjectid" placeholder="请选择科目" :showSearch="true">
						<lay-select-option :value="value" :label="label" v-for="(label, value) in subjects" :key="value"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="任务名称" prop="taskname" required>
					<lay-input v-model="modelModify.taskname" placeholder="请输入任务名称"></lay-input>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import taskApi from '@/framework/api/admin/task.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import {withConfirm, withLayer} from "@/framework/utils/decorators.js";
import userApi from "@/framework/api/admin/user.js";
export default {
	data() {
		return {
			columns:ref([{
				title: '任务ID',
				key: 'taskid',
				width: '80px'
			}, {
				title: '任务名称',
				key: 'taskname'
			}, {
				title: '科目名称',
				key: 'tasksubject'
			}, {
				title: '任务状态',
				key: 'taskstatus',
				customSlot: "taskstatusName",
				width: '100px'
			}, {
				title: '操作',
				customSlot: "operator",
				key: "operator",
				width: "150px"
			}]),
			subjects:{
				1:'数学',
				2:'语文'
			},
			dataSource:ref([]),
			showAddPage:ref(false),
			showModifyPage:ref(false),
			model:ref({}),
			modelModify:ref({}),
			taskStatusMap:ref({
				0: '未上传',
				1: '已上传',
				2: '已完成'
			}),
			addPageBtns:ref([
				{
					text: "确认",
					callback: () => {
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showAddPage = false;
							this.addTask();
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
			]),
			modifyPageBtns:ref([
				{
					text: "确认",
					callback: () => {
						this.$refs['modifyPageFrom'].validate().then((res) => {
							this.showModifyPage = false;
							this.modifyTask();
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
			])
		}
	},
	async mounted() {
		this.subjects = await taskApi.getSubjects();
		this.getData();
	},
	methods:{
		getData:async function(){
			const id = layer.load(0);
			try{
				const data = await taskApi.getTasks({});
				this.dataSource = Object.values(data.data??{});
				layer.close(id);
			}catch (e) {
				layer.msg(e.message || '获取数据失败')
			}
		},
		showModify:function(row){
			this.modelModify = {...row};
			this.showModifyPage = true;
		},
		delTask:async function(id){
			withConfirm('确定要删除吗？',() => taskApi.delTask(id),this.getData)
		},
		addTask:async function(){
			await taskApi.addTask(this.model);
			await this.getData();
		},
		modifyTask:async function(){
			await taskApi.modifyTask(this.modelModify);
			await this.getData();
		},
		uploadData:async function(taskid){
			let input = document.createElement('input');
			input.setAttribute('type', 'file');
			input.setAttribute('accept', '.xlsx');
			input.click();
			input.onchange = async () => {
				let formData = new FormData();
				const id = layer.load(0);
				formData.append('taskid',taskid);
				formData.append('file', input.files[0], input.files[0].name );
				await withLayer(async() => {
					await taskApi.uploadData(formData);
					await this.getData();
				},null,() => {
					layer.close(id);
				});
			};
		}
	}
}
</script>