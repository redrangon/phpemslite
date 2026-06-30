<template>
	<lay-card class="pagecontent">
		<lay-card style="position: relative;">
			<lay-page-header :content="basic.basic" @back="$router.go(-1)" class="planbread"></lay-page-header>			
			<lay-tab v-model="tabCurrent" type="brief" :activeBarTransition="true">
				<lay-tab-item id="1">
					<template #title>
						<span class="tabtitle">考试记录</span>
					</template>
					<lay-container>
						<lay-panel shadow="never">
							<div class="sectionitem">
								<span style="color:#1E9FFF">您共进行了 {{ page.total }}</span> 次考试。
							</div> 
						</lay-panel>
						<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" :data-source="histories" id="ehid" even>
							<template #footer>
								<lay-row>
										<lay-page v-model="page.current" v-model:limit="page.limit" :total="page.total" :layout="layout" @change="changePage" style="float:right;"></lay-page>
								</lay-row>
							</template>
							<template v-slot:ehtime="{ row }">
								{{ timeFormat(row.ehtime) }}
							</template>
							<template v-slot:ehscore="{ row }">
								<template v-if="row.ehstatus === 1">{{ row.ehscore }}</template>
								<template v-else>
									待评分
								</template>
							</template>
						</lay-table>
					</lay-container>
				</lay-tab-item>
			</lay-tab>
		</lay-card>		
	</lay-card>	
</template>
<script>
import examApi from '@/framework/api/exam.js';
import {useAuthStore} from "@/stores/auth.js";
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	name:'examHistory',
	setup() {
		const authStore = useAuthStore();
		return {authStore};
	},
	mixins:[baseMixin],
	data() {
		return {
			columns:[{
				title: '试卷名称',
				key: 'ehexam'
			}, {
				title: '答题时间',
				key: 'ehstarttime',
				width: '160px'
			}, {
				title: '交卷时间',
				key: 'ehendtime',
				width: '160px'
			}, {
				title: '用时',
				key: 'ehtime',
				customSlot: 'ehtime',
				width: '90px'
			}, {
				title: '得分',
				key: 'ehscore',
				customSlot: 'ehscore',
				width: '90px'
			}],
			tabCurrent:"1",
			histories:[],
			page:{
				current:1,
				limit:10,
				total:0
			},
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			papers:{},
			basic:{},
			plan:{},
		}
	},
	async mounted(){
		this.basic = await examApi.getExamBasic();
		if(!this.basic.basicexam || this.basic.basicexam.model !== 2)this.$router.replace('/desktop/home/exam/exercise');
		else {
			await this.getData();
		}
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await examApi.getExamHistoryList({
					page:this.page.current,
					limit:this.page.limit,
				});
				this.histories = data.data;
                this.page.current = data.page;
                this.page.limit = data.limit;
                this.page.total = data.total;
			},null,null);
		},
		changePage:async function({current,limit}){
			this.page.current = current
			this.page.limit = limit
            await this.getData();
		},
		timeFormat:function(time){
			let format = 0;
			if(time >= 60){
				if(time % 60){
					format = parseInt(time / 60) +'分'+ time % 60 + '秒'
				}else{
					format = parseInt(time / 60) +'分'
				}
			}else{
				format = time + '秒'
			}
			return format;
		}
	}
}
</script>
<style scoped>
.desc{
	padding:15px;
	font-size: 16px;
	line-height: 2;
	margin-bottom: 20px;
}
</style>