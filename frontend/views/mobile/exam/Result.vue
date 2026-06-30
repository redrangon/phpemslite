<template>
	<div style="width:100%;">
        <!-- 导航栏 -->
        <van-nav-bar fixed left-arrow placeholder title="考试成绩" @click-left="$router.back()"/>
        <div v-if="history" class="card-container">
	        <div v-if="history.ehstatus === 1" style="margin-bottom: 10px;">
	            <van-cell-group class="menu-list">
	                <div style="text-align: center;padding: 30px 20px 10px 20px;">
		                <span class="score">{{history.ehscore}}</span>
	                </div>
	                <div style="text-align: center;margin-bottom: 20px;">
	                    <van-space v-if="history.ehispass === 1">
	                        <h3 class="title" style="color:#16baaa;">恭喜您通过考试！</h3>
	                    </van-space>
						<van-space v-else>
	                        <h3 class="title" style="color:#FF5722;">很遗憾，您没有通过考试！</h3>
	                    </van-space>
	                    <van-space :size="15" style="color:#999999;font-size: 14px;text-align: center;">
	                        <van-space>总分：{{ history.ehstats?.setting?.totalScore }}分</van-space>
	                        <van-space>合格分：{{ history.ehstats?.setting?.passMark }}分</van-space>
	                        <van-space>耗时：{{ timeFormat(history.ehtime) }}</van-space>
	                    </van-space>
	                </div>
	            </van-cell-group>
	            <van-cell-group class="menu-list">
	                <van-cell>
		                <template #title>
			                <span class="title">试卷分析</span>
		                </template>
		            </van-cell>
	                <div style="margin: 10px;">
		                <table class="table">
			                <thead>
				                <tr>
					                <th width="25%">题型</th>
					                <th width="20%">总题数</th>
					                <th width="20%">答对数</th>
					                <th width="20%">总分</th>
					                <th width="15%">得分</th>
				                </tr>
			                </thead>
			                <template v-for="(num,nid) in history.ehstats?.questionTypeAnalysis?.totalTypeNumber" :key="nid">
				                <tr v-if="num > 0">
					                <td>{{ questionTypes[nid]?.questype }}</td>
					                <td>{{ num }}</td>
					                <td>{{ history.ehstats?.questionTypeAnalysis?.rightTypeNumber[nid] }}</td>
					                <td>{{ history.ehstats?.questionTypeAnalysis?.totalTypeScore[nid]}}</td>
					                <td>{{ history.ehstats?.questionTypeAnalysis?.rightTypeScore[nid].toFixed(1) }}</td>
				                </tr>
			                </template>
		                </table>
		                <table class="table">
			                <thead>
				                <tr>
					                <th>知识点</th>
					                <th width="20%">总题数</th>
					                <th width="20%">答对数</th>
					                <th width="20%">正确率</th>
				                </tr>
			                </thead>
			                <template v-for="(stat,sid) in history.ehstats?.pointAnalysis?.totalPointNumber" :key="sid">
				                <tr v-if="stat > 0">
					                <td>{{ history.points[sid] }}</td>
					                <td>{{ stat }}</td>
					                <td>{{ history.ehstats?.pointAnalysis?.rightPointNumber[sid]??0 }}</td>
					                <td>{{ (100 * (history.ehstats?.pointAnalysis?.rightPointNumber[sid]??0) / stat).toFixed(1) }}%</td>
				                </tr>
			                </template>
		                </table>
	                </div>
	                <div style="margin: 16px;text-align: center;padding:10px;">
	                    <van-space v-if="basic.basicexam.model === 2">
	                        <van-button block to="/mobile/exam/examhistory" type="primary">
	                            考试记录
	                        </van-button>
	                    </van-space>
	                    <van-space v-else>
	                        <van-button type="primary" @click="toRedo()">
	                            重做试卷
	                        </van-button>
	                        <van-button :to="`/mobile/exam/view/${history.ehid}`" type="primary">
	                            查看详细
	                        </van-button>
	                        <van-button to="/mobile/exam/history" type="primary">
	                            考试记录
	                        </van-button>
	                    </van-space>
	                </div>
	            </van-cell-group>
	        </div>
	        <van-cell-group v-else>
	            <div style="text-align: center;padding-top:25px;">
	                <h3 class="title">请等待教师评卷完成</h3>
	            </div>
	            <div style="margin: 16px;">
					<van-button block type="primary" @click="$router.back()">
	                    返回
					</van-button>
				</div>
	        </van-cell-group>
	        <div style="margin-top: 40px;"></div>
        </div>
    </div>
</template>

<script>
	import examApi from "@/framework/api/exam.js";
	import {useAuthStore} from "@/stores/auth.js";
	import baseMixin from "@/framework/mixins/baseMixin.js";
    import {ref} from 'vue';
    export default {
		mixins: [baseMixin],
        data() {
            return {
	            tabCurrent:"1",
	            ehid:0,
	            history:[],
	            setting:{},
	            number:{},
	            questionTypes:{},
	            stats:{},
	            right:{},
	            score:{},
	            basic:{}
            };
        },
	    setup() {
		    const authStore = useAuthStore();
		    return {authStore};
	    },
	    async mounted(){
		    this.ehid = this.$route.params.ehid;
		    this.basic = this.authStore.basic;
            await this.getQuestionTypes();
		    await this.getData();
	    },
        methods:{
	        getData:async function(){
		        await this.execute( async () => {
			        this.history = await examApi.getHistoryStats(this.ehid);
		        },null,null)
	        },
	        getQuestionTypes:async function(){
		        this.questionTypes = await examApi.getQuestionTypes();
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
	        },
	        toRedo:async function(){
		        await this.execute(async () => {
			        const data = await examApi.getReTestExamSession(this.ehid);
			        this.$router.push({path:'/mobile/exam/paper/'+data.sessionid});
		        },null,null);
	        },
        }
    };
</script>

<style scoped>
.score{
    font-size: 32px;
    font-weight: bold;
    color: #16baaa;
    line-height: 80px;
}
.title{
    font-size: 16px;
    line-height: 2;
    font-weight: bold;	
}
.table {
	border-collapse:collapse;
	border:1px solid #aaa;
	text-align: center;
	margin-bottom:20px;
    font-size: 14px;
    width: 100%;
    color:#000000;
}
.table thead{
	background-color: #fafafa;
	font-weight: bold;
}
.table th {
	border:1px solid #ddd;
	padding:10px;
}
.table td {
	padding:10px;
	border:1px solid #ddd;
}
.table .left{
	text-align: left;
}
.menu-list{
	padding:10px;
}
</style>