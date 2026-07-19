<template>
	<lay-card class="pagecontent">
		<lay-card style="position: relative;">
			<lay-page-header :content="basic.basic" @back="$router.go(-1)" class="planbread"></lay-page-header>			
			<lay-tab v-model="tabCurrent" type="brief" @change="getData">
				<lay-tab-item id="1">
					<template #title>
						<span class="tabtitle">试题列表</span>
					</template>
					<lay-container>
						<lay-space direction="vertical">
							<lay-space>
								<lay-space></lay-space>
								<lay-space>
									<span style='width:60px'> 类型：</span>
									<lay-select v-model="search.questionisparent" placeholder="不限" style="width:100px;" allow-clear>
										<lay-select-option :value="1" label="普通题"></lay-select-option>
										<lay-select-option :value="2" label="题帽题"></lay-select-option>
									</lay-select>
								</lay-space>
								<lay-space>
									<span style='width:70px'> 关键字：</span><lay-input v-model="search.keyword"></lay-input>
								</lay-space>
								<lay-space>
									<span style='width:70px'> 题型：</span>
									<lay-select v-model="search.questiontype" placeholder="请选择" allow-clear style="min-width: 180px;width:100%">
										<lay-select-option :value="questype.questid" :label="questype.questype" v-for="(questype,qid) in questionTypes" :key="qid"></lay-select-option>
									</lay-select>
								</lay-space>
								<lay-space>
									<span style='width:70px'> 难度：</span>
									<lay-select v-model="search.questionlevel" placeholder="不限" style="width:80px;" allow-clear>
										<lay-select-option :value="lid" :label="level" v-for="(level,lid) in levels" :key="lid"></lay-select-option>
									</lay-select>
								</lay-space>
							</lay-space>
							<lay-space>
								<lay-space></lay-space>
								<lay-space>
									<span style='width:60px'> 章节：</span>
									<lay-select v-model="search.sections" placeholder="请选择" @change="changeSection(search.sections)" multiple allow-clear style="width: 260px;" minCollapsedNum="2">
										<lay-select-option :value="section.sectionid" :label="section.section" v-for="(section,seid) in sections" :key="seid"></lay-select-option>
									</lay-select>
								</lay-space>
								<lay-space>
									<span style='width:70px'> 知识点：</span>
									<lay-select v-model="search.points" placeholder="请选择" multiple allow-clear style="width: 380px;" minCollapsedNum="2">
										<lay-select-option :value="point.pointid" :label="point.point" v-for="(point,pid) in points" :key="pid"></lay-select-option>
									</lay-select>
								</lay-space>
								<lay-space>
									<lay-button type="normal" @click="getData">搜索</lay-button>						
								</lay-space>
							</lay-space>
						</lay-space>
						<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" :data-source="dataSource" id="questionid" even class="dataTable">
							<template v-slot:question="{ row }">
								{{safeStripHtml(row.question)??'纯图片题目'}}
							</template>
							<template v-slot:questionisparent="{ row }">
								{{row.questionisparent === 1?'题帽题':'普通题'}}
							</template>
							<template v-slot:questiontypename="{ row }">
								{{questionTypes?.[row.questiontype]?.questype}}
							</template>
							<template v-slot:questionlevelname="{ row }">
								{{levels?.[row.questionlevel]}}
							</template>
							<template v-slot:operator="{ row }">
								<lay-button size="xs" type="normal" @click="showQuestion(row)">查看</lay-button>
							</template>
						</lay-table>
						<lay-page v-model="page.current" theme="blue" :limit="page.limit" :total="page.total" style="float:right;" @change="changePage"></lay-page>
					</lay-container>
				</lay-tab-item>				
			</lay-tab>
		</lay-card>
		<lay-layer v-model="showPage" :shade="true" :shadeClose="false" shadeOpacity="0.6" :area="['960px','90vh']" title="预览试题">
			<div style="padding: 20px 50px 20px 20px;">
				<template v-if="question.questionisparent === 1">
					<div v-html="question.question" class="rowsQuestion"></div>
					<template v-for="(children,key) in question.data" :key="key">
						<lay-line>第{{key + 1}}题</lay-line>
						<myQuestion :question="children" index="1" :questionType="questionTypes[children.questiontype]" childIndex="1" :disabled="true" :userAnswer="children.questionanswer"></myQuestion>
					</template>
				</template>
				<template v-else>
					<myQuestion :question="question" index="1" :questionType="questionTypes[question.questiontype]" childIndex="0" :disabled="true" :userAnswer="question.questionanswer"></myQuestion>
				</template>
			</div>
		</lay-layer>
	</lay-card>
