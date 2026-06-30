<template>
	<div style="width:100%;">
        <van-nav-bar title="章节练习" left-arrow @click-left="$router.back()" placeholder fixed/>
        <div class="card-container">
        <!-- 考场控制台信息 -->
	        <van-space direction="vertical" fill>
	            <van-cell-group v-for="(section,sid) in sections" :key="sid">
		            <van-cell>
			            <template #title>
				            <span class="title">{{section.section}}</span>
			            </template>
		            </van-cell>
		            <van-cell v-for="(point,pid) in section.points" :key="pid" is-link center :to="`/mobile/exam/exercise/${point.pointid}`">
			            <template #title>
				            <span class="title">{{point.point}}</span>
			            </template>
			            <template #label>
				            <span class="intro">共 {{ point.pointallnumber}} 题<span v-if="point.progress > 0">，上次做到 {{ point.progress }} 题</span></span>
			            </template>
		            </van-cell>
	            </van-cell-group>
	        </van-space>
        </div>
    </div>
</template>

<script>
	import examApi from '@/framework/api/exam.js'
	import baseMixin from "@/framework/mixins/baseMixin.js";
	import {useAuthStore} from "@/stores/auth.js"
	import {ref} from 'vue';
    export default {
	    setup() {
		    const basic = ref({});
		    basic.value = useAuthStore().basic;
		    return {basic}
	    },
	    mixins: [baseMixin],
        data() {
            return {
                sections:[]
            };
        },
        async mounted() {
			await this.getData();
		},
        methods: {
            getData:async function(){
	            await this.execute( async () => {
		            this.sections = await examApi.getExerciseList();
	            },null,null);
            }
        }
    };
</script>
<style scoped>
.title{
	font-size:18px;
	line-height: 2.5;
}
.intro{
	font-size:14px;
}
</style>