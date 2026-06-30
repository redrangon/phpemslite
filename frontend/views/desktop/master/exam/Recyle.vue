<template>
	<lay-card>
		<lay-space direction="vertical">
            <lay-space size="lg">
                <lay-space></lay-space>
                <lay-space>
                    <span style='width:70px'> 类型</span>
                    <lay-select v-model="search.isrows" placeholder="请选择" @change="getData">
                        <lay-select-option :value="0" label="普通试题"></lay-select-option>
                        <lay-select-option :value="1" label="题冒题"></lay-select-option>
                    </lay-select>
                </lay-space>
                
                <lay-space>
					<span style='width:70px'> 科目：</span>
					<lay-select v-model="search.subjectid" placeholder="请选择" @change="changeSubject(search.subjectid)" allow-clear>
						<lay-select-option :value="subject.subjectid" :label="subject.subject" v-for="subject,sid in subjects" :key="sid"></lay-select-option>
					</lay-select>
				</lay-space>
                <lay-space>
                    <span style='width:70px'> 章节：</span>
                    <lay-select v-model="search.sectionid" placeholder="请选择" @change="changeSection(search.sectionid)" multiple allow-clear style="width:100%;min-width: 180px;">
                        <lay-select-option :value="section.sectionid" :label="section.section" v-for="section,seid in sections" :key="seid"></lay-select-option>
                    </lay-select>
                </lay-space>
				<lay-space>
                    <span style='width:70px'> 知识点：</span>
                    <lay-select v-model="search.knowsids" placeholder="请选择" multiple allow-clear style="min-width: 180px;width:100%">
                        <lay-select-option :value="knows.knowsid" :label="knows.knows" v-for="knows,kid in knowses" :key="kid"></lay-select-option>
                    </lay-select>
                </lay-space>                
            </lay-space>
            <lay-space size="lg">
                <lay-space></lay-space>
                <lay-space>
                    <span style='width:70px'> 关键字：</span><lay-input v-model="search.keyword"></lay-input>
                </lay-space>
                <lay-space>
                    <lay-button type="primary" @click="getData">搜索</lay-button>
                    <lay-button type="danger" @click="clearRecyleAlert">清空回收站</lay-button>
                </lay-space>
            </lay-space>            
        </lay-space>
	</lay-card>
	<lay-card>
		<lay-space>
            <lay-table ref="searchTableRef" v-if="search.isrows == 1" :page="page" @change="getData" :columns="rowsquestioncolumns" :data-source="questionDataSource" id="qrid" v-model:selected-keys="selectedRowsQuestionsKeys" even>
                <template #footer>
                    <lay-button type="primary" size="sm" @click="restoreRowsQuestion()" :disabled="selectedRowsQuestionsKeys.length < 1">恢复</lay-button>
                    <lay-button type="primary" size="sm" @click="delRowsQuestion()" :disabled="selectedRowsQuestionsKeys.length < 1">彻底删除</lay-button>
                </template>
                <template v-slot:operator="{ row }">
                    <lay-button size="xs" type="primary" @click="restoreRowsQuestion(row.qrid)">恢复</lay-button>
                    <lay-button size="xs" type="danger" @click="delRowsQuestion(row.qrid)">删除</lay-button>
                </template>
            </lay-table>
            <lay-table ref="searchTableRef" v-else :page="page" :columns="questioncolumns" @change="getData" :data-source="questionDataSource" id="questionid" v-model:selected-keys="selectedQuestionsKeys" even>
                <template #footer>
                    <lay-button type="primary" size="sm" @click="restoreQuestion()" :disabled="selectedQuestionsKeys.length < 1">恢复</lay-button>
                    <lay-button type="primary" size="sm" @click="delQuestion()" :disabled="selectedQuestionsKeys.length < 1">彻底删除</lay-button>
                </template>
                <template v-slot:operator="{ row }">
                    <lay-button size="xs" type="primary" @click="restoreQuestion(row.questionid)">恢复</lay-button>
                    <lay-button size="xs" type="danger" @click="delQuestion(row.exaquestionidmid)">删除</lay-button>
                </template>
            </lay-table>
        </lay-space>
	</lay-card>
