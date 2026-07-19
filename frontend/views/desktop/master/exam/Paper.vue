<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 关键字：</span><lay-input v-model="search.keyword"></lay-input>
			</lay-space>
			<lay-space>
				<span style='width:70px'> 科目：</span>
				<lay-select v-model="search.subjectid" allow-clear placeholder="请选择">
					<lay-select-option v-for="(subject,sid) in subjects" :key="sid" :label="subject" :value="sid"></lay-select-option>
				</lay-select>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-table id="examid" ref="tableRef" v-model:selected-keys="selectedKeys" :columns="columns" :data-source="dataSource" :default-toolbar="false" even>
			<template #toolbar>
				<lay-button-group>
					<lay-button type="primary">添加试卷</lay-button>   
					<lay-dropdown placement="bottom-end">
						<lay-button style="padding-left:8px;padding-right:8px;" type="primary">
							<lay-icon type="layui-icon-down"></lay-icon>
						</lay-button>
						<template #content>
							<lay-dropdown-menu style="width: 130px">
								<lay-dropdown-menu-item v-for="(paperType,ptid) in paperTypes" @click="showAdd(Number(ptid))">{{paperType}}</lay-dropdown-menu-item>
							</lay-dropdown-menu>
						</template>
					</lay-dropdown>
				</lay-button-group>
			</template>
			<template #footer>
				<lay-row>
					<lay-col md="12">
						<lay-button :disabled="selectedKeys.length < 1" size="sm" type="primary" @click="delPaper()">删除选中数据</lay-button>
					</lay-col>
					<lay-col md="12">
						<lay-page v-model="page.current" v-model:limit="page.limit" :layout="layout" :total="page.total" style="float:right;" @change="changePage"></lay-page>
					</lay-col>
				</lay-row>
			</template>
			<template v-slot:examtypename="{ row }">
				{{paperTypes[row.examtype]}}
			</template>
			<template v-slot:operator="{ row }">
				<lay-button v-if="row.examtype === 2" size="xs" type="primary" @click="showSetScore(row)">设分</lay-button>
                <lay-button v-if="row.examtype === 2" size="xs" type="primary" @click="downloadPaper(row.examid)">下载</lay-button>
				<lay-button size="xs" type="primary" @click="showModify(row.examid)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delPaper(row.examid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :area="['1060px','90vh']" :btn="addPageBtns" :shade="true" title="添加试卷">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form ref="addPageFrom" :labelWidth="100" :model="model" :pane="false" class="form" size="md">
				<lay-form-item label="考试科目" prop="examsubject" required>
					<lay-select v-model="model.examsubject" :show-search="true" allow-clear placeholder="请选择" @change="changeSubject(model.examsubject)">
						<lay-select-option v-for="(subject,sid) in subjects" :key="sid" :label="subject" :value="Number(sid)"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="试卷名称" prop="exam" required>
					<lay-input v-model="model.exam" placeholder="请输入试卷名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="考试时间" prop="examtotaltime" required>
					<lay-space><lay-input v-model="model.examtotaltime" placeholder="请输入考试时间"></lay-input></lay-space>
					<lay-space>（分钟）</lay-space>
				</lay-form-item>
				<lay-form-item label="总分" prop="examtotalscore" required>
					<lay-space><lay-input v-model="model.examtotalscore" placeholder="请输入总分"></lay-input></lay-space>
					<lay-space>（分）</lay-space>
				</lay-form-item>
				<lay-form-item label="及格线" prop="exampassmark" required>
					<lay-space><lay-input v-model="model.exampassmark" placeholder="请输入及格线"></lay-input></lay-space>
					<lay-space>（分）</lay-space>
				</lay-form-item>
				<div v-if="model.examsubject && model.examtype === 1">
					<lay-form-item label="题型排序" prop="examsetting.lite" required>
						<mySort v-model="model.examsetting.lite" :datas="model.sort"></mySort>
					</lay-form-item>
					<lay-form-item label="题型配比" prop="examscalemodel" required>
						<lay-radio-group v-model="model.examscalemodel">
							<lay-radio-button :value="0" label="关闭"></lay-radio-button>
							<lay-radio-button :value="1" label="开启"></lay-radio-button>
						</lay-radio-group>
					</lay-form-item>
					<div v-if="model.examscalemodel === 1">
						<div v-for="questid in model.examsetting.lite">
							<lay-form-item :label="questionTypes[questid].questype">
								<lay-space>共 <lay-input v-model="model.examsetting.questionTypes[questid].number" style="width:50px"></lay-input> 题 &nbsp;</lay-space>
								<lay-space>每题 <lay-input v-model="model.examsetting.questionTypes[questid].score" style="width:50px"></lay-input> 分&nbsp;</lay-space>
								<lay-space>题型描述 <lay-input v-model="model.examsetting.questionTypes[questid].describe" style="width:240px"></lay-input>&nbsp;</lay-space>
							</lay-form-item>
							<lay-form-item label="配比率">
								<lay-textarea v-model="model.examsetting.examScale[questid]" placeholder="知识点ID1,知识点ID2:总题数:易题数,中题数,难题数；每行一个，可设置多行" rows="6"></lay-textarea>
							</lay-form-item>
						</div>
					</div>
					<div v-else>
						<lay-form-item v-for="questid in model.examsetting.lite" :label="questionTypes[questid].questype">
							<lay-space>共 <lay-input v-model="model.examsetting.questionTypes[questid].number" style="width:50px"></lay-input> 题 &nbsp;</lay-space>
							<lay-space>每题 <lay-input v-model="model.examsetting.questionTypes[questid].score" style="width:50px"></lay-input> 分&nbsp;</lay-space>
							<lay-space>题型描述 <lay-input v-model="model.examsetting.questionTypes[questid].describe" style="width:240px"></lay-input>&nbsp;</lay-space>
							<lay-space> 易 <lay-input v-model="model.examsetting.questionTypes[questid].easyNumber" style="width:50px"></lay-input>&nbsp;</lay-space>
							<lay-space> 中 <lay-input v-model="model.examsetting.questionTypes[questid].middleNumber" style="width:50px"></lay-input>&nbsp;</lay-space>
							<lay-space> 难 <lay-input v-model="model.examsetting.questionTypes[questid].hardNumber" style="width:50px"></lay-input>&nbsp;</lay-space>
						</lay-form-item>
					</div>
				</div>
				<div v-else-if="model.examsubject && model.examtype === 2">
					<lay-form-item label="题型排序" prop="examsetting.lite" required>
						<mySort v-model="model.examsetting.lite" :datas="model.sort"></mySort>
					</lay-form-item>
					<lay-form-item v-for="questid in model.examsetting.lite" :label="questionTypes[questid].questype">
						<lay-space>共 <lay-input v-model="model.examsetting.questionTypes[questid].number" style="width:50px"></lay-input> 题 &nbsp;</lay-space>
						<lay-space>每题 <lay-input v-model="model.examsetting.questionTypes[questid].score" style="width:50px"></lay-input> 分&nbsp;</lay-space>
						<lay-space>题型描述 <lay-input v-model="model.examsetting.questionTypes[questid].describe" style="width:200px"></lay-input>&nbsp;</lay-space>
						<lay-space>
							<lay-button style="width:116px;" type="primary" @click="showSelectedQuestions(questid)">普通题（{{selectednumber[questid]?.question??0}}）</lay-button>
							<lay-button style="width:116px;" type="primary" @click="showSelectedQuestions(questid,1)">题帽题（{{selectednumber[questid]?.rowsquestion??0}}）</lay-button>
							<lay-button type="primary" @click="selectQuestions(questid)">选题</lay-button>
                            <lay-button type="primary" @click="clearSelectedQuestions(questid)">清空</lay-button>
						</lay-space>
					</lay-form-item>
				</div>
				<div v-else>
					<lay-form-item label="其他设置">
						<lay-space>
							<lay-input :disabled="true" placeholder="请选择科目"></lay-input>
						</lay-space>
					</lay-form-item>
				</div>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :area="['1060px','90vh']" :btn="modifyPageBtns" :shade="true" title="修改试卷">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form ref="modifyPageFrom" :labelWidth="100" :model="model" :pane="false" class="form" size="md">
				<lay-form-item label="试卷名称" prop="exam" required>
					<lay-input v-model="model.exam" placeholder="请输入试卷名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="考试时间" prop="examtotaltime" required>
					<lay-space><lay-input v-model="model.examtotaltime" placeholder="请输入考试时间"></lay-input></lay-space>
					<lay-space>（分钟）</lay-space>
				</lay-form-item>
				<lay-form-item label="总分" prop="examtotalscore" required>
					<lay-space><lay-input v-model="model.examtotalscore" placeholder="请输入总分"></lay-input></lay-space>
					<lay-space>（分）</lay-space>
				</lay-form-item>
				<lay-form-item label="及格线" prop="exampassmark" required>
					<lay-space><lay-input v-model="model.exampassmark" placeholder="请输入及格线"></lay-input></lay-space>
					<lay-space>（分）</lay-space>
				</lay-form-item>
				<div v-if="model.examsubject && model.examtype === 1">
					<lay-form-item label="题型排序" prop="examsetting.lite" required>
						<mySort v-model="model.examsetting.lite" :datas="model.sort"></mySort>
					</lay-form-item>
					<lay-form-item label="题型配比" prop="examscalemodel" required>
						<lay-radio-group v-model="model.examscalemodel">
							<lay-radio-button :value="0" label="关闭"></lay-radio-button>
							<lay-radio-button :value="1" label="开启"></lay-radio-button>
						</lay-radio-group>
					</lay-form-item>
					<div v-if="model.examscalemodel === 1">
						<div v-for="questid in model.examsetting.lite">
							<lay-form-item :label="questionTypes[questid].questype">
								<lay-space>共 <lay-input v-model="model.examsetting.questionTypes[questid].number" style="width:50px"></lay-input> 题 &nbsp;</lay-space>
								<lay-space>每题 <lay-input v-model="model.examsetting.questionTypes[questid].score" style="width:50px"></lay-input> 分&nbsp;</lay-space>
								<lay-space>题型描述 <lay-input v-model="model.examsetting.questionTypes[questid].describe" style="width:240px"></lay-input>&nbsp;</lay-space>
							</lay-form-item>
							<lay-form-item label="配比率">
								<lay-textarea v-model="model.examsetting.examScale[questid]" placeholder="知识点ID1,知识点ID2:总题数:易题数,中题数,难题数；每行一个，可设置多行" rows="6"></lay-textarea>
							</lay-form-item>
						</div>
					</div>
					<div v-else>
						<lay-form-item v-for="questid in model.examsetting.lite" :label="questionTypes[questid].questype">
							<lay-space>共 <lay-input v-model="model.examsetting.questionTypes[questid].number" style="width:50px"></lay-input> 题 &nbsp;</lay-space>
							<lay-space>每题 <lay-input v-model="model.examsetting.questionTypes[questid].score" style="width:50px"></lay-input> 分&nbsp;</lay-space>
							<lay-space>题型描述 <lay-input v-model="model.examsetting.questionTypes[questid].describe" style="width:240px"></lay-input>&nbsp;</lay-space>
							<lay-space> 易 <lay-input v-model="model.examsetting.questionTypes[questid].easyNumber" style="width:50px"></lay-input>&nbsp;</lay-space>
							<lay-space> 中 <lay-input v-model="model.examsetting.questionTypes[questid].middleNumber" style="width:50px"></lay-input>&nbsp;</lay-space>
							<lay-space> 难 <lay-input v-model="model.examsetting.questionTypes[questid].hardNumber" style="width:50px"></lay-input>&nbsp;</lay-space>
						</lay-form-item>
					</div>
				</div>
				<div v-else-if="model.examsubject && model.examtype === 2">
					<lay-form-item label="题型排序" prop="examsetting.lite" required>
						<mySort v-model="model.examsetting.lite" :datas="model.sort"></mySort>
					</lay-form-item>
					<lay-form-item v-for="questid in model.examsetting.lite" :label="questionTypes[questid].questype">
						<lay-space>共 <lay-input v-model="model.examsetting.questionTypes[questid].number" style="width:50px"></lay-input> 题 &nbsp;</lay-space>
						<lay-space>每题 <lay-input v-model="model.examsetting.questionTypes[questid].score" style="width:50px"></lay-input> 分&nbsp;</lay-space>
						<lay-space>题型描述 <lay-input v-model="model.examsetting.questionTypes[questid].describe" style="width:200px"></lay-input>&nbsp;</lay-space>
						<lay-space>
							<lay-button style="width:116px;" type="primary" @click="showSelectedQuestions(questid)">普通题（{{selectednumber[questid].question}}）</lay-button>
							<lay-button style="width:116px;" type="primary" @click="showSelectedQuestions(questid,1)">题帽题（{{selectednumber[questid].rowsquestion}}）</lay-button>
							<lay-button type="primary" @click="selectQuestions(questid)">选题</lay-button>
                            <lay-button type="primary" @click="clearSelectedQuestions(questid)">清空</lay-button>
						</lay-space>
					</lay-form-item>
				</div>
				<div v-else>
					<lay-form-item label="其他设置"><lay-space><lay-input :disabled="true" placeholder="请选择科目"></lay-input></lay-space></lay-form-item>
				</div>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showSelectQuestionPage" :area="['1060px','90vh']" :btn="showSelectQuestionPageBtns" :shade="true" title="选题">
		<div style="padding: 20px 20px 20px 20px;">
			<lay-space direction="vertical">
				<lay-space size="lg">
					<lay-space>
						<span style='width:70px'> 关键字：</span><lay-input v-model="questionSearch.keyword"></lay-input>
					</lay-space>
					<lay-space>
						<span style='width:70px'> 类型</span>
						<lay-select v-model="questionSearch.haschildren" allow-clear placeholder="请选择" @change="searchQuestion">
							<lay-select-option :value="0" label="普通试题"></lay-select-option>
							<lay-select-option :value="1" label="题冒题"></lay-select-option>
						</lay-select>
					</lay-space>
					<lay-space>
						<lay-button type="primary" @click="searchQuestion">搜索</lay-button>
					</lay-space>
				</lay-space>
				<lay-space size="lg">
					<lay-space>
						<span style='width:70px'> 章节：</span>
						<lay-select v-model="questionSearch.sections" allow-clear multiple placeholder="请选择" style="width:100%;min-width: 180px;" @change="changeSection(questionSearch.sections)">
							<lay-select-option v-for="(section,seid) in sections" :key="seid" :label="section" :value="seid"></lay-select-option>
						</lay-select>
					</lay-space>
					<lay-space>
						<span style='width:70px'> 知识点：</span>
						<lay-select v-model="questionSearch.points" allow-clear multiple placeholder="请选择" style="min-width: 180px;width:100%">
							<lay-select-option v-for="(point,pid) in points" :key="kid" :label="point" :value="pid"></lay-select-option>
						</lay-select>
					</lay-space>
				</lay-space>
				<lay-space>
					<lay-table v-if="questionSearch.haschildren === 1" id="questionid" ref="searchTableRef" v-model:selected-keys="selectedRowsQuestionsKeys" :columns="rowsQuestionColumns" :data-source="questionDataSource" :page="questionPage" even @change="searchQuestion">
						<template v-slot:questiontypename="{ row }">
							{{questionTypes[row.questiontype].questype}}
						</template>
						<template v-slot:questionlevelname="{ row }">
							{{levels[row.questionlevel]}}
						</template>
					</lay-table>
					<lay-table v-else id="questionid" ref="searchTableRef" v-model:selected-keys="selectedQuestionsKeys" :columns="questionColumns" :data-source="questionDataSource" :page="questionPage" even @change="searchQuestion">
						<template v-slot:questiontypename="{ row }">
							{{questionTypes[row.questiontype].questype}}
						</template>
						<template v-slot:questionlevelname="{ row }">
							{{levels[row.questionlevel]}}
						</template>
					</lay-table>
				</lay-space>
			</lay-space>
		</div>
	</lay-layer>
	<lay-layer v-model="showSelectedQuestionsPage" :area="['1060px','90vh']" :btn="showSelectQuestionPageBtns" :shade="true" title="看题">
		<div style="padding: 20px 20px 20px 20px;">
			<lay-table v-if="questionSearch.haschildren === 1" id="questionid" ref="searchTableRef" v-model:selected-keys="selectedRowsQuestionsKeys" :columns="rowsQuestionColumns" :data-source="questionDataSource" :page="questionPage" even @change="searchQuestion">
				<template v-slot:questiontypename="{ row }">
					{{questionTypes[row.questiontype].questype}}
				</template>
				<template v-slot:questionlevelname="{ row }">
					{{levels[row.questionlevel]}}
				</template>
			</lay-table>
			<lay-table v-else id="questionid" ref="searchTableRef" v-model:selected-keys="selectedQuestionsKeys" :columns="questionColumns" :data-source="questionDataSource" :page="questionPage" even @change="searchQuestion">
				<template v-slot:questiontypename="{ row }">
					{{questionTypes[row.questiontype].questype}}
				</template>
				<template v-slot:questionlevelname="{ row }">
					{{levels[row.questionlevel]}}
				</template>
			</lay-table>
		</div>
	</lay-layer>
	<lay-layer v-model="showScorePage" :area="['1060px','90vh']" :btn="showScorePageBtns" :shade="true" title="单题设分">
		<div style="padding: 20px 20px 20px 20px;">
			<lay-form ref="scorePageFrom" :label-width="0" :model="examscore" :pane="false" class="form" size="md">
				<lay-collapse v-model="openScoreKeys">
					<lay-collapse-item v-for="(score,sid) in scores" :id="Number(sid)" :key="sid" :title="questionTypes[sid].questype">
						<lay-space v-if="score.questions">
							<lay-table ref="searchTableRef" :columns="scoreQuestionColumns" :data-source="score.questions" even>
								<template v-slot:score="{ row }">
									<lay-input v-model="examscore.examscore[row.questionid]" placeholder="分数" style="width:100%;"></lay-input>
								</template>
							</lay-table>
						</lay-space>
						<template v-if="score.rowsquestions">
							<lay-space v-for="(rowsquestion,rid) in score.rowsquestions" :key="rid" direction="vertical">
								<lay-quote style="margin-top:10px; ">
									<div v-html="rowsquestion.question"></div>
								</lay-quote>
								<lay-table ref="searchTableRef" :columns="scoreQuestionColumns" :data-source="rowsquestion.data" even>
									<template v-slot:score="{ row }">
										<lay-input v-model="examscore.examscore[row.questionid]" placeholder="分数" style="width:100%;"></lay-input>
									</template>
								</lay-table>
							</lay-space>
						</template>
					</lay-collapse-item>
				</lay-collapse>
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
import mySort from "@/components/master/Sort.vue";

