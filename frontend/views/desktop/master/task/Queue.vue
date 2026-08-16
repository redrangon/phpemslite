<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space>
				<span style='width:90px'>任务名称：</span><lay-input v-model="search.taskname" placeholder="请输入任务名称"></lay-input>
			</lay-space>
			<lay-space>
				<span style='width:70px'>任务类型：</span>
				<lay-select v-model="search.tasktype" placeholder="全部类型" :allow-clear="true" style="width:120px">
					<lay-select-option value="default" label="默认"></lay-select-option>
					<lay-select-option value="report" label="报表"></lay-select-option>
					<lay-select-option value="email" label="邮件"></lay-select-option>
					<lay-select-option value="payment" label="支付"></lay-select-option>
				</lay-select>
			</lay-space>
			<lay-space>
				<span style='width:70px'>任务状态：</span>
				<lay-select v-model="search.taskstatus" placeholder="全部状态" :allow-clear="true" style="width:120px">
					<lay-select-option value="pending" label="待执行"></lay-select-option>
					<lay-select-option value="locked" label="执行中"></lay-select-option>
					<lay-select-option value="finished" label="已完成"></lay-select-option>
				</lay-select>
			</lay-space>
			<lay-space>
				<span style='width:70px'>执行结果：</span>
				<lay-select v-model="search.taskresult" placeholder="全部结果" :allow-clear="true" style="width:120px">
					<lay-select-option :value="0" label="未执行"></lay-select-option>
					<lay-select-option :value="1" label="失败"></lay-select-option>
					<lay-select-option :value="2" label="成功"></lay-select-option>
				</lay-select>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-table id="taskid" ref="tableRef" v-model:selected-keys="selectedKeys" :columns="columns" :data-source="dataSource" :default-toolbar="false" :page="page" even>
			<template #tasktypeName="{ row }">
				<lay-tag v-if="row.tasktype === 'report'" color="blue">报表</lay-tag>
				<lay-tag v-else-if="row.tasktype === 'email'" color="orange">邮件</lay-tag>
				<lay-tag v-else-if="row.tasktype === 'payment'" color="green">支付</lay-tag>
				<lay-tag v-else color="gray">默认</lay-tag>
			</template>
			<template #taskstatusName="{ row }">
				<lay-tag v-if="row.taskstatus === 'pending'" color="blue">待执行</lay-tag>
				<lay-tag v-else-if="row.taskstatus === 'locked'" color="orange">执行中</lay-tag>
				<lay-tag v-else-if="row.taskstatus === 'finished'" color="green">已完成</lay-tag>
				<lay-tag v-else color="gray">未知</lay-tag>
			</template>
			<template #taskresultName="{ row }">
				<span v-if="row.taskresult === 0" style="color:#999">—</span>
				<lay-tag v-else-if="row.taskresult === 1" color="red">失败</lay-tag>
				<lay-tag v-else-if="row.taskresult === 2" color="green">成功</lay-tag>
			</template>
			<template #taskscheduletime="{ row }">
				<span v-if="row.taskscheduletime">{{ formatTime(row.taskscheduletime) }}</span>
				<span v-else style="color:#999">立即执行</span>
			</template>
			<template #taskstarttime="{ row }">
				<span v-if="row.taskstarttime">{{ formatTime(row.taskstarttime) }}</span>
				<span v-else style="color:#999">—</span>
			</template>
			<template #taskfinishtime="{ row }">
				<span v-if="row.taskfinishtime">{{ formatTime(row.taskfinishtime) }}</span>
				<span v-else style="color:#999">—</span>
			</template>
			<template #operator="{ row }">
				<lay-button size="xs" type="primary" @click="showDetail(row)">详情</lay-button>
				<lay-button size="xs" type="warm" @click="retryTask(row.taskid)" :disabled="row.taskresult !== 1">重试</lay-button>
				<lay-button size="xs" type="danger" @click="delTask(row.taskid)">删除</lay-button>
			</template>
			<template #footer>
				<lay-row>
					<lay-col md="12">
						<lay-button type="danger" size="sm" @click="delTask()" :disabled="selectedKeys.length < 1">删除选中</lay-button>
					</lay-col>
					<lay-col md="12">
						<lay-page v-model="page.current" :layout="layout" v-model:limit="page.limit" :total="page.total" @change="changePage" style="float:right;"></lay-page>
					</lay-col>
				</lay-row>
			</template>
		</lay-table>
	</lay-card>

	<!-- 任务详情 -->
	<lay-layer v-model="showDetailPage" :area="['650px']" title="任务详情">
		<div style="padding: 20px 40px 20px 20px;">
			<lay-form :pane="false" :labelWidth="120" size="md">
				<lay-form-item label="任务 ID">
                    <div class="taskDetail">{{ detail.taskid }}</div>
                </lay-form-item>
				<lay-form-item label="任务名称">
                    <div class="taskDetail">{{ detail.taskname }}</div>
                </lay-form-item>
				<lay-form-item label="任务类名">
                    <div class="taskDetail">{{ detail.taskclass }}</div>
                </lay-form-item>
				<lay-form-item label="任务类型">
                    <div class="taskDetail">
                        <lay-tag v-if="detail.tasktype === 'report'" color="blue">报表</lay-tag>
                        <lay-tag v-else-if="detail.tasktype === 'email'" color="orange">邮件</lay-tag>
                        <lay-tag v-else-if="detail.tasktype === 'payment'" color="green">支付</lay-tag>
                        <lay-tag v-else color="gray">默认</lay-tag>
                    </div>
				</lay-form-item>
				<lay-form-item label="任务状态">
                    <div class="taskDetail">
                        <lay-tag v-if="detail.taskstatus === 'pending'" color="blue">待执行</lay-tag>
                        <lay-tag v-else-if="detail.taskstatus === 'locked'" color="orange">执行中</lay-tag>
                        <lay-tag v-else-if="detail.taskstatus === 'finished'" color="green">已完成</lay-tag>
                    </div>
				</lay-form-item>
				<lay-form-item label="执行结果">
                    <div class="taskDetail">
                        <lay-tag v-if="detail.taskresult === 1" color="red">失败</lay-tag>
                        <lay-tag v-else-if="detail.taskresult === 2" color="green">成功</lay-tag>
                        <span v-else style="color:#999">未执行</span>
                    </div>
				</lay-form-item>
				<lay-form-item label="已重试次数">
                    <div class="taskDetail">{{ detail.taskattempts }} / {{ detail.taskmaxattempts }}</div>
                </lay-form-item>
				<lay-form-item label="超时设置">
                    <div class="taskDetail">{{ detail.tasktimeout > 0 ? detail.tasktimeout + ' 秒' : '不限' }}</div>
                </lay-form-item>
				<lay-form-item label="任务参数">
					<pre style="background:#f6f6f6;padding:10px;border-radius:4px;font-size:12px;margin:0;max-height:120px;overflow:auto;">{{ JSON.stringify(detail.taskpayload, null, 2) }}</pre>
				</lay-form-item>
				<lay-form-item label="处理信息" v-if="detail.taskinfo">
					<pre style="background:#f6f6f6;padding:10px;border-radius:4px;font-size:12px;margin:0;max-height:100px;overflow:auto;">{{ detail.taskinfo }}</pre>
				</lay-form-item>
				<lay-form-item label="计划执行时间">
                    <div class="taskDetail">
                        <span v-if="detail.taskscheduletime">{{ formatTime(detail.taskscheduletime) }}</span>
					    <span v-else style="color:#999">立即执行</span>
                    </div>
				</lay-form-item>
				<lay-form-item label="开始时间">
                    <div class="taskDetail">
                        <span v-if="detail.taskstarttime">{{ formatTime(detail.taskstarttime) }}</span>
                        <span v-else style="color:#999">—</span>
                    </div>
				</lay-form-item>
				<lay-form-item label="完成时间">
                    <div class="taskDetail">
                        <span v-if="detail.taskfinishtime">{{ formatTime(detail.taskfinishtime) }}</span>
					    <span v-else style="color:#999">—</span>
                    </div>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped>