</template>
<style scoped></style>
<script>
import exam from '@/framework/api/exam.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
	data() {
		return {
			questioncolumns:exam.columns.question,			
			dataSource:[],
			questionDataSource:[],
			rowsquestioncolumns:exam.columns.rowsQuestion,
			rowsQuestionDataSource:[],
			search:ref({}),
			selectedQuestionsKeys:ref(),
            selectedRowsQuestionsKeys:ref(),
			page:{ current: 1, limit: 20, total: 0 },
            knowses:ref(),
            sections:ref(),
            subjects:ref()
		}
	},
	emits: ['setVal'],
	async created() {
		this.$emit('setVal',{bcmus:[{
			title:'首页',
			path:'/'
		},{
			title:'考试',
			path:'/exam'
		},{
			title:'回收站',
			path:'/exam/recyle'
		}]});
        this.search.isrows = 0;
        await this.getQuestypes();
		await this.getSubjects();
		await this.getData();
	},
	methods:{
		getQuestypes:async function(){
			const questypes = await exam.getQuestypes();
			this.questypes = questypes;
		},
		getSubjects:async function(){
			const subjects = await exam.getAllSubjects();
			this.subjects = subjects;
		},
		clearRecyleAlert:function(){
			layer.confirm("回收站清空后将不能恢复，您确定要清空吗？", {
				title:'操作确认',
				btn: [
					{
						text:'确定', 
						callback: (layerid) => { 
							layer.close(layerid); 
							this.clearRecyle();				
						}							 
					},
					{
						text:'取消', 
						callback: (layerid) => { 
							layer.close(layerid); 
						}
					}
				]
			});					
		},
		clearRecyle:async function(){
			const id = layer.load(0);
			await exam.clearRecyle();
			this.getData();
			layer.close(id);
		},
		getData:async function(){
			const id = layer.load(0);
            const level = {'1':'易','2':'中','3':'难'};
			if(this.search.isrows == 1)
			{
				const data = await exam.getRowsQuestionList({
					search:this.search,
					limit:this.page.limit,
					page:this.page.current,
					isrecyle:1
				});
				this.page = data.page;
				let tmp = data.data;
				for(let x in tmp)
				{
					tmp[x]['qrlevelname'] = level[tmp[x]['qrlevel']]?level[tmp[x]['qrlevel']]:'';
				}
				this.questionDataSource = tmp;
			}
			else
			{
				const data = await exam.getQuestionList({
					search:this.search,
					limit:this.page.limit,
					page:this.page.current,
					isrecyle:1
				});
				this.page = data.page;
				let tmp = data.data;
				for(let x in tmp)
				{
					tmp[x]['questionlevelname'] = level[tmp[x]['questionlevel']]?level[tmp[x]['questionlevel']]:'';
				}
				this.questionDataSource = tmp;
			}
			layer.close(id);
		},
        changeSubject:async function(subjectid){
			if(subjectid > 0)
			{
				this.sections = await exam.getAllSections({
					subjectid:subjectid
				});	
			}
			else this.sections = [];
		},
		changeSection:async function(sectionid){
			if(sectionid && sectionid.length > 0)
			{
				this.knowses = await exam.getAllKnows({
					sectionid:sectionid
				});	
			}
			else this.knowses = [];
		},
		delQuestion:function(id)
		{
			let ids = this.selectedQuestionsKeys;
			if(id){
				ids = [id];
			}
			exam.finaldelQuestion(ids,'question',this.getData)	
		},
		delRowsQuestion:function(id){
			let ids = this.selectedRowsQuestionsKeys;
			if(id){
				ids = [id];
			}
			exam.finaldelQuestion(ids,'rowsquestion',this.getData)			
		}
	}
}
</script>