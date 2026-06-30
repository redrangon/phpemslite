<template>
	<lay-card>
		<template v-slot:title>
			<span style="font-size: 18px;">{{model.basic}}</span>
		</template>
		<lay-form :model="model" :pane="false" size="md" labelWidth="180" class="form" ref="addPageFrom">
			<lay-field title="考试范围设定">
				<lay-form-item :label="section.section" prop="basicknows[section.sectionid]" v-for="section,sid in sections">
					<lay-select v-model="model.basicknows[section.sectionid]" multiple allow-clear placeholder="请选择" style="width:90%">
						<lay-select-option :value="knows.knowsid" :label="knows.knows" v-for="knows,kid in knowses[section.sectionid]" :key="kid"></lay-select-option>
					</lay-select>
				</lay-form-item>
			</lay-field>
			<lay-field title="考场配置">
				<lay-form-item label="配比设定" prop="basicexam.rulemodel" required>
					<lay-radio v-model="model.basicexam.rulemodel" name="rulemodel" :value="0" label="考试范围约束题型配比"></lay-radio>
					<lay-radio v-model="model.basicexam.rulemodel" name="rulemodel" :value="1" label="考试范围不约束题型配比"></lay-radio>
				</lay-form-item>
				<lay-form-item label="考场模式" prop="basicexam.model" required>
					<lay-radio v-model="model.basicexam.model" name="model" :value="0" label="全功能模式【显示试题库】"></lay-radio>
					<lay-radio v-model="model.basicexam.model" name="model" :value="1" label="练习模式【不显示试题库】"></lay-radio>
					<lay-radio v-model="model.basicexam.model" name="model" :value="2" label="考试模式"></lay-radio>
				</lay-form-item>
				<lay-form-item label="试题排序" prop="basicexam.changesequence" required>
					<lay-radio v-model="model.basicexam.changesequence" name="changesequence" :value="0" label="正式考试不打乱试题"></lay-radio>
					<lay-radio v-model="model.basicexam.changesequence" name="changesequence" :value="1" label="正式考试打乱试题"></lay-radio>
				</lay-form-item>
				<lay-form-item label="模拟考试试卷" prop="basicexam.auto" required>
					<lay-select v-model="model.basicexam.auto" multiple allow-clear placeholder="请选择" style="width:90%">
						<lay-select-option :value="paper.examid" :label="paper.exam" v-for="paper,pid in papers" :key="pid"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="模拟考试试卷模板" prop="basicexam.autotemplate" required>
					<lay-select v-model="model.basicexam.autotemplate" placeholder="请选择">
						<lay-select-option value="0" label="整页显示"></lay-select-option>
						<lay-select-option value="1" label="一页一题"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="正式考试试卷" prop="basicexam.self" required>
					<lay-select v-model="model.basicexam.self" multiple allow-clear placeholder="请选择" style="width:90%">
						<lay-select-option :value="paper.examid" :label="paper.exam" v-for="paper,pid in papers" :key="pid"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="正式考试试卷模板" prop="basicexam.selftemplate" required>
					<lay-select v-model="model.basicexam.selftemplate" placeholder="请选择">
						<lay-select-option value="0" label="整页显示"></lay-select-option>
						<lay-select-option value="1" label="一页一题"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="正式考试开始时间" prop="basicexam.opentime">
					<lay-space><lay-date-picker v-model="model.basicexam.opentime" type="datetime"></lay-date-picker></lay-space>
					<lay-space>不填写为不限制开始时间</lay-space>
				</lay-form-item>
				<lay-form-item label="正式考试结束时间" prop="basicexam.closetime">
					<lay-space><lay-date-picker v-model="model.basicexam.closetime" type="datetime"></lay-date-picker></lay-space>
					<lay-space>不填写为不限制结束时间</lay-space>
				</lay-form-item>
				<lay-form-item label="正式考试抽卷规则" prop="basicexam.selectrule" required>
					<lay-radio v-model="model.basicexam.selectrule" name="selectrule" :value="0" label="系统随机抽卷"></lay-radio>
					<lay-radio v-model="model.basicexam.selectrule" name="selectrule" :value="1" label="用户手动选卷"></lay-radio>
				</lay-form-item>
				<lay-form-item label="未答完题禁止交卷" prop="basicexam.fullsubmit" required>
					<lay-radio v-model="model.basicexam.fullsubmit" name="fullsubmit" :value="0" label="关闭"></lay-radio>
					<lay-radio v-model="model.basicexam.fullsubmit" name="fullsubmit" :value="1" label="开启"></lay-radio>
				</lay-form-item>
				<lay-form-item label="开考限制" prop="basicexam.unjointime" required>
					<lay-space><lay-input v-model="model.basicexam.unjointime" placeholder=""></lay-input></lay-space>
					<lay-space>（填写分钟数，开考经过设定分钟时间后禁止抽卷，不限请填写0）</lay-space>
				</lay-form-item>
				<lay-form-item label="限考次数" prop="basicexam.examnumber" required>
					<lay-space><lay-input v-model="model.basicexam.examnumber" placeholder=""></lay-input></lay-space>
					<lay-space>（正式考试最多考试次数，不限请填写0）</lay-space>
				</lay-form-item>
				<lay-form-item label="切屏次数" prop="basicexam.screennumber" required>
					<lay-space><lay-input v-model="model.basicexam.screennumber" placeholder=""></lay-input></lay-space>
					<lay-space>（考试时允许的切屏次数，不限请填写0）</lay-space>
				</lay-form-item>
				<lay-form-item label="考场经纬度" prop="basicexam.examarea" required>
					<lay-space><lay-input v-model="model.basicexam.examarea" placeholder=""></lay-input></lay-space>
					<lay-space>（考场经纬度，不限请填写0，经度、纬度使用用英文逗号隔开，如：“113.923453,35.281846”）</lay-space>
				</lay-form-item>
				<lay-form-item label="经纬度容差" prop="basicexam.areatolerance" required>
					<lay-space><lay-input v-model="model.basicexam.areatolerance" placeholder=""></lay-input></lay-space>
					<lay-space>（考试经纬度容差范围，不限请填写0）</lay-space>
				</lay-form-item>
				<lay-form-item label="交卷后" prop="basicexam.notviewscore" required>
					<lay-radio v-model="model.basicexam.notviewscore" name="notviewscore" :value="0" label="直接显示分数"></lay-radio>
					<lay-radio v-model="model.basicexam.notviewscore" name="notviewscore" :value="1" label="暂不显示分数"></lay-radio>
				</lay-form-item>
			</lay-field>
			<lay-form-item label="">
				<lay-button type="primary" @click="submitConfig">提交</lay-button>
				<lay-button type="default" @click="">重置</lay-button>
			</lay-form-item>
		</lay-form>
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
			basic:ref({}),
			model:ref({basicexam:ref({})}),
			subject:ref(),
			papers:ref(),
			basicid:ref(),
			sections:ref({}),
			knowses:ref({})
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
			title:'考场管理',
			path:'/exam/basic'
		},{
			title:'考场配置'
		}]});
		this.basicid = this.$route.params.basicid;
		await this.getBasic();
		await this.getSections();
		await this.getknowes();
		await this.getPapers();
	},
	methods:{
		getBasic:async function(){
			const id = layer.load(0);
			const data = await exam.getBasic(this.basicid);
			this.model = data;
			layer.close(id);
		},
		getPapers:async function(){
			const id = layer.load(0);
			const data = await exam.getAllPapers({
				search:{
					subjectid:this.model.basicsubjectid
				}
			});
			this.papers = data;
			layer.close(id);
		},
		getSections:async function(){
			if(this.model.basicsubjectid > 0)
			{
				this.sections = await exam.getAllSections({
					subjectid:this.model.basicsubjectid
				});
			}
			else this.sections = [];
		},
		getknowes:async function(){
			if(this.model.basicsubjectid > 0)
			{
				const res = await exam.getAllKnows({
					subjectid:this.model.basicsubjectid
				});
				let knowses = {};
				for(let x in res)
				{
					if(!knowses[res[x].sectionid])knowses[res[x].sectionid] = [];
					knowses[res[x].sectionid].push(res[x]);
				}
				this.knowses = knowses;
			}
			else this.knowses = {};
		},
		submitConfig:async function(){
			await exam.modifyBasic({
				basic:{
					basicid:this.model.basicid,
					basicknows:this.model.basicknows,
					basicexam:this.model.basicexam
				}
			});
			this.getBasic();
		}
	}
}
</script>