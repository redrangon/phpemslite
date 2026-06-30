<template>
	<div style="width:100%;">
        <van-nav-bar :title="subject.cstitle" left-arrow @click-left="$router.go(-1)"  placeholder fixed/>
		<div class="card-container" v-if="courseId > 0">
	        <template v-if="showFace">
	            <div style="margin-top: 10px;"></div>
	            <van-cell-group inset>
	                <van-cell title="真人校验" title-style="text-align:center"></van-cell>
	                <van-cell>
	                    <myCamera :faceverify="faceVerify"></myCamera>
	                </van-cell>
	                <van-cell title="请进行人脸识别校验后继续学习" title-style="text-align:center"></van-cell>
	            </van-cell-group>
	        </template>
	        <template v-else>
		        <template v-if="course.coursemodule === 'video'">
			        <div v-if="course.coursepath" style="border-radius: 4px;overflow: hidden">
				        <myPlayer :settings="settings" :action="{record,finish}" :progress="progress"></myPlayer>
			        </div>
			        <div v-html="course.coursedescribe" class="content"></div>
		        </template>
		        <template v-else-if="course.coursemodule === 'html'">
			        <div v-html="course.coursedescribe" class="content"></div>
		        </template>
		        <template v-else-if="course.coursemodule === 'md'">
			        <div class="content">
				        <MarkDownView :content="course.coursedescribe"></MarkDownView>
			        </div>
		        </template>
		        <template v-else-if="course.coursemodule === 'pdf'">
			        <VueOfficePdf :src="course.coursepath"/>
		        </template>
		        <template v-else-if="!courseId">
			        <van-cell title-style="flex: 1;min-width: 0;" center>
				        <template #title>
					        <div v-html="subject.csdescribe"></div>
				        </template>
			        </van-cell>
		        </template>
		        <template v-else>
			        <van-cell title-style="flex: 1;min-width: 0;" center>
				        <template #title>
					        <div class="title">当前课件为空目录，请在左侧选择您要查看的课件</div>
				        </template>
			        </van-cell>
			    </template>
	        </template>
		</div>
		<div class="card-container" v-else>
			<courseTree v-model:data="courses" @current-change="viewCourse"></courseTree>
		</div>
		<van-floating-bubble icon="bars" @click="showLessons = !showLessons" />
		<van-popup v-model:show="showLessons" position="left" :style="{ width: '80%', height: '100%',padding:'10px' }" safe-area-inset-top>
			<courseTree v-model:data="courses" @current-change="viewCourse"></courseTree>
		</van-popup>
    </div>
</template>

<script>
import courseApi from '@/framework/api/course.js';
import {ref} from 'vue';
import myPlayer from '@/components/mobile/Player.vue';
import myCamera from '@/components/mobile/Camera.vue';
import courseTree from '@/components/mobile/CourseTree.vue';
import MarkDownView from "@/components/mobile/MarkDownView.vue";
import VueOfficePdf from '@vue-office/pdf'
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
    data() {
        return {
            settings:{source:false},
			progress:{progressControl:true},
	        subject:{},
	        cdata: {},
	        courseId:0,
			videoTab:"1",
			tabCurrent:"1",
			lesson:[],
			course:{},
	        courses:[],
			logs:[],
			showFace:false,
            showLessons:false,
			player:null,
            width:document.documentElement.clientWidth - 22,
            height:(document.documentElement.clientWidth - 22) * 9 /16,
        };
    },
    components:{
		myPlayer,
	    myCamera,
	    courseTree,
	    MarkDownView,
	    VueOfficePdf
	},
    async mounted() {
	    try{
		    this.courseId = this.$route.params.courseid??0;
		    this.subject = await courseApi.getCourseSubject();
		    await this.getCourseTree();
	    }catch(e){
		    this.$router.push('/mobile/core/myplan');
	    }
	},
    methods: {
        getData:async function(){
	        if(this.courseId > 0)
	        {
		        this.course = await courseApi.getCourse(this.courseId);
		        this.progress = {
			        progressControl:this.subject.csprogress === 1,
			        currentTime: this.course.logprogress??0
		        };
		        if(this.course.coursemodule === 'video')
		        {
					this.settings = {
						source:this.course?.coursepath??null,
						width:this.width,
						height:this.height,
						autoplay:true
					};
		        }
	        }
		},
	    getCourseTree: async function() {
		    const data = await courseApi.getAllCourse();
		    this.courses = await courseApi.buildCourseTree(data);
	    },
	    viewCourse:function(data){
		    this.courseId = data.courseid
		    this.showLessons = false;
		    this.getData();
	    },
	    record:async function(time,player){
		    this.player = player;
		    await this.execute(async ()=>{
			    const res = await courseApi.recordCourseProgress({
				    courseid:this.courseId,
				    time:time
			    })
			    if(res.faceVerify === 1)
			    {
				    this.player.pause();
				    this.showFace = true;
			    }
		    },null,null)
	    },
	    finish:async function(){
		    await this.execute(async ()=>{
			    await courseApi.finishCourse({
				    courseid:this.courseId
			    })
			    await this.getCourseTree();
		    },null,null);
	    },
	    faceVerify:async function(face)
	    {
		    await this.execute(async ()=>{
			    const data = await courseApi.verifyCourseFace({
				    courseid:this.courseId,
				    face
			    });
			    this.showFace = false;
			    this.player.play();
		    },null,'人脸检测失败，请重试');
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
