<template>
	<div style="width:100%;">
        <van-nav-bar :title="subject.cstitle" left-arrow @click-left="$router.go(-1)"  placeholder fixed/>
		<div class="card-container">
			<courseTree v-model:data="courses" @current-change="viewCourse"></courseTree>
		</div>
    </div>
</template>

<script>
import courseApi from '@/framework/api/course.js';
import {ref} from 'vue';
import myPlayer from '@/views/components/mobile/Player.vue';
import myCamera from '@/views/components/mobile/Camera.vue';
import courseTree from '@/views/components/mobile/CourseTree.vue';
import MarkDownView from "@/views/components/mobile/MarkDownView.vue";
import VueOfficePdf from '@vue-office/pdf'
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
    data() {
        return {
            subject:{},
	        courses:[]
        };
    },
    components:{
	    courseTree
	},
    async mounted() {
	    try{
		    this.subject = await courseApi.getCourseSubject();
		    await this.getCourseTree();
	    }catch(e){
		    this.$router.push('/mobile/core/course');
	    }
	},
    methods: {
        getData:async function(){
            //
		},
	    getCourseTree: async function() {
		    const data = await courseApi.getAllCourse();
		    this.courses = await courseApi.buildCourseTree(data);
	    },
	    viewCourse:function(data){
            this.$router.push('/mobile/course/play/'+data.courseid);
	    }
    }
};
</script>

<style scoped>
.courseTree div{
	padding:10px;
}
/* 可以添加自定义样式 */
.current{
    color: #1E9FFF;
}
.content{
    box-sizing: border-box;
    width: 100%;
    padding: 10px 20px;
    text-align: left;
    line-height: 2;
    color:#333333;
    background-color: #FFFFFF;
	font-size: 16px;
}
:deep(.desc p){
    text-indent: 2em;
}
:deep(.desc img){
    max-width: 100%!important;
    height: auto!important;
}
</style>
