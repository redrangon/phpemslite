<template>
	<lay-card>
        <lay-field title="题帽/材料">
            <div style="font-size: 16px;line-height: 2;" v-html="question.question"></div>
        </lay-field>
    </lay-card>
    <lay-card>
		<lay-table id="questionid" ref="tableRef" v-model:selected-keys="selectedKeys" :columns="columns" :data-source="this.question.data" :default-toolbar="false" :page="page" even @change="getData">
			<template #toolbar>
				<lay-button size="sm" type="primary" @click="showAdd">添加试题</lay-button>
			</template>
			<template #footer>
				<lay-button :disabled="selectedKeys.length < 1" size="sm" type="primary" @click="delQuestion()">删除选中数据</lay-button>
			</template>
            <template v-slot:sequence="{ row }">
				<lay-input v-model="row.questionsequence" placeholder="排序" style="width:100%;" @blur="liteChildren(row.questionid,row.questionsequence)"></lay-input>
			</template>
			<template v-slot:questiontypename="{ row }">
				{{questionTypes[row.questiontype].questype}}
			</template>
			<template v-slot:operator="{ row }">
                <lay-button size="xs" type="primary" @click="showQuestion(row)">预览</lay-button>
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delQuestion(row.questionid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showQuestionPage" :area="['960px','90vh']" :shade="true" :shadeClose="false" shadeOpacity="0.6" title="预览试题">
		<div style="padding: 20px 50px 20px 20px;">
			<myQuestion :disabled="true" :question="modelShow" :questionType="questionTypes[modelShow.questiontype]" :userAnswer="modelShow.questionanswer" childIndex="0" index="1"></myQuestion>
		</div>
	</lay-layer>
	<lay-layer v-model="showAddPage" :area="['960px','90vh']" :btn="addPageBtns" :shade="true" :shadeClose="false" shadeOpacity="0.6" title="添加试题">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form ref="addPageFrom" :labelWidth="100" :model="model" :pane="false" class="form" size="md">
				<lay-form-item label="题型" prop="questiontype" required>
					<lay-select v-model="model.questiontype" placeholder="请选择">
						<lay-select-option v-for="(questype,questid) in questionTypes" :key="questid" :label="questype.questype" :value="questype['questid']"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="题干" prop="question" required>
					<myEditor v-model:content="model.question"></myEditor>
				</lay-form-item>
				<template v-if="questionTypes[model.questiontype]?.questsort === 0 && questionTypes[model.questiontype].questchoice  < 4">
					<lay-form-item label="备选项模式" prop="questionselecttype">
						<lay-radio v-model="model.questionselecttype" :value="0" label="纯文字选项" name="questionselecttype"></lay-radio>
						<lay-radio v-model="model.questionselecttype" :value="1" label="标签型选项" name="questionselecttype"></lay-radio>
						<span v-if="model.questionselecttype === 1" style="float: right;">编辑器 <lay-switch v-model="editorOn"></lay-switch></span>
					</lay-form-item>
					<template v-if="model.questionselecttype === 1">
						<lay-form-item v-if="editorOn" label="备选项" prop="questionselect" required>
							<myEditor v-model:content="model.questionselect"></myEditor>
						</lay-form-item>
						<lay-form-item v-else label="备选项" prop="questionselect" required>
							<lay-textarea v-model="model.questionselect" placeholder="每行一个，支持HTML标签" rows="8"></lay-textarea>
						</lay-form-item>
					</template>
					<template v-else>
						<lay-form-item label="备选项" prop="questionselect" required>
							<lay-textarea v-model="model.questionselect" placeholder="每行一个，不支持HTML标签" rows="8"></lay-textarea>
						</lay-form-item>
					</template>
				</template>
				<div v-if="questionTypes[model.questiontype]">
					<lay-form-item v-if="questionTypes[model.questiontype].questsort === 1" label="参考答案" prop="questionanswer" required>
						<myEditor v-model:content="model.questionanswer"></myEditor>
					</lay-form-item>
					<lay-form-item v-else-if="questionTypes[model.questiontype].questchoice === 4" label="参考答案" prop="questionanswer" required>
						<lay-radio-button v-model="model.questionanswer" label="正确" name="questionanswer" value="A"></lay-radio-button>
						<lay-radio-button v-model="model.questionanswer" label="错误" name="questionanswer" value="B"></lay-radio-button>
					</lay-form-item>
					<lay-form-item v-else-if="questionTypes[model.questiontype].questchoice === 5" label="参考答案" prop="questionanswer" required>
						<lay-input v-model="model.questionanswer" placeholder="请输入参考答案"></lay-input>
					</lay-form-item>
					<lay-form-item v-else-if="questionTypes[model.questiontype].questchoice === 2 || questionTypes[model.questiontype].questchoice === 3" label="参考答案" prop="questionanswer" required>
						<lay-checkbox-group v-model="model.questionanswer">
							<lay-checkbox v-for="(selector,slid) in selectors" :label="selector" :value="selector" name="questionanswer[]"></lay-checkbox>
						</lay-checkbox-group>
					</lay-form-item>
					<lay-form-item v-else label="参考答案" prop="questionanswer" required>
						<lay-radio-button v-for="(selector,slid) in selectors" v-model="model.questionanswer" :label="selector" :value="selector" name="questionanswer"></lay-radio-button>
					</lay-form-item>
				</div>
				<lay-form-item label="习题解析" prop="questiondescribe">
					<myEditor v-model:content="model.questiondescribe"></myEditor>
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
				<template  v-if="questionTypes[modelModify.questiontype]?.questsort === 0 && questionTypes[modelModify.questiontype].questchoice  < 4">
					<lay-form-item label="备选项模式" prop="questionselecttype">
						<lay-radio v-model="modelModify.questionselecttype" :value="0" label="纯文字选项" name="questionselecttype"></lay-radio>
						<lay-radio v-model="modelModify.questionselecttype" :value="1" label="标签型选项" name="questionselecttype"></lay-radio>
						<span v-if="modelModify.questionselecttype === 1" style="float: right;">编辑器 <lay-switch v-model="editorOn"></lay-switch></span>
					</lay-form-item>
					<template v-if="modelModify.questionselecttype === 1">
						<lay-form-item v-if="editorOn" label="备选项" prop="questionselect" required>
							<myEditor v-model:content="modelModify.questionselect"></myEditor>
						</lay-form-item>
						<lay-form-item v-else label="备选项" prop="questionselect" required>
							<lay-textarea v-model="modelModify.questionselect" placeholder="每行一个，支持HTML标签" rows="8"></lay-textarea>
						</lay-form-item>
					</template>
					<template v-else>
						<lay-form-item label="备选项" prop="questionselect" required>
							<lay-textarea v-model="modelModify.questionselect" placeholder="每行一个，不支持HTML标签" rows="8"></lay-textarea>
						</lay-form-item>
					</template>
				</template>
				<div v-if="questionTypes[modelModify.questiontype]">
					<lay-form-item v-if="questionTypes[modelModify.questiontype].questsort === 1" label="参考答案" prop="questionanswer" required>
						<myEditor v-model:content="modelModify.questionanswer"></myEditor>
					</lay-form-item>
					<lay-form-item v-else-if="questionTypes[modelModify.questiontype].questchoice === 4" label="参考答案" prop="questionanswer" required>
						<lay-radio-button v-model="modelModify.questionanswer" label="正确" name="questionanswer" value="A"></lay-radio-button>
						<lay-radio-button v-model="modelModify.questionanswer" label="错误" name="questionanswer" value="B"></lay-radio-button>
					</lay-form-item>
					<lay-form-item v-else-if="questionTypes[modelModify.questiontype].questchoice === 5" label="参考答案" prop="questionanswer" required>
						<lay-input v-model="modelModify.questionanswer" placeholder="请输入参考答案"></lay-input>
					</lay-form-item>
					<lay-form-item v-else-if="questionTypes[modelModify.questiontype].questchoice === 2 || questionTypes[modelModify.questiontype].questchoice === 3" label="参考答案" prop="questionanswer" required>
						<lay-checkbox-group v-model="modelModify.questionanswer">
							<lay-checkbox v-for="(selector,slid) in selectors" :label="selector" :value="selector" name="questionanswer[]"></lay-checkbox>
						</lay-checkbox-group>
					</lay-form-item>
					<lay-form-item v-else label="参考答案" prop="questionanswer" required>
						<lay-radio-button v-for="(selector,slid) in selectors" v-model="modelModify.questionanswer" :label="selector" :value="selector" name="questionanswer"></lay-radio-button>
					</lay-form-item>
				</div>
				<lay-form-item label="习题解析" prop="questiondescribe">
					<myEditor v-model:content="modelModify.questiondescribe"></myEditor>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import myEditor from '@/views/components/master/Editor.vue';
import myQuestion from '@/views/components/master/Question.vue';
import examApi from '@/framework/api/admin/exam.js';
import {layer} from '@layui/layui-vue';
import {withConfirm, withLayer} from "@/framework/utils/decorators.js";
export default {
	data() {
		return {
			columns:[{
				title: "选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			}, {
				title: '排序',
				customSlot: "sequence",
				key: "sequence",
				width: "80"
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
				customSlot: "questiontypename",
				width: '120px'
			}, {
				title: '操作',
				customSlot: "operator",
				key: "operator",
				width: "160px"
			}],
			editorOn:false,
			levels:{'1':'易','2':'中','3':'难'},
			page:{ current: 1, limit: 20, total: 0 },
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
            questionid:0,
            question:{},
			dataSource:[],
			selectedKeys:[],
			selectors:['A','B','C','D','E','F','G','H'],
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
	async created() {
        this.questionid = this.$route.params.questionid;
		await this.getQuestionTypes();
		await this.getData();
	},
	methods:{
		base:async function(fn){
			await withLayer(fn,	null,this.getData);
		},
		getQuestionTypes:async function(){
			this.questionTypes = await examApi.getAllQuestionTypes();
		},
		getData:async function(){
			this.question = await examApi.getQuestion(this.questionid);
		},
        showAdd:function(){
			this.model = {};
			this.showAddPage = true;
		},
		showModify:function(row){
			const question = {...row};
			if(this.questionTypes[question.questiontype] && this.questionTypes[question.questiontype].questsort === 0 && (this.questionTypes[question.questiontype].questchoice === 2 || this.questionTypes[question.questiontype].questchoice === 3))
			{
				if(question.questionanswer)question.questionanswer = question.questionanswer.split('');
				else question.questionanswer = [];
			}
			this.modelModify = question;
			this.showModifyPage = true;
		},
		showQuestion:function(row){
			this.showQuestionPage = true;
			this.modelShow = {...row};
		},
		delQuestion:function(id){
			withConfirm('删除后将不能找回，确定要操作吗？',async () => {
				await examApi.delChildren(id?[id]:this.selectedKeys);
			},this.getData)
		},
		addQuestion:function(){
			this.base(async() => {
				await examApi.addQuestion({
					...this.model,
					questionparent:this.questionid
				})
			});
		},
		modifyQuestion:function(){
			this.base(async() => {
				await examApi.modifyQuestion(this.modelModify)
			});
		},
        liteChildren:async function(id,lite){
	        this.base(async() => {
		        await examApi.modifyQuestion({
			        questionid:id,
			        questionsequence:lite
		        })
	        });
        }
	}
}
</script>