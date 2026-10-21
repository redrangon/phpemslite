<template>
	<div class="pagecontent">
		<!-- 新闻列表 -->
		<lay-card :bordered="false" class="news-list-card">
            <lay-tab v-model="tabCurrent1" type="brief">
                <lay-tab-item id="tc1_1">
                    <template #title>
                        <span class="tabtitle">
                            <span>考场列表</span>
                        </span>
                    </template>
                    <lay-card :bordered="false" class="news-list" v-if="page.total > 0">
                        <div v-for="exam in exams" :key="exam.csid" class="news-item" @click="viewExamDetail(exam.basicid)">
                            <div class="news-image">
                                <img :src="exam.basicthumb" :alt="exam.basic" />
                            </div>
                            <div class="news-content">
                                <h3 class="news-title">{{ exam.basic }}</h3>
                                <p class="news-desc" v-html="exam.basicdescribe?exam.basicdescribe:'暂无简介'"></p>
                                <div class="news-footer">
                                    <lay-button type="primary" size="md">
                                        开始学习
                                    </lay-button>
                                </div>
                            </div>
                        </div>
                        <div style="text-align: center;padding:10px;">
                            <router-link to="/desktop/home/exam/category/0">
                                <lay-button>更多考场 &gt;&gt;</lay-button>
                            </router-link>
                        </div>
                    </lay-card>
                    <lay-empty v-else description="暂无考场数据"></lay-empty>
                </lay-tab-item>
            </lay-tab>
		</lay-card>
	</div>
</template>

<script>
import { ref } from 'vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import examApi from "@/framework/api/exam.js";
export default {
    mixins: [baseMixin],
	data() {
		return {
            tabCurrent1:"tc1_1",
            layout:['count', 'prev', 'page', 'next'],
            page:{current:1,total:0,limit:10},
            exams: [],
		};
	},
	async mounted() {
		await this.getData();
	},
	methods: {
		// 加载新闻列表
        async getData() {
            await this.execute(async () => {
                const data = await examApi.getBasicList({
                    page:this.page.current,
                    limit:this.page.limit
                });
                this.page.current = data.page;
                this.page.limit = data.limit;
                this.page.total = data.total;
                this.exams = data.data;
            },null,null);
		},
        pageChange:async function({current,limit}){
            this.page.current = current
            this.page.limit = limit
            await this.getData()
        },
        viewExamDetail:function ( basicId){
            this.$router.push('/desktop/home/exam/basic/'+ basicId);
        }
	}
};
</script>
<style src="@/assets/css/desktop/category.css"></style>
<style scoped>

.news-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 10px;
}

.news-item {
    display: flex;
    gap: 20px;
    padding: 30px 20px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
}

.news-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateX(5px);
}

.news-image {
    flex-shrink: 0;
    width: 280px;
    height: 180px;
    border-radius: 6px;
    overflow: hidden;
}

.news-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.news-item:hover .news-image img {
    transform: scale(1.05);
}

.news-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.news-content .news-title {
    font-size: 20px;
    font-weight: bold;
    color: #333;
    margin-bottom: 12px;
    line-height: 1.4;
}

.news-desc {
    font-size: 14px;
    color: #666;
    line-height: 1.8;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>
