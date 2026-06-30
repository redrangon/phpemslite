<template>
	<lay-card>
		<lay-space direction="vertical">
			<lay-space size="lg">
				<lay-space></lay-space>
				<lay-space>
					<span style='width:70px'> 关键字：</span><lay-input v-model="search.keyword"></lay-input>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 题型：</span>
					<lay-select v-model="search.questype" allow-clear placeholder="请选择" style="min-width: 180px;width:100%">
						<lay-select-option v-for="(questype,qid) in questionTypes" :key="qid" :label="questype.questype" :value="questype.questid"></lay-select-option>
					</lay-select>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 难度：</span><lay-input v-model="search.level"></lay-input>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 录入时间：</span>
					<lay-date-picker v-model="search.range" :allow-clear="true" :placeholder="['开始日期','结束日期']" range></lay-date-picker>
				</lay-space>
			</lay-space>
			<lay-space size="lg">
				<lay-space></lay-space>
				<lay-space>
					<span style='width:70px'> 科目：</span>
					<lay-select v-model="search.subjectid" allow-clear placeholder="请选择" @change="changeSubject(search.subjectid)">
						<lay-select-option v-for="(subject,sid) in subjects" :key="sid" :label="subject" :value="sid"></lay-select-option>
					</lay-select>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 章节：</span>
					<lay-select v-model="search.sections" allow-clear multiple placeholder="请选择" style="width:100%;min-width: 180px;" @change="changeSection(search.sections)">
						<lay-select-option v-for="(section,seid) in sections" :key="seid" :label="section" :value="seid"></lay-select-option>
					</lay-select>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 知识点：</span>
					<lay-select v-model="search.points" allow-clear multiple placeholder="请选择" style="min-width: 180px;width:100%">
						<lay-select-option v-for="(point,pid) in points" :key="pid" :label="point" :value="pid"></lay-select-option>
					</lay-select>
				</lay-space>
				<lay-space>
					<lay-button type="primary" @click="getData">搜索</lay-button>
					<lay-button style="display: none;" type="primary" @click="exportQuestion">导出试题</lay-button>
					<lay-button type="danger" @click="batDelQuestions">删除</lay-button>
				</lay-space>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
        <lay-quote>添加或删除试题后，请到科目管理中，更新对应科目的缓存。</lay-quote>
		<lay-table id="qrid" ref="tableRef" v-model:selected-keys="selectedKeys" :columns="columns" :data-source="dataSource" :default-toolbar="false" even @change="getData">
			<template #toolbar>
				<lay-button size="sm" type="primary" @click="model = {};showAddPage = true">添加试题</lay-button>
                <lay-button size="sm" type="primary" @click="importQuestion">导入试题</lay-button>
                <a href="attach/rowsquestion.xlsx" style="margin-left:10px;"><lay-button size="sm" type="danger">下载试题模板</lay-button></a>
			</template>
			<template #footer>
				<lay-row>
					<lay-col md="12">
						<lay-button :disabled="selectedKeys.length < 1" size="sm" type="primary" @click="delQuestion()">删除选中数据</lay-button>
					</lay-col>
					<lay-col md="12">
						<lay-page v-model="page.current" v-model:limit="page.limit" :layout="layout" :total="page.total" style="float:right;" @change="changePage"></lay-page>
					</lay-col>
				</lay-row>
			</template>
			<template v-slot:questiontypename="{ row }">
				{{questionTypes[row.questiontype]?.questype}}
			</template>
			<template v-slot:questionlevelname="{ row }">
				{{levels[row.questionlevel]}}
			</template>
			<template v-slot:operator="{ row }">
                <lay-button size="xs" type="primary" @click="showQuestion(row.questionid)">预览</lay-button>
				<lay-button size="xs" type="primary" @click="showChildQuestion(row.questionid)">子试题</lay-button>
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delQuestion(row.questionid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showQuestionPage" :area="['960px','90vh']" :shade="true" :shadeClose="false" shadeOpacity="0.6" title="预览试题">
		<div style="padding: 20px 50px 20px 20px;">
			<div style="margin-top: 10px;padding:10px;display: block;" v-html="modelShow.question"></div>
			<template v-for="(children,key) in modelShow.data" :key="key">
				<lay-line>第{{key + 1}}题</lay-line>
				<myQuestion :disabled="true" :question="children" :questionType="questionTypes[children.questiontype]" :userAnswer="children.questionanswer" childIndex="1" index="1"></myQuestion>
			</template>
		</div>
	</lay-layer>
	<lay-layer v-model="showAddPage" :area="['960px','90vh']" :btn="addPageBtns" :shade="true" :shadeClose="false" shadeOpacity="0.6" title="添加试题">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form ref="addPageFrom" :labelWidth="100" :model="model" :pane="false" class="form" size="md">
				<lay-form-item label="科目/章节">
					<lay-space size="md">
						<lay-space>
							<lay-select v-model="model.subjectid" allow-clear placeholder="请选择" @change="changeSubject(model.subjectid)">
								<lay-select-option v-for="(subject,sid) in subjects" :key="sid" :label="subject" :value="sid"></lay-select-option>
							</lay-select>
						</lay-space>
						<lay-space>
							<lay-select v-model="model.sections" allow-clear multiple placeholder="请选择" style="width:525px" @change="changeSection(model.sections)">
								<lay-select-option v-for="(section,seid) in sections" :key="seid" :label="section" :value="seid"></lay-select-option>
							</lay-select>
						</lay-space>
					</lay-space>
				</lay-form-item>
				<lay-form-item label="知识点" prop="points" required>
					<lay-select v-model="model.points" allow-clear multiple placeholder="请选择" style="width:100%">
						<lay-select-option v-for="(point,pid) in points" :key="pid" :label="point" :value="pid"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="题型" prop="questiontype" required>
					<lay-select v-model="model.questiontype" placeholder="请选择">
						<lay-select-option v-for="(questype,questid) in questionTypes" :key="questid" :label="questype.questype" :value="questype['questid']"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="题干" prop="question" required>
					<myEditor v-model:content="model.question"></myEditor>
				</lay-form-item>
				<lay-form-item label="难度" prop="questionlevel" required>
					<lay-select v-model="model.questionlevel" placeholder="请选择">
						<lay-select-option v-for="(level,lid) in levels" :label="level" :value="Number(lid)"></lay-select-option>
					</lay-select>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage"  :area="['960px','90vh']" :btn="modifyPageBtns" :shade="true" :shadeClose="false" shadeOpacity="0.6" title="修改试题">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form ref="modifyPageFrom" :labelWidth="100" :model="modelModify" :pane="false" class="form" size="md">
				<lay-form-item label="题型" prop="questiontype" required>
					<lay-select v-model="modelModify.questiontype" placeholder="请选择">
						<lay-select-option v-for="(questype,questid) in questionTypes" :key="questid" :label="questype.questype" :value="questype['questid']"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="题干" prop="question" required>
					<myEditor v-model:content="modelModify.question"></myEditor>
				</lay-form-item>
				<lay-form-item label="难度" prop="questionlevel" required>
					<lay-select v-model="modelModify.questionlevel" placeholder="请选择">
						<lay-select-option v-for="(level,lid) in levels" :label="level" :value="Number(lid)"></lay-select-option>
					</lay-select>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import examApi from '@/framework/api/admin/exam.js';
import {layer} from '@layui/layui-vue';
import myEditor from '@/components/master/Editor.vue';
import myQuestion from '@/components/master/Question.vue';
import baseMixin from "@/framework/mixins/baseMixin.js";

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
				key: 'qkid',
				width: '60px'
			}, {
				title: '题目ID',
				key: 'questionid',
				width: '60px'
			}, {
				title: '试题',
				key: 'question'
			}, {
				title: '题型',
				key: 'questiontypename',
				customSlot: 'questiontypename',
				width: '120px'
			}, {
				title: '难度',
				key: 'questionlevelname',
				customSlot: 'questionlevelname',
				width: '80px'
			}, {
				title: '子题数',
				key: 'questionchildnumber',
				width: '60px'
			}, {
				title: '操作',
				customSlot: "operator",
				key: "operator",
				width: "210px"
			}],
			dataSource:[],
			subjects:[],
			sections:[],
			points:[],
			search:{},
			selectedKeys:[],
			selectors:['A','B','C','D','E','F','G','H'],
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			levels:{'1':'易','2':'中','3':'难'},
			page:{
				limit:20,current:1,total:0
			},
			questionTypes:{},
			showAddPage:false,
			showModifyPage:false,
			showQuestionPage:false,
			model:{},
			modelModify:{},
			modelShow:{},
			addPageBtns:[
				{
					text: "确认",
					callback: () => {
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showAddPage = false;
							this.addQuestion();
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
							this.modifyQuestion();
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
	components:{myEditor,myQuestion},
	async mounted() {
		if(this.$route.params.questionid)this.search.questionid = this.$route.params.questionid;
		await this.getQuestionTypes();
		await this.getSubjects();
	},
	async activated(){
		await this.getData();
	},
	methods:{
		getQuestionTypes:async function(){
			this.questionTypes = await examApi.getAllQuestionTypes();
		},
		getSubjects:async function(){
			this.subjects = await examApi.getAllSubjects();
		},
		getData:async function(){
			await this.execute(async () => {
				const data = await examApi.getQuestionList({
					search:this.search,
					haschildren:1,
					limit:this.page.limit,
					page:this.page.current
				})
				this.page.page = data.page;
				this.page.total = data.total;
				this.page.limit = data.limit;
				this.dataSource = data.data;
			},null,null);
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		showQuestion:async function(id){
			this.showQuestionPage = true;
			this.modelShow = await examApi.getQuestion(id);
		},
		showModify:function(row){
			this.modelModify = {...row};
			this.showModifyPage = true;
		},
		batDelQuestions:function(){
			this.confirmDelete(async () => {
				await examApi.delQuestions({
					search:this.search
				});
			},this.getData)
		},
		delQuestion:function(id){
			this.confirmDelete(async () => {
				await examApi.delQuestions({
					ids:id?[id]:this.selectedKeys
				});
			},this.getData)
		},
		addQuestion:function(){
			this.base(async() => {
				await examApi.addQuestion({...this.model,questionisparent:1})
			});
		},
		modifyQuestion:function(){
			this.base(async() => {
				await examApi.modifyQuestion(this.modelModify)
			});
		},
		importQuestion:function(){
			let input = document.createElement('input');
			input.setAttribute('type', 'file');
			input.setAttribute('accept', '.xlsx');
			input.click();
			input.onchange = async () => {
				let formData = new FormData();
				const id = layer.load(0);
				formData.append('api','importquestions');
				formData.append('file', input.files[0], input.files[0].name );
				await examApi.importQuestion(formData);
				layer.close(id);
				await this.getData();
			};
		},
		exportQuestion:async function(){
			const id = layer.load(0);
			const data = await examApi.exportQuestion({
				search:this.search
			});
			let a = document.createElement("a");
			a.download = "data.xlsx";
			// 创建二进制对象
			const blob = new Blob([data]);
			const downloadURL = (window.URL || window.webkitURL).createObjectURL(blob);
			a.href = downloadURL;
			a.click();
			URL.revokeObjectURL(downloadURL);
			layer.close(id);
		},
		changeSubject:async function(subjectId){
			if(subjectId > 0)
			{
				this.sections = await examApi.getAllSections({
					subjectid:subjectId
				});
			}
			else this.sections = [];
		},
		changeSection:async function(sectionId){
			if(sectionId && sectionId.length > 0)
			{
				this.points = await examApi.getAllPoints({
					sectionid:sectionId,
					singletype:true
				});
			}
			else this.points = [];
		},
		showChildQuestion:function(qrid){
			this.$router.push('/desktop/master/exam/children/'+qrid);
		}
	}
}
</script>