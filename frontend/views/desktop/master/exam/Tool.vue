<template>
	<lay-card>
		<template v-slot:title>
			<span style="font-size: 18px;">批量工具</span>
		</template>
		<lay-field title="清除考试会话">
			<lay-space size="lg" style="padding:30px;">
				<span style='width:70px'> 清除范围：</span>
				<lay-date-picker v-model="examsession" range :placeholder="['开始日期','结束日期']" :allow-clear="true"></lay-date-picker>
				<lay-button type="primary" @click="submitExamSession(1)">清除</lay-button>
				<lay-button type="danger" @click="submitExamSession(2)">清除全部</lay-button>
			</lay-space>
		</lay-field>
		<lay-field title="清除考试记录">
			<lay-space size="lg" style="padding:30px;">
				<span style='width:70px'> 清除范围：</span>
				<lay-date-picker v-model="examhistory" range :placeholder="['开始日期','结束日期']" :allow-clear="true"></lay-date-picker>
				<lay-button type="primary" @click="submitExamHistory(1)">清除</lay-button>
				<lay-button type="danger" @click="submitExamHistory(2)">清除全部</lay-button>
			</lay-space>
		</lay-field>
		<lay-field title="清理在线用户">
			<lay-space size="lg" style="padding:30px;">
				<span style='width:70px'> 清理范围：</span>
				<lay-date-picker v-model="usersession" range :placeholder="['开始日期','结束日期']" :allow-clear="true"></lay-date-picker>
				<lay-button type="primary" @click="submitUserSession(1)">清除</lay-button>
				<lay-button type="danger" @click="submitUserSession(2)">清除全部</lay-button>
			</lay-space>
		</lay-field>
	</lay-card>
</template>
<style scoped></style>
<script>
import examApi from '@/framework/api/admin/exam.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
	data() {
		return {
			examsession:{},
			examhistory:{},
			usersession:{}
		}
	},
	methods:{
		submitExamSession:function(type){
			const time = type === 1?this.examsession:0;
			examApi.clearExamSession(time,this.getData);
		},
		submitExamHistory:function(type){
			const time = type === 1?this.examhistory:0;
			examApi.clearExamHistory(time,this.getData);
		},
		submitUserSession:function(type){
			const time = type === 1?this.usersession:0;
			examApi.clearUserSession(time,this.getData);
		}
	}
}
</script>