<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space>
				<span style='width:70px'> 考场ID：</span><lay-input v-model="search.basicid"></lay-input>
			</lay-space>
			<lay-space>
				<span style='width:70px'> 关键词</span><lay-input v-model="search.keyword"></lay-input>
			</lay-space>
			<lay-space>
				<span style='width:70px'> 科目</span>
				<lay-select v-model="search.basicsubjectid" placeholder="请选择科目" :allow-clear="true">
					<lay-select-option v-for="(subject,sid) in subjects" :key="sid" :value="sid" :label="subject"></lay-select-option>
				</lay-select>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-table :default-toolbar="false" :columns="columns" v-model:selectedKeys="selectedKeys" :data-source="dataSource" id="basicid">
			<template #toolbar>
				<lay-button type="primary" size="sm" @click="showAddPage = true">添加考场</lay-button>
			</template>
			<template #basicthumb="{ row }">
				<img :src="row.basicthumb?row.basicthumb:'/src/assets/images/noimages.png'" style="height: 40px;"/>
			</template>
			<template #subject="{ row }">
				{{subjects[row.basicsubjectid]}}
			</template>
			<template #basicstatus="{ row }">
				<lay-icon type="layui-icon-success" color="#16baaa" v-if="row.basicstatus === 1"></lay-icon>
				<lay-icon type="layui-icon-error" v-else></lay-icon>
			</template>
			<template #footer>
				<lay-row>
					<lay-col md="12">
						<lay-button type="danger" size="sm" @click="delBasic()" :disabled="selectedKeys.length < 1">删除选择考场</lay-button>
					</lay-col>
					<lay-col md="12">
						<lay-page v-model="page.current" :layout="layout" v-model:limit="page.limit" :total="page.total" @change="changePage" style="float:right;"></lay-page>
					</lay-col>
				</lay-row>
			</template>
			<template v-slot:operator="{ row }">
                <lay-button type="primary" size="xs" @click="showBasicPrice(row.basicid)">价格</lay-button>
                <lay-button size="xs" type="normal" @click="showMember(row.basicid)">人员</lay-button>
                <lay-button size="xs" type="normal" @click="monitor(row.basicid)">监考</lay-button>
				<lay-button size="xs" type="normal" @click="config(row.basicid)">配置</lay-button>
				<lay-button size="xs" type="primary" @click="showUnScore(row.basicid)">批卷</lay-button>
                <lay-button size="xs" type="primary" @click="showScore(row.basicid)">成绩</lay-button>
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delBasic(row.basicid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showConfigPage" :area="['1200px','80vh']" :btn="showConfigPageBtn" title="考场设置">
		<div style="padding: 20px;">
			<lay-form :model="basicModel" :pane="false" size="md" :labelWidth="150" class="form" ref="configPageFrom">
				<lay-field title="考试范围设定">
					<lay-form-item :label="section" prop="basicpoint[sid]" v-for="(section,sid) in sections">
						<lay-select v-model="basicModel.basicpoint[sid]" multiple allow-clear placeholder="请选择" style="width:90%">
							<lay-select-option :value="pid" :label="point" v-for="(point,pid) in points[sid]" :key="pid"></lay-select-option>
						</lay-select>
					</lay-form-item>
				</lay-field>
				<lay-field title="考场配置">
					<lay-form-item label="配比设定" prop="basicexam.rulemodel" required>
						<lay-radio v-model="basicModel.basicexam.rulemodel" name="rulemodel" :value="0" label="考试范围约束题型配比"></lay-radio>
						<lay-radio v-model="basicModel.basicexam.rulemodel" name="rulemodel" :value="1" label="考试范围不约束题型配比"></lay-radio>
					</lay-form-item>
					<lay-form-item label="考场模式" prop="basicexam.model" required>
						<lay-radio v-model="basicModel.basicexam.model" name="model" :value="0" label="全功能模式【显示试题库】"></lay-radio>
						<lay-radio v-model="basicModel.basicexam.model" name="model" :value="1" label="练习模式【不显示试题库】"></lay-radio>
						<lay-radio v-model="basicModel.basicexam.model" name="model" :value="2" label="考试模式"></lay-radio>
					</lay-form-item>
					<lay-form-item label="试题排序" prop="basicexam.changesequence" required>
						<lay-radio v-model="basicModel.basicexam.changesequence" name="changesequence" :value="0" label="正式考试不打乱试题"></lay-radio>
						<lay-radio v-model="basicModel.basicexam.changesequence" name="changesequence" :value="1" label="正式考试打乱试题"></lay-radio>
					</lay-form-item>
					<lay-form-item label="模拟考试试卷" prop="basicexam.auto" required>
						<lay-select v-model="basicModel.basicexam.auto" multiple allow-clear placeholder="请选择" style="width:90%">
							<lay-select-option :value="pid" :label="paper" v-for="(paper,pid) in papers" :key="pid"></lay-select-option>
						</lay-select>
					</lay-form-item>
					<lay-form-item label="模拟考试试卷模板" prop="basicexam.autotemplate" required>
						<lay-select v-model="basicModel.basicexam.autotemplate" placeholder="请选择">
							<lay-select-option value="0" label="整页显示"></lay-select-option>
							<lay-select-option value="1" label="一页一题"></lay-select-option>
						</lay-select>
					</lay-form-item>
					<lay-form-item label="正式考试试卷" prop="basicexam.self" required>
						<lay-select v-model="basicModel.basicexam.self" multiple allow-clear placeholder="请选择" style="width:90%">
							<lay-select-option :value="pid" :label="paper" v-for="(paper,pid) in papers" :key="pid"></lay-select-option>
						</lay-select>
					</lay-form-item>
					<lay-form-item label="正式考试试卷模板" prop="basicexam.selftemplate" required>
						<lay-select v-model="basicModel.basicexam.selftemplate" placeholder="请选择">
							<lay-select-option value="0" label="整页显示"></lay-select-option>
							<lay-select-option value="1" label="一页一题"></lay-select-option>
						</lay-select>
					</lay-form-item>
					<lay-form-item label="正式考试开始时间" prop="basicexam.opentime">
						<lay-space><lay-date-picker v-model="basicModel.basicexam.opentime" type="datetime"></lay-date-picker></lay-space>
						<lay-space>不填写为不限制开始时间</lay-space>
					</lay-form-item>
					<lay-form-item label="正式考试结束时间" prop="basicexam.closetime">
						<lay-space><lay-date-picker v-model="basicModel.basicexam.closetime" type="datetime"></lay-date-picker></lay-space>
						<lay-space>不填写为不限制结束时间</lay-space>
					</lay-form-item>
					<lay-form-item label="正式考试抽卷规则" prop="basicexam.selectrule" required>
						<lay-radio v-model="basicModel.basicexam.selectrule" name="selectrule" :value="0" label="系统随机抽卷"></lay-radio>
						<lay-radio v-model="basicModel.basicexam.selectrule" name="selectrule" :value="1" label="用户手动选卷"></lay-radio>
					</lay-form-item>
					<lay-form-item label="未答完题禁止交卷" prop="basicexam.fullsubmit" required>
						<lay-radio v-model="basicModel.basicexam.fullsubmit" name="fullsubmit" :value="0" label="关闭"></lay-radio>
						<lay-radio v-model="basicModel.basicexam.fullsubmit" name="fullsubmit" :value="1" label="开启"></lay-radio>
					</lay-form-item>
					<lay-form-item label="开考限制" prop="basicexam.unjointime" required>
						<lay-space><lay-input v-model="basicModel.basicexam.unjointime" placeholder=""></lay-input></lay-space>
						<lay-space>（填写分钟数，开考经过设定分钟时间后禁止抽卷，不限请填写0）</lay-space>
					</lay-form-item>
					<lay-form-item label="限考次数" prop="basicexam.examnumber" required>
						<lay-space><lay-input v-model="basicModel.basicexam.examnumber" placeholder=""></lay-input></lay-space>
						<lay-space>（正式考试最多考试次数，不限请填写0）</lay-space>
					</lay-form-item>
					<lay-form-item label="切屏次数" prop="basicexam.screennumber" required>
						<lay-space><lay-input v-model="basicModel.basicexam.screennumber" placeholder=""></lay-input></lay-space>
						<lay-space>（考试时允许的切屏次数，不限请填写0）</lay-space>
					</lay-form-item>
					<lay-form-item label="交卷后" prop="basicexam.notviewscore" required>
						<lay-radio v-model="basicModel.basicexam.notviewscore" name="notviewscore" :value="0" label="直接显示分数"></lay-radio>
						<lay-radio v-model="basicModel.basicexam.notviewscore" name="notviewscore" :value="1" label="暂不显示分数"></lay-radio>
					</lay-form-item>
				</lay-field>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showAddPage" :area="['800px']" :btn="showAddPageBtn" title="添加考场">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="model" :pane="false" size="md" :labelWidth="100" class="form" ref="addPageFrom">
				<lay-form-item label="考场名称" prop="basic" required>
					<lay-input v-model="model.basic" placeholder="考场名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="考场缩略图" prop="basicthumb">
					<myThumb v-model:src="model.basicthumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="考试科目" prop="basicsubjectid" required>
					<lay-select v-model="model.basicsubjectid" placeholder="请选择考试科目">
						<lay-select-option v-for="(subject,sid) in subjects" :key="Number(sid)" :value="Number(sid)" :label="subject"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="考场简介" prop="basicdescribe">
					<lay-textarea placeholder="请输入考场简介" v-model="model.basicdescribe"></lay-textarea>
				</lay-form-item>
                <lay-form-item label="考场详情" prop="basictext">
                    <myEditor v-model:content="model.basictext"></myEditor>
                </lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :area="['800px']" :btn="showModifyPageBtn" title="编辑考场">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modify" :pane="false" size="md" :labelWidth="100" class="form" ref="modifyPageFrom">
				<lay-form-item label="考场名称" prop="basic" required>
					<lay-input v-model="modify.basic" placeholder="考场名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="考场缩略图" prop="basicthumb">
					<myThumb v-model:src="modify.basicthumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="考试科目" prop="basicsubjectid" required>
					<lay-select v-model="modify.basicsubjectid" placeholder="请选择考试科目">
						<lay-select-option v-for="(subject,sid) in subjects" :key="Number(sid)" :value="Number(sid)" :label="subject"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="考场简介" prop="basicdescribe">
					<lay-textarea placeholder="请输入考场简介" v-model="modify.basicdescribe"></lay-textarea>
				</lay-form-item>
                <lay-form-item label="考场详情" prop="basictext">
                    <myEditor v-model:content="modify.basictext"></myEditor>
                </lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import examApi from '@/framework/api/admin/exam.js';
import {layer} from '@layui/layui-vue';
import myThumb from '@/components/desktop/Thumb.vue';
import {withConfirm, withLayer} from "@/framework/utils/decorators.js";
import MyEditor from "@/components/master/Editor.vue";

export default {
	name:'examBasic',
	data() {
		return {
			dataSource:[],
			columns:[{
				title: '复选',
				type: "checkbox",
				width: '40px',
				fixed: "left"
			}, {
				title: 'ID',
				key: 'basicid',
				width: '20px'
			}, {
				title: '缩略图',
				key: 'basicthumb',
				customSlot: 'basicthumb',
				width: '80px'
			}, {
				title: '考场名称',
				key: 'basic'
			}, {
				title: '考试科目',
				key: 'subject',
				customSlot: 'subject',
				width: '200px'
			}, {
                title: '开通人数',
                key: 'basicnumber',
                width: '80px'
            }, {
				title: '操作',
				customSlot: "operator",
				key: "operator",
				width: "380px"
			}],
			selectedKeys:[],
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{limit:10,current:1,total:0},
			search:{},
			subjects:[],
			areas:[],
			model:{},
			modify:{},
			showAddPage:false,
			showModifyPage:false,
			showConfigPage:false,
			basicModel:{},
			sections:{},
			points:{},
			papers:[],
			showAddPageBtn:[
				{
					text: "确认",
					callback: () => {
						console.log(this.model);
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showAddPage = false;
							this.addBasic();
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
			showModifyPageBtn:[
				{
					text: "确认",
					callback: () => {
						this.$refs['modifyPageFrom'].validate().then((res) => {
							this.showModifyPage = false;
							this.modifyBasic();
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
			showConfigPageBtn:[
				{
					text: "确认",
					callback: () => {
						this.$refs['configPageFrom'].validate().then((res) => {
							this.showConfigPage = false;
							this.submitConfig();
						}).catch( res => {
							console.log(res);
						});
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showConfigPage = false;
					}
				}
			]
		}
	},
	async mounted() {
		this.subjects = await examApi.getAllSubjects();
	},
	async activated(){
		await this.getData();
	},
	components:{
        MyEditor,
		myThumb
	},
	methods:{
		base:async function(fn){
			await withLayer(fn,	null,this.getData);
		},
		getData:async function(){
			await withLayer(
					async () => {
						const data = await examApi.getBasicList({
							search: this.search,
							limit: this.page.limit,
							page: this.page.current
						});
						this.page.page = data.page;
						this.page.total = data.total;
						this.page.limit = data.limit;
						this.dataSource = data.data;
					},[null,null]
			);
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		showModify:function(row){
			this.modify = JSON.parse(JSON.stringify(row));
			this.modify.basicstatus = this.modify.basicstatus === 1
			this.showModifyPage = true
		},
		delBasic:function(id){
			withConfirm('确定要删除吗？', async () => {
				await examApi.delBasics(id?[id]:this.selectedKeys);
			},this.getData)

		},
		addBasic:async function(){
			this.base( async() => {
				await examApi.addBasic(this.model);
			});
		},
		modifyBasic:function(){
			this.base( async() => {
				await examApi.modifyBasic(this.modify);
			});
		},
		submitConfig:async function(){
			await withLayer(
					async() => {
						await examApi.modifyBasic({
							basicid:this.basicModel.basicid,
							basicpoint:this.basicModel.basicpoint,
							basicexam:this.basicModel.basicexam
						});
					},
					null,this.getData
			);
		},
		config:async function(basicid){
			await withLayer(
					async() => {
						this.basicModel = await examApi.getBasic(basicid);
						this.papers = await examApi.getAllPapers({
							subjectid:this.basicModel.basicsubjectid,
							search:{}
						});
						if(this.basicModel.basicsubjectid > 0)
						{
							this.sections = await examApi.getAllSections({
								subjectid:this.basicModel.basicsubjectid
							});
						}
						else this.sections = [];
						if(this.basicModel.basicsubjectid > 0)
						{
							this.points = await examApi.getAllPoints({
								subjectid:this.basicModel.basicsubjectid
							});
						}
						else this.points = {};
						this.showConfigPage = true;
					},[null,'加载失败']
			)
		},
        showBasicPrice:async function(basicId){
            this.$router.push('/desktop/master/exam/price/' + basicId);
        },
		showUnScore:function(basicId){
			this.$router.push('/desktop/master/exam/decide/' + basicId);
		},
		monitor:function(basicId){
			this.$router.push('/desktop/master/exam/monitor/' + basicId);
		},
        showScore:function(basicId){
            this.$router.push('/desktop/master/exam/history/' + basicId);
        },
        showMember:function(basicId){
            this.$router.push('/desktop/master/exam/member/' + basicId);
        },
	}
}
</script>