<template>
	<div style="width:100%;">
        <!-- 导航栏 -->
        <van-nav-bar title="考试记录" left-arrow @click-left="$router.go(-1)" placeholder fixed/>
		<div class="card-container">
			<van-list :loading="loading" :finished="finished" finished-text="没有更多了" @load="onLoad">
				<van-cell-group class="menu-list">
					<van-cell is-link v-for="(history, index) in histories" :key="index" :to="`/mobile/exam/result/${history.ehid}`">
						<template #title>
							<div style="font-size: 16px;">{{history.ehexam}}</div>
						</template>
						<template #label>
							<div style="font-size: 14px;">考试时间: {{history.ehstarttime}}</div>
						</template>
						<template #right-icon>
							<span class="low-score" v-if="history.ehstatus < 1">待评分</span>
							<span :class="{ 'high-score': history.ehispass === 1, 'low-score': history.ehispass < 1 }" v-else>
                                {{ history.ehscore }}
                            </span>
						</template>
					</van-cell>
				</van-cell-group>
			</van-list>
		</div>
    </div>
</template>

<script>
import examApi from '@/framework/api/exam.js';
import {useAuthStore} from "@/stores/auth.js";
import baseMixin from "@/framework/mixins/baseMixin.js";

export default {
	setup() {
		const authStore = useAuthStore();
		return {authStore};
	},
	mixins:[baseMixin],
    data() {
        return {
            histories:[],
            loading: false,
            finished: false,
            basic:{},
            page:{
				current:1,
	            total:1,
	            limit:20
            },
        };
    },
    async mounted() {
		await this.getData()        
    },
    methods: {
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
        onLoad:async function() {
            // 加载更多数据
            const data = await examApi.getExamHistoryList({
                page:this.page.current + 1,
                limit:this.page.limit
            });
	        this.page.current = data.page;
	        this.page.limit = data.limit;
	        this.page.total = data.total;
            if(data.data.length >= 1)  {
	            data.data.map((item) => {
		            this.histories.push(item);
	            });
            }            
            if(this.histories.length >= this.page.total){
                this.finished = true;
            }
        }
    }
};
</script>
<style scoped>
.exam-history-page {
    padding: 15px;
}

.high-score,.low-score{
	display: flex;
	align-items: center;
	justify-content: center;
}

.high-score {
    color: green;
    font-size: 1.5em;
}

.low-score {
    color: red;
    font-size: 1.5em;
}
.menu-list div{
	padding:10px 20px;
	font-size: 16px;
	background: transparent;
}
</style>
