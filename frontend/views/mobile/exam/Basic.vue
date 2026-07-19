<template>
	<div style="width:100%;">
        <van-nav-bar :title="basic.basic??''" left-arrow @click-left="$router.back()" placeholder fixed/>
		<div class="card-container">
            <!-- 考场控制台信息 -->
	        <van-space direction="vertical" fill>
	            <van-cell-group class="menu-list">
	                <van-cell is-link center to="/mobile/exam/exercise">
		                <template #title>
			                <div style="font-size: 18px;line-height: 2;">课后练习</div>
		                </template>
		                <template #label>
			                <div style="font-size: 14px;padding-top:0;">逐个章节、知识点刷题练习</div>
		                </template>
	                    <template #icon>
	                        <van-icon name="label-o" size="48" class="icon" />
	                    </template>
	                </van-cell>
	            </van-cell-group>
	            <van-cell-group class="menu-list">
	                <van-cell is-link center to="/mobile/exam/exampaper">
	                    <template #title>
			                <div style="font-size: 18px;line-height: 2;">模拟考试</div>
		                </template>
		                <template #label>
			                <div style="font-size: 14px;padding-top:0;">仿真模拟，体验真实考试环境</div>
		                </template>
	                    <template #icon>
	                        <van-icon name="completed-o" size="48" class="icon" />
	                    </template>
	                </van-cell>
	            </van-cell-group>
	            <van-cell-group class="menu-list">
	                <van-cell label="强化训练、模拟和正式考试记录" is-link center to="/mobile/exam/history">
		                <template #title>
			                <div style="font-size: 18px;line-height: 2;">考试记录</div>
		                </template>
		                <template #label>
			                <div style="font-size: 14px;padding-top:0;">强化训练、模拟和正式考试记录</div>
		                </template>
	                    <template #icon>
	                        <van-icon name="chart-trending-o" size="48" class="icon" />
	                    </template>
	                </van-cell>
	            </van-cell-group>
	            <van-cell-group class="menu-list">
	                <van-cell is-link center to="/mobile/exam/favor">
		                <template #title>
			                <div style="font-size: 18px;line-height: 2;">习题收藏</div>
		                </template>
		                <template #label>
			                <div style="font-size: 14px;padding-top:0;">试题收藏 便捷背题</div>
		                </template>
	                    <template #icon>
	                        <van-icon name="list-switch" size="48" class="icon" />
	                    </template>
	                </van-cell>
	            </van-cell-group>
	        </van-space>
		</div>
    </div>
</template>

<script>
	import examApi from '@/framework/api/exam.js';
    export default {
		setup() {

		},
        data() {
            return {
                basic:{}
            };
        },
        async mounted() {
	        this.basic = await examApi.getExamBasic();
            if(this.basic.basicexam?.model === 2)this.$router.replace('/mobile/core/exam/exam');
		},
        methods: {
            //
        }
    };
</script>
<style scoped>
.menu-list div{
	padding:10px 20px;
	font-size: 18px;
	background: transparent;
}
</style>