.taskDetail{
    padding-top:10px;
}
</style>
<script>
import taskApi from '@/framework/api/admin/task.js';
import baseMixin from "@/framework/mixins/baseMixin.js";

export default {
    mixins: [baseMixin],
	data() {
		return {
			columns: [{
				title: "选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			}, {
				title: 'ID',
				key: 'taskid',
				width: '60px'
			}, {
				title: '任务名称',
				key: 'taskname'
			}, {
				title: '任务类型',
				customSlot: 'tasktypeName',
				key: 'tasktype',
				width: '80px'
			}, {
				title: '任务状态',
				customSlot: 'taskstatusName',
				key: 'taskstatus',
				width: '90px'
			}, {
				title: '执行结果',
				customSlot: 'taskresultName',
				key: 'taskresult',
				width: '90px'
			}, {
				title: '重试次数',
				key: 'taskattempts',
				width: '80px'
			}, {
				title: '计划执行时间',
				customSlot: 'taskscheduletime',
				width: '160px'
			}, {
				title: '开始时间',
				customSlot: 'taskstarttime',
				width: '160px'
			}, {
				title: '完成时间',
				customSlot: 'taskfinishtime',
				width: '160px'
			}, {
				title: '操作',
				customSlot: "operator",
				key: "operator",
				width: "150px",
				fixed: 'right'
			}],
			dataSource: [],
			search: {},
			selectedKeys: [],
			layout: ['count', 'prev', 'page', 'next', 'limits', 'refresh', 'skip'],
			page: { current: 1, limit: 15, total: 0 },
			showDetailPage: false,
			detail: {},
		}
	},
	async mounted() {
		await this.getData();
	},
	methods: {
		formatTime: function(ts) {
			if (!ts) return '';
			const d = new Date(ts*1000);
			const pad = n => String(n).padStart(2, '0');
			return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
		},
		getData: async function() {
			await this.execute(
				async () => {
					const data = await taskApi.getTaskList({
						page: this.page.current,
						limit: this.page.limit,
						search: this.search
					});
					this.page.page = data.page;
					this.page.total = data.total;
					this.page.limit = data.limit;
					this.dataSource = data.data;
				}, null, null
			);
		},
		changePage: function({ current, limit }) {
			this.page.current = current;
			this.page.limit = limit;
			this.getData();
		},
		showDetail: async function(row) {
			this.detail = await taskApi.getTaskDetail(row.taskid);
			this.showDetailPage = true;
		},
		delTask: function(id) {
			this.confirmDelete(async () => {
				await taskApi.delTask(id ? [id] : this.selectedKeys);
			}, this.getData);
		},
		retryTask: function(taskid) {
			this.confirmOperate('确定要重试这个任务吗？', async () => {
				await taskApi.retryTask(taskid);
			}, this.getData);
		},
	}
}
</script>
