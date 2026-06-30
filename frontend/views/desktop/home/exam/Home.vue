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
                    <lay-card :bordered="false" class="grid-card" v-if="page.total > 0">
                        <lay-row :space="15">
                            <lay-col v-for="exam in exams" :key="exam.basicid" :xs="24" :sm="12" :md="8" :lg="6">
                                <lay-card :bordered="false" class="grid-item" @click="viewExamDetail(exam.basicid)">
                                    <div class="grid-thumb">
                                        <img :src="exam.basicthumb" :alt="exam.basic" />
                                    </div>
                                    <h4 class="grid-title">{{ exam.basic }}</h4>
                                    <p class="grid-summary">{{ exam.basicdescribe?exam.basicdescribe:'暂无简介' }}</p>
                                    <div class="grid-meta">
                                        <lay-icon type="layui-icon-read" style="margin-left: 8px;"></lay-icon>
                                        <span>{{ exam.basicnumber??0 }}</span>
                                    </div>
                                </lay-card>
                            </lay-col>
                        </lay-row>
                    </lay-card>
                    <lay-empty v-else description="暂无考场数据"></lay-empty>
                    <div class="pagination-wrapper" v-if="page.total > 0">
                        <lay-page v-model="page.current"  :layout="layout" v-model:limit="page.limit" :total="page.total" @change="pageChange" theme="blue" style="float:right">
                        </lay-page>
                    </div>
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
