<template>
	<div class="pagecontent">
		<!-- 新闻列表 -->
		<lay-card :bordered="false" class="news-list-card">
            <lay-tab v-model="tabCurrent1" type="brief">
                <lay-tab-item id="tc1_1">
                    <template #title>
                        <span class="tabtitle">
                            <span>证书列表</span>
                        </span>
                    </template>
                    <div v-if="page.total > 0" class="news-list">
                        <div v-for="cert in certs" :key="cert.ceid" class="news-item" @click="toDetail(cert.cetext)">
                            <div class="news-image">
                                <img :src="cert.cethumb" :alt="cert.cetitle" />
                            </div>
                            <div class="news-content">
                                <h3 class="news-title">{{ cert.cetitle }}</h3>
                                <p class="news-desc">{{ cert.cedescribe }}</p>
                                <div class="news-footer">
                                    <lay-button type="primary" size="md">
                                        查看详情
                                    </lay-button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <lay-empty v-else description="暂无证书数据"></lay-empty>
                    <div class="pagination-wrapper" v-if="page?.total > 0">
                        <lay-page v-model="page.current"  :layout="layout" v-model:limit="page.limit" :total="page.total"  @change="pageChange" theme="blue" style="float:right">
                        </lay-page>
                    </div>
                </lay-tab-item>
            </lay-tab>
		</lay-card>
        <lay-layer v-model="showDetailPage" :area="['960px','90%']" title="证书详情">
            <div style="padding: 20px;">
                <lay-container>
                    <lay-card>
                        <h2 style="text-align: center;">证书详情</h2>
                        <lay-line></lay-line>
                        <div v-html="detail" class="layui-font-16 cert-detail"></div>
                    </lay-card>
                </lay-container>
            </div>
        </lay-layer>
	</div>
</template>

<script>
import { ref } from 'vue';
import certApi from "@/framework/api/cert.js";
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
    mixins: [baseMixin],
	data() {
		return {
            tabCurrent1:"tc1_1",
            layout:['count', 'prev', 'page', 'next'],
            page:{current:1,total:0,limit:10},
			certs: [],
            showDetailPage:false,
		};
	},
	async mounted() {
		await this.getData();
	},
	methods: {
		// 加载新闻列表
		async getData() {
			// 模拟JSON数据
            await this.execute(async () => {
                const data = await certApi.getCertList({
                    page:this.page.current,
                    limit:this.page.limit
                });
                this.page.current = data.page;
                this.page.limit = data.limit;
                this.page.total = data.total;
                this.certs = data.data;
            },null,null);
		},

        pageChange:async function({current,limit}){
            this.page.current = current
            this.page.limit = limit
            await this.getData()
        },
        toDetail:function(cetext){
            this.detail = cetext;
            this.showDetailPage = true;
        }
	}
};
</script>

<style scoped>
.news-thumb img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	transition: transform 0.3s ease;
}

.hot-news-item:hover .news-thumb img {
	transform: scale(1.1);
}

.top-tag {
	position: absolute;
	top: 8px;
	right: 8px;
}

.news-title {
	font-size: 16px;
	font-weight: bold;
	color: #333;
	line-height: 1.5;
	margin-bottom: 8px;
	overflow: hidden;
	text-overflow: ellipsis;
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
}
/* 新闻列表 */

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
	border-color: #1e9fff;
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

.news-info {
	display: flex;
	align-items: center;
	gap: 8px;
	font-size: 13px;
	color: #999;
}

/* 分页 */
.pagination-wrapper {
	margin-top: 30px;
	display: flex;
	justify-content: center;
}
.cert-detail{
    line-height: 2;
}
</style>