</template>
<script>
import examApi from '@/framework/api/exam.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import {useAuthStore} from "@/stores/auth.js";
import baseMixin from "@/framework/mixins/baseMixin.js";
import myQuestion from "@/components/desktop/Question.vue";
export default {
	components: {myQuestion},
	data() {
		return {
			tabCurrent:"1",
			basic:{},
			page:{
				current:1,
				limit:20,
				total:1
			},
			columns:[{
				title:'类型',
				customSlot:'questionisparent',
				width:'80px'
			},{
				title:'试题',
				customSlot: 'question',
				key:'question'
			},{
				title:'题型',
				customSlot:'questiontypename',
				width:'100px'
			},{
				title:'难度',
				customSlot:'questionlevelname',
				width:'50px'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"50"
			}],
			dataSource:[],
			sections:[],
			points:[],
			structPoints:{},
			search:{},
			selectors:['A','B','C','D','E','F','G','H'],
			levels:{
				"1":"易",
				"2":"中",
				"3":"难",
			},
			questionTypes:{},
			question:{},
			showPage:false,
			showPageBtn:[
				{
					text: "关闭",
					callback: () => {
						this.showPage = false;
					}
				}
			],
		}
	},
	setup() {
		const authStore = useAuthStore();
		return {authStore};
	},
	mixins:[baseMixin],
	async mounted(){
		this.basic = this.authStore.basic;
		await Promise.all([
			this.getQuestionTypes(),
			this.getSections(),
			this.getData()
		]);
	},
	methods:{
		safeStripHtml(html) {
			if (!html) return '';
			// 使用 DOMParser 将字符串解析为 HTML 文档
			const doc = new DOMParser().parseFromString(html, 'text/html');
			// 提取 body 中的纯文本内容
			return doc.body.textContent || '';
		},
		getQuestionTypes:async function(){
			this.questionTypes = await examApi.getQuestionTypes();
		},
		getData:async function(){
            const level = {'1':'易','2':'中','3':'难'};
			await this.execute( async () => {
				const data = await examApi.getQuestionList({
					limit:this.page.limit,
					page:this.page.current,
					search:this.search
				});
				this.page.current = data.page;
				this.page.total = data.total;
				this.page.limit = data.limit;
				this.dataSource = data.data;
			},null,null);
		},		
		getSections:async function(){
			const data = await examApi.getSectionPoint();
			this.sections = data.sections;
			this.structPoints = data.points;
		},
		changeSection:async function(sections){
			if(sections && sections.length > 0)
			{
				let points = [];
				sections.map((item) => {
					points = points.concat(this.structPoints[item]??[]);
				})
				this.points = points;
			}
			else this.points = [];
		},
		changePage:function({current,limit}){
			this.page.current = current;
			this.page.limit = limit;
			this.getData();
		},
		showQuestion:function(row){
			this.execute(async () => {
				this.question = await examApi.getQuestionDetail(row.questionid);
				this.showPage = true;
			},null,null);
		}
	}
}
</script>
<style scoped>
.dataTable{
	margin-top:10px;
	margin-bottom: 20px;	
}
::v-deep(.layui-table td,.layui-table th){
	line-height: 2!important;
}
.rowsQuestion{
	margin-top: 10px;
	padding:10px;
	display: block;
	line-height:3;
	font-size: 16px;
}
.rowsQuestion p{
	text-indent: 2em;
}
</style>