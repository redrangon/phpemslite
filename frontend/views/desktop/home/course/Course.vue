<template>
	<lay-backtop></lay-backtop>
	<lay-side width="360px" style="padding:10px 0px;">
		<lay-card style="width: 100%;padding:10px;" v-if="courses?.length > 0">
			<courseTree v-model:data="courses" @current-change="viewCourse"></courseTree>
		</lay-card>
	</lay-side>
	<lay-body class="frontLayBody">
		<lay-card class="pagecontent">

			<lay-card style="position: relative;" v-if="courseid > 0">
				<lay-page-header :content="subject.cstitle" @back="$router.go(-1)" class="planbread"></lay-page-header>
				<lay-tab v-model="tabCurrent" type="brief">
					<lay-tab-item id="1">
						<template #title>
							<span class="tabtitle">培训课程</span>
						</template>
						<template v-if="course.coursemodule === 'video'">
							<div v-if="course.coursepath">
								<myPlayer :settings="settings" :action="{record,finish}" :progress="progress"></myPlayer>
							</div>
							<div v-else>
								<div v-html="course.coursedescribe" class="content"></div>
							</div>
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
						<template v-else>
							<div class="content">当前课件为空目录，请在左侧选择您要查看的课件</div>
						</template>
					</lay-tab-item>
				</lay-tab>
				<lay-tab v-model="videoTab" type="brief" :activeBarTransition="true" v-if="course.coursemodule === 'video'">
					<lay-tab-item id="t1">
						<template #title>
							<span class="tabtitle">课件内容</span>
						</template>
						<lay-container>
							<div v-html="course?.coursedescribe" class="content"></div>
						</lay-container>
					</lay-tab-item>
				</lay-tab>
			</lay-card>
			<lay-card style="position: relative;" v-else>
				<lay-page-header :content="subject.cstitle" @back="$router.go(-1)" class="planbread"></lay-page-header>
				<lay-tab v-model="tabCurrent" type="brief">
					<lay-tab-item id="1">
						<template #title>
							<span class="tabtitle">培训课程</span>
						</template>
						<div class="courseContent">
							<h1 class="courseTitle">{{subject.cstitle}}</h1>
							<div v-html="subject.csdescribe" class="content"></div>
						</div>
					</lay-tab-item>
				</lay-tab>
			</lay-card>
		</lay-card>
		<lay-layer v-model="showFace" :shade="true" shadeOpacity="0.6" :area="['500px']" title="人脸识别" :closeBtn="false" :shadeClose="false">
			<myCamera :faceverify="faceVerify" style="width:494px;"></myCamera>
		</lay-layer>
	</lay-body>
</template>
<script>
import courseApi from '@/framework/api/course.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import myPlayer from '@/components/desktop/Player.vue';
import myCamera from '@/components/desktop/Camera.vue';
import courseTree from '@/components/desktop/CourseTree.vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import MarkDownView from "@/components/desktop/MarkDownView.vue";
import VueOfficePdf from '@vue-office/pdf'
export default {
	mixins: [baseMixin],
	data() {
		return {
			settings:{
				source:false
			},
			progress:{
				progressControl:true
			},
			subject:{},
			cdata: {},
			courseid:0,
			videoTab:"t1",
			tabCurrent:"1",
			lesson:[],
			course:{},
			courses:[],
			logs:[],
			showFace:false,
			player:null
		}
	},
	async mounted() {
		try{
			this.subject = await courseApi.getCourseSubject();
			await this.getCourseTree();
		}catch(e){
			this.router.push('/desktop/home/core/myplan');
		}

	},
	components:{
		myPlayer,
		myCamera,
		courseTree,
		MarkDownView,
		VueOfficePdf
	},
	methods:{
		getData:async function(){
			if(this.courseid)
			{
				this.course = await courseApi.getCourse(this.courseid);
				this.progress = {
					progressControl:this.subject.csprogress === 1,
					currentTime: this.course.logprogress??0
				};
				if(this.course.coursemodule === 'video')this.settings = {source:this.course?.coursepath??null,width:760,height:480,autoplay:true};
			}
		},
		getCourseTree: async function() {
			const data = await courseApi.getAllCourse();
			this.courses = await courseApi.buildCourseTree(data);
		},
		viewCourse:function(data){
			this.courseid = data.courseid
			this.getData();
		},
		record:async function(time,player){
			this.player = player;
			await this.execute(async ()=>{
				const res = await planApi.recordCourseProgress({
					courseid:this.courseid,
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
					courseid:this.courseid
				})
				await this.getCourseTree();
			},null,null);
		},
		faceVerify:async function(face)
		{
			await this.execute(async ()=>{
				const data = await courseApi.verifyCourseFace({
					courseid:this.courseid,
					face
				});
				this.showFace = false;
				this.player.play();
			},null,'人脸检测失败，请重试');
		}
	}
}
</script>
<style>
.courseTitle{
	font-size: 18px;
	font-weight: 600;
	text-align: center;
	line-height: 2;
}
.content{
	font-size: 16px;
	padding:10px;
	line-height: 2;
}
.content p{
	text-indent: 2em;
}
</style>