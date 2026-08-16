<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space>
				<span style='width:90px'>计划名称：</span>
                <lay-input v-model="search.tsname" placeholder="请输入计划名称"></lay-input>
			</lay-space>
			<lay-space>
				<span style='width:70px'>任务类型：</span>
				<lay-select v-model="search.tstype" placeholder="全部类型" :allow-clear="true" style="width:120px">
					<lay-select-option value="default" label="默认"></lay-select-option>
					<lay-select-option value="report" label="报表"></lay-select-option>
					<lay-select-option value="email" label="邮件"></lay-select-option>
					<lay-select-option value="payment" label="支付"></lay-select-option>
				</lay-select>
			</lay-space>
			<lay-space>
				<span style='width:70px'>启用状态：</span>
				<lay-select v-model="search.tsenabled" placeholder="全部" :allow-clear="true" style="width:100px">
					<lay-select-option :value="1" label="已启用"></lay-select-option>
					<lay-select-option :value="0" label="已停用"></lay-select-option>
				</lay-select>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
        <lay-quote>
            立即执行和定时执行的一次性任务将直接加入任务队列中，循环任务将加入计划任务中。
        </lay-quote>
		<lay-table id="tsid" ref="tableRef" v-model:selected-keys="selectedKeys" :columns="columns" :data-source="dataSource" :default-toolbar="false" :page="page" even>
			<template #toolbar>
				<lay-button size="sm" type="primary" @click="showAddPage = true">添加任务计划</lay-button>
			</template>
			<template #tstypeName="{ row }">
				<lay-tag v-if="row.tstype === 'report'" color="blue">报表</lay-tag>
				<lay-tag v-else-if="row.tstype === 'email'" color="orange">邮件</lay-tag>
				<lay-tag v-else-if="row.tstype === 'payment'" color="green">支付</lay-tag>
				<lay-tag v-else color="gray">默认</lay-tag>
			</template>
			<template #tsenabled="{ row }">
                <span v-if="row.tsenabled === 1">开启</span>
                <span v-else>关闭</span>
			</template>
			<template #tspriority="{ row }">
				<lay-progress :percent="row.tspriority * 10" :color="row.tspriority >= 8 ? '#FF5722' : row.tspriority >= 5 ? '#FFB800' : '#16baaa'" size="sm"></lay-progress>
				<span style="margin-left:8px">{{ row.tspriority }}/10</span>
			</template>
			<template #tsloop="{ row }">
				<code style="background:#f6f6f6;padding:2px 6px;border-radius:3px;font-size:12px;">{{ row.tsloop }}</code>
			</template>
			<template #tslastrun="{ row }">
				<span v-if="row.tslastrun">{{ formatTime(row.tslastrun) }}</span>
				<span v-else style="color:#999">从未执行</span>
			</template>
			<template #tsnextrun="{ row }">
				<span v-if="row.tsnextrun">{{ formatTime(row.tsnextrun) }}</span>
				<span v-else style="color:#999">待计算</span>
			</template>
			<template #operator="{ row }">
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delSchedule(row.tsid)">删除</lay-button>
			</template>
			<template #footer>
				<lay-row>
					<lay-col md="12">
						<lay-button type="danger" size="sm" @click="delSchedule()" :disabled="selectedKeys.length < 1">删除选中</lay-button>
					</lay-col>
					<lay-col md="12">
						<lay-page v-model="page.current" :layout="layout" v-model:limit="page.limit" :total="page.total" @change="changePage" style="float:right;"></lay-page>
					</lay-col>
				</lay-row>
			</template>
		</lay-table>
	</lay-card>

	<!-- 添加任务计划 -->
	<lay-layer v-model="showAddPage" :area="['700px']" :btn="addPageBtns" :shade="false" title="添加任务计划">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form ref="addPageFrom" :model="model" :pane="false" class="form" :labelWidth="120" size="md">
				<lay-form-item label="计划名称" prop="tsname" required>
					<lay-input v-model="model.tsname" placeholder="请输入计划名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="计划描述" prop="tsdescribe">
					<lay-textarea v-model="model.tsdescribe" placeholder="请描述该计划的任务内容和目标"></lay-textarea>
				</lay-form-item>
				<lay-form-item label="任务类名" prop="tsclass">
                    <lay-select v-model="model.tsclass" placeholder="请选择任务类名">
                        <lay-select-option :value="event.class" :label="event.name" v-for="event in events"></lay-select-option>
                    </lay-select>
				</lay-form-item>
				<lay-form-item label="任务类型" prop="tstype" required>
					<lay-select v-model="model.tstype" placeholder="请选择任务类型">
						<lay-select-option value="default" label="默认"></lay-select-option>
						<lay-select-option value="report" label="报表"></lay-select-option>
						<lay-select-option value="email" label="邮件"></lay-select-option>
						<lay-select-option value="payment" label="支付"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="优先级" prop="tspriority" required>
					<lay-slider v-model="model.tspriority" :min="1" :max="10" :step="1" show-value></lay-slider>
				</lay-form-item>
                <lay-form-item label="执行类型" prop="tsexetype" required>
                    <lay-radio v-model="model.tsexetype" name="tsexetype" :value="1">立即执行</lay-radio>
                    <lay-radio v-model="model.tsexetype" name="tsexetype" :value="2">定时执行</lay-radio>
                    <lay-radio v-model="model.tsexetype" name="tsexetype" :value="3">循环任务</lay-radio>
                </lay-form-item>
                <lay-form-item label="执行时间" prop="tsloop" required v-if="model.tsexetype === 2">
                    <lay-date-picker type="datetime" v-model="model.tsloop" placeholder="选择时间" allow-clear></lay-date-picker>
                </lay-form-item>
				<lay-form-item label="Cron表达式" prop="tsloop" required v-if="model.tsexetype === 3">
					<lay-input v-model="model.tsloop" placeholder="如: 0 0 * * * (每天凌晨)"></lay-input>
				</lay-form-item>
				<lay-form-item label="失败重试" prop="tsattempts">
					<lay-input-number v-model="model.tsattempts" :min="0" :max="10"></lay-input-number>
				</lay-form-item>
				<lay-form-item label="启用状态" prop="tsenabled" v-if="model.tsexetype === 3">
					<lay-radio v-model="model.tsenabled" name="tsenabled" :value="0" label="关闭"></lay-radio>
                    <lay-radio v-model="model.tsenabled" name="tsenabled" :value="1" label="启用"></lay-radio>
				</lay-form-item>
                <lay-form-item label="类参数" prop="tspayload">
                    <lay-textarea placeholder="请输入JSON格式的类参数" v-model="model.tspayload"></lay-textarea>
                </lay-form-item>
			</lay-form>
		</div>
	</lay-layer>

	<!-- 修改任务计划 -->
	<lay-layer v-model="showModifyPage" :area="['700px']" :btn="modifyPageBtns" :shade="false" title="修改任务计划">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form ref="modifyPageFrom" :model="modify" :pane="false" class="form" :labelWidth="120" size="md">
				<lay-form-item label="计划名称" prop="tsname" required>
					<lay-input v-model="modify.tsname" placeholder="请输入计划名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="计划描述" prop="tsdescribe">
					<lay-textarea v-model="modify.tsdescribe" placeholder="请描述该计划的任务内容和目标"></lay-textarea>
				</lay-form-item>
				<lay-form-item label="任务类名" prop="tsclass">
					<lay-select v-model="modify.tsclass" placeholder="请选择任务类名">
                        <lay-select-option :value="event.class" :label="event.name" v-for="event in events"></lay-select-option>
                    </lay-select>
				</lay-form-item>
				<lay-form-item label="任务类型" prop="tstype" required>
					<lay-select v-model="modify.tstype" placeholder="请选择任务类型">
						<lay-select-option value="default" label="默认"></lay-select-option>
						<lay-select-option value="report" label="报表"></lay-select-option>
						<lay-select-option value="email" label="邮件"></lay-select-option>
						<lay-select-option value="payment" label="支付"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="优先级" prop="tspriority" required>
					<lay-slider v-model="modify.tspriority" :min="1" :max="10" :step="1" show-value></lay-slider>
				</lay-form-item>
				<lay-form-item label="Cron 表达式" prop="tsloop" required>
					<lay-input v-model="modify.tsloop" placeholder="如: 0 0 * * * (每天凌晨)"></lay-input>
				</lay-form-item>
				<lay-form-item label="失败重试" prop="tsattempts">
					<lay-input-number v-model="modify.tsattempts" :min="0" :max="10"></lay-input-number>
				</lay-form-item>
				<lay-form-item label="启用状态" prop="tsenabled">
                    <lay-radio v-model="modify.tsenabled" name="tsenabled" :value="0" label="关闭"></lay-radio>
                    <lay-radio v-model="modify.tsenabled" name="tsenabled" :value="1" label="启用"></lay-radio>
				</lay-form-item>
                <lay-form-item label="类参数" prop="tspayload">
                    <lay-textarea placeholder="请输入JSON格式的类参数" v-model="modify.tspayload"></lay-textarea>
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
				key: 'tsid',
				width: '60px'
			}, {
				title: '计划名称',
				key: 'tsname'
			}, {
				title: '任务类型',
				customSlot: 'tstypeName',
				key: 'tstype',
				width: '80px'
			}, {
				title: '优先级',
				customSlot: 'tspriority',
				key: 'tspriority',
				width: '150px'
			}, {
				title: 'Cron 表达式',
				customSlot: 'tsloop',
				key: 'tsloop',
				width: '150px'
			}, {
				title: '执行次数',
				key: 'tstimes',
				width: '80px'
			}, {
				title: '启用状态',
				customSlot: 'tsenabled',
				key: 'tsenabled',
				width: '100px'
			}, {
				title: '上次执行',
				customSlot: 'tslastrun',
				key: 'tslastrun',
				width: '160px'
			}, {
				title: '下次执行',
				customSlot: 'tsnextrun',
				key: 'tsnextrun',
				width: '160px'
			}, {
				title: '操作',
				customSlot: "operator",
				key: "operator",
				width: "100px",
				fixed: 'right'
			}],
            events:[],
			dataSource: [],
			search: {},
			selectedKeys: [],
			layout: ['count', 'prev', 'page', 'next', 'limits', 'refresh', 'skip'],
			page: { current: 1, limit: 10, total: 0 },
			showAddPage: false,
			showModifyPage: false,
			model: {
				tsname: '',
				tsdescribe: '',
				tsclass: '',
				tstype: 'default',
				tspriority: 5,
				tsloop: '',
                tsexetype: 1,
				tsattempts: 3,
				tsenabled: 1,
			},
			modify: {},
			addPageBtns: [
				{
					text: "确认",
					callback: () => {
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showAddPage = false;
							this.addSchedule();
						}).catch(res => {});
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showAddPage = false;
					}
				}
			],
			modifyPageBtns: [
				{
					text: "确认",
					callback: () => {
						this.$refs['modifyPageFrom'].validate().then((res) => {
							this.showModifyPage = false;
							this.modifySchedule();
						}).catch(res => {});
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
        await this.getEventList();
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
					const data = await taskApi.getScheduleList({
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
        getEventList: async function() {
			this.events = await taskApi.getEventList();
		},
		changePage: function({ current, limit }) {
			this.page.current = current;
			this.page.limit = limit;
			this.getData();
		},
		showModify: function(row) {
			this.modify = JSON.parse(JSON.stringify(row));
			this.showModifyPage = true;
		},
		delSchedule: function(id) {
			this.confirmDelete(async () => {
				await taskApi.delSchedule(id ? [id] : this.selectedKeys);
			}, this.getData);
		},
		addSchedule: function() {
			this.base(async () => {
				await taskApi.addSchedule(this.model);
				this.model = {
					tsname: '',
					tsdescribe: '',
					tsclass: '',
					tstype: 'default',
					tspriority: 5,
					tsloop: '',
					tsattempts: 3,
					tsenabled: 1,
				};
			});
		},
		modifySchedule: function() {
			this.base(async () => {
				await taskApi.modifySchedule(this.modify);
			});
		}
	}
}
</script>
