<template>
    <div style="width:100%;">
        <!-- 导航条 -->
        <van-nav-bar title="考场列表" fixed left-arrow  placeholder @click-left="$router.go(-1)"/>
        <div class="card-container">
            <van-space direction="vertical" fill>
                <van-list v-if="page.total > 0" v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoad">
                    <van-cell-group v-if="exams.length >= 1" class="menu-list">
                        <van-cell v-for="(basic, index) in exams" :key="index" :is-link="true" :title="basic.basic" center title-style="flex: 1;min-width: 0;" :to="`/mobile/exam/detail/${basic.basicid}`">
                            <template #label>
                                <div class="detailDesc" v-html="basic.basicdescribe"></div>
                            </template>
                        </van-cell>
                    </van-cell-group>
                </van-list>
                <van-empty v-else description="当前没有考试"></van-empty>
            </van-space>
        </div>
    </div>
</template>
<style scoped>
.my-swipe .van-swipe-item {
    color: #fff;
    font-size: 20px;
    line-height: 180px;
    text-align: center;
    background-color: #39a9ed;
}
.thumb{
    width: 60px;
    height: 48px;
    object-fit: cover;
    margin:5px 15px 5px 0px;
}
.menu-list div{
    padding:15px 20px;
    font-size: 16px;
    background: transparent;
}
.detailDesc{
    padding:10px 0!important;
    font-size: 14px!important;
    line-height: 1.75;
    max-height: 60px;
    overflow: hidden
}
</style>
<script>
	import examApi from '@/framework/api/exam.js';
    import baseMixin from "@/framework/mixins/baseMixin.js";
    export default {
        mixins: [baseMixin],
		setup() {

		},
        data() {
            return {
                exams:[],
                loading: false,
                finished: false,
                page:{
                    current:1,
                    total:0,
                    limit:10
                },
            };
        },
        async mounted() {
	        await this.getData();
		},
        methods: {
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
            onLoad:async function() {
                // 加载更多数据
                let data = await examApi.getBasicList({
                    page:this.page.current + 1,
                    limit:this.page.limit,
                });
                this.page.current = data.page;
                this.page.total = data.total;
                this.page.limit = data.limit;
                for(let i in data.data){
                    this.exams.push(data.data[i])
                }
                if(this.exams.length >= this.page.total){
                    this.finished = true;
                }
            },
        }
    };
</script>