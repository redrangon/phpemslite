<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 关键字：</span>
				<lay-input v-model="search.plan"></lay-input>
			</lay-space>
			<lay-space>
				<span style='width:70px'> 状态：</span>
				<lay-select v-model="search.status" allow-clear placeholder="请选择">
					<lay-select-option :value="1" label="待审核"></lay-select-option>
					<lay-select-option :value="2" label="已审核"></lay-select-option>
				</lay-select>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
        <lay-quote>
            <p>提示：参训人数包含未缴费和未审核人员</p>
        </lay-quote>
		<lay-table id="planid" ref="tableRef" v-model:selected-keys="selectedKeys" :columns="columns" :data-source="dataSource" :default-toolbar="false" even>
			<template #toolbar>
				<lay-button size="sm" type="primary" @click="showAddPage = true">添加计划</lay-button>
			</template>
			<template #footer>
                <lay-button :disabled="selectedKeys.length < 1" size="sm" type="primary" @click="refresh()">刷新人数</lay-button>
                <lay-button :disabled="selectedKeys.length < 1" size="sm" type="danger" @click="verifyPlan()">通过审核</lay-button>
				<lay-button :disabled="selectedKeys.length < 1" size="sm" type="warm" @click="unVerifyPlan()">拒绝审核</lay-button>
				<lay-button :disabled="selectedKeys.length < 1" size="sm" type="danger" @click="delPlan()">删除选中数据</lay-button>
                <lay-page v-model="page.current" v-model:limit="page.limit" :layout="layout" :total="page.total" style="float:right;" @change="changePage"></lay-page>
			</template>
			<template #planstatus="{row}">
				<p v-if="row.planstatus === 1">待审核</p>
				<p v-else-if="row.planstatus === 2">通过审核</p>
				<p v-else>待提交</p>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary"  @click="planNotice(row.planid)">公告</lay-button>
				<lay-button size="xs" type="primary"  @click="planMonitor(row.planid)">监考</lay-button>
				<lay-button size="xs" type="primary" @click="planMember(row.planid)">人员</lay-button>
				<lay-button size="xs" type="primary" @click="planCourse(row.planid)">课程</lay-button>
				<lay-button size="xs" type="primary" @click="planExam(row.planid)">考试</lay-button>
				<lay-button size="xs" type="primary"  @click="planCert(row.planid)">证件</lay-button>
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delPlan(row.planid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :area="['760px','600px']" :btn="addPageBtns" title="添加计划">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form ref="addPageFrom" :labelWidth="120" :model="model" :pane="false" class="form" size="md">
				<lay-form-item label="计划名称" prop="planname" required>
					<lay-input v-model="model.planname" placeholder="请输入计划名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="planthumb" required>
					<myThumb v-model:src="model.planthumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="开通方式" prop="planjointype" required>
					<lay-radio v-model="model.planjointype" :value="0" label="仅管理员开通" name="planjointype"></lay-radio>
					<lay-radio v-model="model.planjointype" :value="1" label="管理员或用户报名开通" name="planjointype"></lay-radio>
				</lay-form-item>
				<lay-form-item label="人员审核" prop="planverifytype" required>
					<lay-radio v-model="model.planverifytype" :value="0" label="手动审核" name="planverifytype"></lay-radio>
					<lay-radio v-model="model.planverifytype" :value="1" label="自动审核" name="planverifytype"></lay-radio>
				</lay-form-item>
				<lay-form-item label="完成方式" prop="planpasstype" required>
					<lay-radio v-model="model.planpasstype" :value="0" label="全部完成" name="planpasstype"></lay-radio>
					<lay-radio v-model="model.planpasstype" :value="1" label="学完课程" name="planpasstype"></lay-radio>
					<lay-radio v-model="model.planpasstype" :value="2" label="通过考试" name="planpasstype"></lay-radio>
				</lay-form-item>
				<lay-form-item v-if="model.planjointype === 1" :disabled="true" label="报名费用" prop="planprice" required>
					<lay-input v-model="model.planprice" placeholder="请输入报名费用"><template #append="{ disabled }">元</template></lay-input>
				</lay-form-item>
				<lay-form-item label="课程人脸对比" prop="planlessonface">
					<lay-switch v-model="model.planlessonface"></lay-switch>
				</lay-form-item>
				<lay-form-item label="考试人脸对比" prop="planexamface">
					<lay-switch v-model="model.planexamface"></lay-switch>
				</lay-form-item>
				<lay-form-item label="人脸对比时间" prop="planfacetime">
					<lay-radio v-model="model.planfacetime" :value="10" label="10分钟" name="planfacetime"></lay-radio>
					<lay-radio v-model="model.planfacetime" :value="20" label="20分钟" name="planfacetime"></lay-radio>
					<lay-radio v-model="model.planfacetime" :value="30" label="30分钟" name="planfacetime"></lay-radio>
					<lay-radio v-model="model.planfacetime" :value="40" label="40分钟" name="planfacetime"></lay-radio>
					<lay-radio v-model="model.planfacetime" :value="50" label="50分钟" name="planfacetime"></lay-radio>
					<lay-radio v-model="model.planfacetime" :value="60" label="60分钟" name="planfacetime"></lay-radio>
				</lay-form-item>
				<lay-form-item label="培训时间" prop="plantraintime" required>
					<lay-date-picker v-model="model.plantraintime" :placeholder="['开始日期','结束日期']" range type="datetime"></lay-date-picker>
				</lay-form-item>
				<lay-form-item label="证书编号前缀" prop="plancertprefix" required>
					<lay-input v-model="model.plancertprefix" placeholder="请输入证书编号前缀"></lay-input>
				</lay-form-item>
				<lay-form-item label="培训简介" prop="plandescribe">
					<lay-textarea v-model="model.plandescribe" placeholder="请输入培训简介"></lay-textarea>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :area="['760px','600px']" :btn="modifyPageBtns" title="修改计划">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form ref="modifyPageFrom" :labelWidth="120" :model="modelModify" :pane="false" class="form" size="md">
				<lay-form-item label="计划名称" prop="planname" required>
					<lay-input v-model="modelModify.planname" placeholder="请输入计划名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="planthumb" required>
					<myThumb v-model:src="modelModify.planthumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="开通方式" prop="planjointype" required>
					<lay-radio v-model="modelModify.planjointype" :value="0" label="仅管理员开通" name="planjointype"></lay-radio>
					<lay-radio v-model="modelModify.planjointype" :value="1" label="管理员或用户报名开通" name="planjointype"></lay-radio>
				</lay-form-item>
				<lay-form-item label="人员审核" prop="planverifytype" required>
					<lay-radio v-model="modelModify.planverifytype" :value="0" label="手动审核" name="planverifytype"></lay-radio>
					<lay-radio v-model="modelModify.planverifytype" :value="1" label="自动审核" name="planverifytype"></lay-radio>
				</lay-form-item>
				<lay-form-item label="完成方式" prop="planpasstype" required>
					<lay-radio v-model="modelModify.planpasstype" :value="0" label="全部完成" name="planpasstype"></lay-radio>
					<lay-radio v-model="modelModify.planpasstype" :value="1" label="学完课程" name="planpasstype"></lay-radio>
					<lay-radio v-model="modelModify.planpasstype" :value="2" label="通过考试" name="planpasstype"></lay-radio>
				</lay-form-item>
				<lay-form-item v-if="modelModify.planjointype == 1" :disabled="true" label="报名费用" prop="planprice" required>
					<lay-input v-model="modelModify.planprice" placeholder="请输入报名费用"><template #append="{ disabled }">元</template></lay-input>
				</lay-form-item>
				<lay-form-item label="课程人脸对比" prop="planlessonface">
					<lay-switch v-model="modelModify.planlessonface"></lay-switch>
				</lay-form-item>
				<lay-form-item label="考试人脸对比" prop="planexamface">
					<lay-switch v-model="modelModify.planexamface"></lay-switch>
				</lay-form-item>
				<lay-form-item label="人脸对比时间" prop="planfacetime">
					<lay-radio v-model="modelModify.planfacetime" :value="10" label="10分钟" name="planfacetime"></lay-radio>
					<lay-radio v-model="modelModify.planfacetime" :value="20" label="20分钟" name="planfacetime"></lay-radio>
					<lay-radio v-model="modelModify.planfacetime" :value="30" label="30分钟" name="planfacetime"></lay-radio>
					<lay-radio v-model="modelModify.planfacetime" :value="40" label="40分钟" name="planfacetime"></lay-radio>
					<lay-radio v-model="modelModify.planfacetime" :value="50" label="50分钟" name="planfacetime"></lay-radio>
					<lay-radio v-model="modelModify.planfacetime" :value="60" label="60分钟" name="planfacetime"></lay-radio>
				</lay-form-item>
				<lay-form-item label="培训时间" prop="plantraintime" required>
					<lay-date-picker v-model="modelModify.plantraintime" :placeholder="['开始日期','结束日期']" range type="datetime"></lay-date-picker>
				</lay-form-item>
				<lay-form-item label="证书编号前缀" prop="plancertprefix" required>
					<lay-input v-model="modelModify.plancertprefix" placeholder="请输入证书编号前缀"></lay-input>
				</lay-form-item>
				<lay-form-item label="培训简介" prop="plandescribe">
					<lay-textarea v-model="modelModify.plandescribe" placeholder="请输入培训简介"></lay-textarea>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import planApi from '@/framework/api/admin/plan.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import myThumb from '@/components/desktop/Thumb.vue';
import myUploadFile from '@/components/desktop/UploadFile.vue';
import {withConfirm, withLayer} from "@/framework/utils/decorators.js";
import examApi from "@/framework/api/admin/exam.js";
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	name:'plan',
    mixins:[baseMixin],
	data() {
		return {
			columns:[{
				title:"选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			},{
				title:'ID',
				key:'planid',
				width:'80px'
			},{
				title:'计划名称',
				key:'planname'
			},{
				title:'开始时间',
				key:'planstarttime',
				width:"120px"
			},{
				title:'结束时间',
				key:'planendtime',
				width:"120px"
			},{
				title:'参训人数',
				key:'plancounter',
				width:'120px'
			},{
				title:'审核状态',
				customSlot:'planstatus',
				key:'planstatus',
				width:'120px'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"380px"
			}],
			dataSource:[],
			search:{},
			showImportPage:false,
			selectedKeys:[],
			page:{ current: 1, limit: 20, total: 0 },
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
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
							this.addPlan();					
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
							this.modifyPlan();
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
			],
			showImportPageBtns:[
				{
					text: "提交",
					callback: () => {
						this.$refs['importPageFrom'].validate().then((res) => {
							this.showImportPage = false;
							this.importPlanMember();					
						}).catch( res => {
							console.log(res);
						});	
					}
				},
				{
					text: "关闭",
					callback: () => {
						this.showImportPage = false;
					}
				}
			]
		}
	},
	async mounted() {
		//
	},
	async activated(){
		await this.getData();
	},
	components:{
		myThumb,
		myUploadFile
	},
	methods:{
		base:async function(fn){
			await withLayer(fn,	null,this.getData);
		},
		getData:async function(){
			await withLayer(
					async () => {
						const planData = await planApi.getPlanList({
							page:this.page.current,
							limit:this.page.limit,
							search:this.search
						});
						this.dataSource = planData.data;
						this.page.current = planData.page;
						this.page.limit = planData.limit;
						this.page.total = planData.total;
					},[null,null]
			);
		},
		showModify:function(row){
			this.modelModify = JSON.parse(JSON.stringify(row));
			this.modelModify.planlessonface = this.modelModify.planlessonface === 1
			this.modelModify.planexamface = this.modelModify.planexamface === 1
			this.showModifyPage = true;
		},
		delPlan:function(id){
			let ids = this.selectedKeys;
			if(id){
				ids = [id];
			}
			withConfirm(
					'确定要删除吗？',
					async() => {
						await planApi.delPlan(ids);
					},this.getData
			);
		},
		verifyPlan:function(id){
			let ids = this.selectedKeys;
			if(id){
				ids = [id];
			}
			withConfirm(
					'确定要通过审核吗？',
					async () => {
						await planApi.verifyPlan(ids);
					},this.getData
			);
		},
		unVerifyPlan:function(id){
			let ids = this.selectedKeys;
			if(id){
				ids = [id];
			}
			withConfirm(
					'确定要拒绝审核吗？',
					async () => {
						await planApi.unVerifyPlan(ids);
					},this.getData
			);
		},
		addPlan:function(){
			this.model.planlessonface = this.model.planlessonface?1:0
			this.model.planexamface = this.model.planexamface?1:0
			withLayer(
					async() => {
						await planApi.addPlan(this.model);
						this.model = {};
					},null,this.getData
			);
		},
		modifyPlan:function(){
			this.modelModify.planlessonface = this.modelModify.planlessonface?1:0
			this.modelModify.planexamface = this.modelModify.planexamface?1:0
			withLayer(
					async() => {
						await planApi.modifyPlan(this.modelModify);
						this.modelModify = {};
					},null,this.getData
			);
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
        refresh:function(){
            this.confirmOperate('刷新可能引起卡顿，确定要刷新吗？',async () => {
                await planApi.refreshPlanMemberCounter(this.selectedKeys??[]);
            },this.getData);
        },
		planCert:function(planId){
			this.$router.push('/desktop/master/plan/cert/'+planId);
		},
		planMember:function(planId){
			this.$router.push('/desktop/master/plan/member/'+planId);
		},
		planCourse:function(planId){
			this.$router.push('/desktop/master/plan/course/'+planId);
		},
		planExam:function(planId){
			this.$router.push('/desktop/master/plan/exam/'+planId);
		},
		planStats:function(planId){
			this.$router.push('/desktop/master/plan/stats/'+planId);
		},
		planNotice:function(planId){
			this.$router.push('/desktop/master/plan/notice/'+planId);
		},
		planMonitor:function(planId){
			this.$router.push('/desktop/master/plan/monitor/'+planId);
		}
	}
}
</script>