export default {
	data() {
		return {
			formMode:'add',
			columns:[{
				title: "选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			}, {
				title: 'ID',
				key: 'examid',
				width: '20px'
			}, {
				title: '试卷类型',
				key: 'examtypename',
				customSlot: 'examtypename',
				width: '120px'
			}, {
				title: '试卷名称',
				key: 'exam'
			}, {
				title: '科目名称',
				key: 'subject'
			}, {
				title: '总分',
				key: 'examtotalscore',
				width: '80px'
			}, {
				title: '组卷时间',
				key: 'examtime',
				width: '120px'
			}, {
				title: '操作',
				customSlot: "operator",
				key: "operator",
				width: "190px"
			}],
			paperTypes:{
				"1":"随机组卷",
				"2":"手动组卷"
			},
			questionColumns:[{
				title: "选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			}, {
				title: 'ID',
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
				width: '60px'
			}],
			rowsQuestionColumns:[{
				title: "选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			}, {
				title: 'ID',
				key: 'questionid',
				width: '20px'
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
				width: '60px'
			}, {
				title: '子题数',
				key: 'questionchildnumber',
				width: '60px'
			}],
			scoreQuestionColumns:[{
				title: 'ID',
				key: 'questionid',
				width: '20px'
			}, {
				title: '题干',
				key: 'question'
			}, {
				title: '分数',
				customSlot: "score",
				key: "score",
				width: "150px"
			}],
			levels:{'1':'易','2':'中','3':'难'},
			dataSource:[],
			questionDataSource:[],
			rowsQuestionDataSource:[],
			totalscore:null,
			scores:{},
			examScore:{},
			openScoreKeys:[],
			search:{},
			questionSearch:{},
			selectedKeys:[],
			page:{ current: 1, limit: 20, total: 0 },
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			questionPage:{ current: 1, limit: 20, total: 0 },
			subjects:[],
			sections:[],
			points:[],
			questionTypes:{},
			showAddPage:false,
			showModifyPage:false,
			showScorePage:false,
			model:{},
			showSelectQuestionPage:false,
			showSelectedQuestionsPage:false,
			selectedQuestionsKeys:[],
			selectedRowsQuestionsKeys:[],
			selectQuestionSubjectid:{},
			addPageBtns:[
				{
					text: "确认",
					callback: () => {
						this.$refs['addPageFrom'].validate().then((res) => {
							//this.showAddPage = false;
							this.addPaper();
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
							//this.showModifyPage = false;
							this.modifyPaper();
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
			showScorePageBtns:[
				{
					text: "确认",
					callback: () => {
						this.$refs['scorePageFrom'].validate().then((res) => {
							const sumScore = Object.values(this.examscore.examscore).reduce((sum,current) => sum + Number(current),0);
							if(sumScore.toFixed(0) !== this.totalscore.toFixed(0))
							{
								layer.msg('试题分数和与总分不一致');
								return;
							}
							this.showScorePage = false;
							this.setScore();
						}).catch( res => {
							console.log(res);
						});
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showScorePage = false;
					}
				}
			],
			showSelectQuestionPageBtns:[
				{
					text: "确认",
					callback: () => {
						const model = JSON.parse(JSON.stringify(this.model));
						if(!model.examquestions)model.examquestions = {};
						Object.values(this.questionTypes).map(item => {
							if((model.examquestions?.[item.questid]??false) === false)
							{
								model.examquestions[item.questid] = {
									questions:[],
									rowsquestions:[]
								};
							}
						});

						if(this.selectedQuestionsKeys)
						{
							model.examquestions[this.questionSearch.questiontype].questions = this.selectedQuestionsKeys??[];
						}
						if(this.selectedRowsQuestionsKeys)
						{
							model.examquestions[this.questionSearch.questiontype].rowsquestions = this.selectedRowsQuestionsKeys??[];
						}
						this.selectedQuestionsKeys = [];
						this.selectedRowsQuestionsKeys = [];
						this.model = model;
						this.showSelectQuestionPage = false;
						this.showSelectedQuestionsPage = false;
					}
				},
				{
					text: "取消",
					callback: () => {
						this.selectedQuestionsKeys = [];
						this.selectedRowsQuestionsKeys = [];
						this.showSelectQuestionPage = false;
						this.showSelectedQuestionsPage = false;
					}
				}
			]
		}
	},
	components:{
		mySort
	},
	async mounted() {
		await this.getQuestionTypes();
		await this.getSubjects();
		await this.getData();
	},
	methods:{
		base:async function(fn){
			await withLayer(fn,	null,this.getData);
		},
		getQuestionTypes:async function(){
			this.questionTypes = await examApi.getAllQuestionTypes();
		},
		getSubjects:async function(){
			this.subjects = await examApi.getAllSubjects();
		},
		getData:async function(){
			await withLayer(
					async () => {
						const data = await examApi.getPaperList({
							search:this.search,
							limit:this.page.limit,
							page:this.page.current
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
		showAdd:function(examtype){
			this.formMode='add';
			this.showAddPage = true;
			this.model= {
				examsetting: {},
				examquestions:{},
				examtype
			};
		},
		showModify:async function(paperid){
			this.formMode='modify';
			const model = await examApi.getPaper(paperid);
			const sort = {};
			if(model.examsetting.lite)
			{
				model.examsetting.lite.map(item => {
					if(this.questionTypes[item])sort[item] = this.questionTypes[item].questype;
				});
			}
			if(!model.examsetting.examScale)model.examsetting.examScale = {};
			model.sort = sort;
			this.model = model;
			this.showModifyPage = true;
		},
		delPaper:function(id){
			withConfirm('删除后试卷无法恢复，确定要操作吗？',async () => {
				await examApi.delPapers(id?[id]:this.selectedKeys);
			},this.getData)
		},
		addPaper:async function(){
			let checked = this.checkSetting();
			if(checked){
				this.showAddPage = false;
				await examApi.addPaper(this.model);
				await this.getData();
			}
		},
		checkSetting:function(){
			const lite = this.model.examsetting.lite;
			const questionTypes = this.model.examsetting.questionTypes??{};
			let total = 0;
			let hasError = false;
			lite.some(q => {
				if(this.model.examscalemodel === 0 && this.model.examtype === 1){
					let num = Number(questionTypes[q]?.easyNumber??0) + Number(questionTypes[q]?.middleNumber??0) + Number(questionTypes[q]?.hardNumber??0);
					if(Number(questionTypes[q]?.number??0) !== num)
					{
						layer.confirm('题型 '+this.questionTypes[q].questype+' 中，题目总数与各难度题目数量之和不符，请检查！',{title:'错误提示'});
						hasError = true;
						return true;
					}
				}
				if(this.model.examscalemodel === 1){
					const examScale = this.model.examsetting.examScale?.[q]?.split("\n")??[];
					let tol = 0;
					for(let i in examScale)
					{
						let s = examScale[i].split(":");
						if(s[2]){
							let ns = s[2].split(",");
							let n = ns.reduce((sum,current) => sum + Number(current),0);
							if(n !== Number(s[1])){
								layer.confirm('题型 '+this.questionTypes[q].questype+' 中，题目数量与题型配置的题型数量不匹配，请检查1！'+n+'|'+s[1],{title:'错误提示'});
								hasError = true;
								return true;
							}
						}
						console.log(tol);
						tol += Number(s[1]??0);
					}
					if(Number(questionTypes[q].number??0) !== tol)
					{
						layer.confirm('题型 '+this.questionTypes[q].questype+' 中，题目总数与题型配置的题型数量不匹配，请检查！'+tol+'|'+questionTypes[q].number,{title:'错误提示'});
						hasError = true;
						return true;
					}
				}
				console.log(questionTypes,q,questionTypes[q]);
				total += Number(questionTypes[q].score??0) * Number(questionTypes[q].number??0);
				return false;
			});
			if(hasError) {
				return false;
			}
			if(total !== Number(this.model.examtotalscore)) {
				layer.confirm('总分与试题配置分数之和不匹配，请检查！',{title:'错误提示'});
				return false;
			}
			return true;
		},
		modifyPaper:async function(){
			let checked = this.checkSetting();
			if(checked){
				this.showModifyPage = false;
				await examApi.modifyPaper(this.model);
				await this.getData();
			}			
		},
		changeSubject:async function(subjectid){
			const subject = await examApi.getSubject(subjectid)
			const sort = {};
			// 使用深拷贝避免引用共享
			const model = JSON.parse(JSON.stringify(this.model));
			model.examquestions = {};
			model.examsetting.lite = [];
			if(subject.subjectsetting??false)
			{
				subject.subjectsetting.map(item => {
					if(this.questionTypes[item])
					{
						sort[item] = this.questionTypes[item].questype;
						model.examquestions[item] = {
							questions:[],
							rowsquestions:[]
						};
						model.examsetting.lite.push(item);
					}
				});
			}
			model.examsetting.questionTypes = {};
			model.examsetting.examScale = {};
			model.examsetting.lite.map(item => {
				model.examsetting.questionTypes[item] = {
					number:0,
					score:0,
					describe:'',
					easyNumber:0,
					middleNumber:0,
					hardNumber:0,
				};
			});
			model.examscalemodel = 0;
			model.sort = sort;
			this.model = model;
		},
        clearSelectedQuestions:function(questype){
            if(this.model.examquestions[questype]??false)
            {
                this.model.examquestions[questype] = {
                    questions:[],
                    rowsquestions:[]
                };
            }
        },
		showSelectedQuestions:function(questype,isrows){
			if(isrows)
			{
				if(this.model.examquestions[questype]?.rowsquestions.length <= 0)
				{
					layer.msg('没有选择试题');
					return;
				}
			}
			else
			{
				if(this.model.examquestions[questype]?.questions.length <= 0)
				{
					layer.msg('没有选择试题');
					return;
				}
			}
			this.questionSearch.questype = questype;
			this.questionSearch.haschildren = isrows;
			if(this.model.examquestions)
			{
				this.selectedQuestionsKeys = this.model.examquestions[questype]?this.model.examquestions[questype].questions.map(Number):[];
				this.selectedRowsQuestionsKeys = this.model.examquestions[questype]?this.model.examquestions[questype].rowsquestions.map(Number):[];
			}
			this.searchQuestion(questype,true);
			this.showSelectedQuestionsPage = true;
		},
		selectQuestions:function(questiontype){
			this.questionSearch.questiontype = questiontype;
			if(this.model.examquestions)
			{
				this.selectedQuestionsKeys = this.model.examquestions[questiontype]?this.model.examquestions[questiontype].questions.map(Number):[];
				this.selectedRowsQuestionsKeys = this.model.examquestions[questiontype]?this.model.examquestions[questiontype].rowsquestions.map(Number):[];
			}			
			this.searchQuestion();
			this.getSections(this.model.examsubject);
			this.showSelectQuestionPage = true;
		},
		searchQuestion:async function(questiontype,withids){
			const id = layer.load(0);
            const level = {'1':'易','2':'中','3':'难'};
			this.questionSearch.subjectid = this.model.examsubject;
			if(this.questionSearch.haschildren === 1)
			{
				if(withids)this.questionSearch.ids = this.model.examquestions?.[questiontype].rowsquestions??[0];
				else this.questionSearch.ids = {};
				const data = await examApi.getQuestionList({
					search:this.questionSearch,
					limit:this.questionPage.limit,
					page:this.questionPage.current,
					haschildren:1,
				})
				this.questionPage.page = data.page;
				this.questionPage.total = data.total;
				this.questionPage.limit = data.limit;
				this.questionDataSource = data.data;
			}
			else
			{
				if(withids)this.questionSearch.ids = this.model.examquestions?.[questiontype].questions??[0];
				else this.questionSearch.ids = {};
				const data = await examApi.getQuestionList({
					search:this.questionSearch,
					limit:this.questionPage.limit,
					page:this.questionPage.current
				})
				this.questionPage.page = data.page;
				this.questionPage.total = data.total;
				this.questionPage.limit = data.limit;
				this.questionDataSource = data.data;
			}
			layer.close(id);
		},
		getSections:async function(subjectid){
			if(subjectid > 0)
			{
				this.sections = await examApi.getAllSections({
					subjectid:subjectid
				});
			}
			else this.sections = [];
		},
		changeSection:async function(sectionid){
			if(sectionid && sectionid.length > 0)
			{
				this.points = await examApi.getAllPoints({
					sectionid:sectionid
				});
			}
			else this.points = [];
		},
		showSetScore:function(row){
			this.base(async() => {
				this.totalscore = row.examtotalscore;
				const paperId = row.examid;
				const data = await examApi.getPaperQuestion(paperId);
				const examscore = data.examscore??{};
				const openkeys = [];
				Object.values(this.questionTypes).map(
						item => {
							if(data.questions?.[item.questid]?.questions || data.questions?.[item.questid]?.rowsquestions)
							{
								openkeys.push(item.questid);
							}
						}
				);
				this.openScoreKeys = openkeys;
				this.examscore = {
					examid:paperId,
					examscore:examscore
				};
				this.scores = data.questions??{};
				this.showScorePage = true;
			});
		},
		setScore:async function(){
			const id = layer.load(0);
			await examApi.modifyPaper(this.examscore);
			layer.close(id);
			await this.getData();
		},
        downloadPaper:async function(paperId){
	        const id = layer.load(0);
			try{
				const data = await examApi.downloadPaper(paperId);
				const a = document.createElement("a");
				a.download = "data.docx";
				// 创建二进制对象
				const blob = new Blob([data]);
				const downloadURL = (window.URL || window.webkitURL).createObjectURL(blob);
				a.href = downloadURL;
				a.click();
				URL.revokeObjectURL(downloadURL);
				layer.close(id);
			}catch(e){
				layer.close(id);
				layer.msg(e.message || '下载失败')
			}
		},
	},
	computed:{
		selectednumber:function(){
			const number = {};
			const examquestions = this.model.examquestions?this.model.examquestions:null;
			if(examquestions)
			{
				for(let x in this.questionTypes)
				{
					number[x] = {
						question:examquestions[x]?.questions?examquestions[x].questions.length:0,
						rowsquestion:examquestions[x]?.rowsquestions?examquestions[x].rowsquestions.length:0
					};
				}
			}
			else
			{
				for(let x in this.questionTypes)
				{
					number[x] = {question:0,rowsquestion:0};
				}
			}
			return number;
		}
	}
}
</script>