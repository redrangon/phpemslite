<template>
    <div class="page">
        <van-nav-bar :title="lesson.cstitle" left-arrow @click-left="$router.go(-1)"  placeholder fixed/>
        <template v-if="showFace">
            <div style="margin-top: 10px;"></div>
            <van-cell-group inset>
                <van-cell title="真人校验" title-style="text-align:center"></van-cell>
                <van-cell>
                    <myCamera :faceverify="faceverify"></myCamera>
                </van-cell> 
                <van-cell title="请进行人脸识别校验后继续学习" title-style="text-align:center"></van-cell>
            </van-cell-group>                       
        </template>
        <template v-else>
            <template v-if="settings.source">
                <div>
                    <myPlayer :settings="settings" :action="{record,finish}" :progress="progress"></myPlayer>
                </div>
                <div class="desc" :style="`height: ${height}px;overflow-y: auto;`" v-html="video.coursedescribe"></div>
            </template>
            <template v-else>
                <div class="desc" v-html="video.coursedescribe"></div>   
            </template>            
            <van-floating-bubble icon="bars" @click="showLessons = !showLessons" />
            <van-popup v-model:show="showLessons" position="left" :style="{ width: '80%', height: '100%',padding:'10px' }" safe-area-inset-top>
                <courseTree v-model:data="videos" @current-change="playvideo"></courseTree>
            </van-popup>        
        </template>
    </div>
</template>

<script>
import plan from '@/framework/api/plan.js';
import courseApi from '@/framework/api/course.js';
import {ref} from 'vue';
import myPlayer from '@/components/desktop/Player.vue';
import myCamera from '@/components/desktop/Camera.vue';
import { showSuccessToast, showFailToast } from 'vant';
import courseTree from '@/components/desktop/CourseTree.vue';
export default {
    data() {
        return {
            settings:ref({source:false}),
			progress:ref({progressControl:true}),
			csid:ref(),
			cdata:ref(),
			videoid:ref(),
			plan:ref({}),
			videoTab:ref("1"),
			tabCurrent:ref("1"),
			lesson:ref([]),
			video:ref({}),
			videos:ref([]),
			logs:ref({}),
			showFace:ref(false),
            showLessons:ref(false),
			player:ref(),
            width:document.documentElement.clientWidth - 2,
            height:document.documentElement.clientHeight - 50 - (document.documentElement.clientWidth - 2) * 9 /16,
        };
    },
    components:{
		myPlayer,myCamera,courseTree
	},
    async created() {
		this.csid = this.$route.params.csid;
		await this.getData();
	},
    methods: {
        getData:async function(){           
            const data = await course.getPlayer({csid:this.csid,videoid:this.videoid});
            let videos = data.videos;
			videos = videos.map(item =>{
				return {
					...item,
					module:item.coursemoduleid == 1 ? 'dirs' : 'course',
					expanded: false,
					isCurrent:item.courseid == data.video['courseid'],
					logstatus:data.logs?.[item.courseid]?.logstatus,
					logprogress:data.logs?.[item.courseid]?.logprogress,
					children: []
				};
			});
            const buildTree = function(data)
			{
				// 将原始对象转换为数组，并为每个节点添加 children
				const items = data;

				// 创建一个以 courseid 为 key 的映射，便于快速查找
				const itemMap = {};
				items.forEach(item => {
					itemMap[item.courseid] = item;
				});

				// 根节点数组
				const roots = [];

				// 遍历所有节点，将其加入父节点的 children 中
				items.forEach(item => {
					if (item.coursedirid === 0) {
						// 根节点
						roots.push(item);
					} else {
						// 找到父节点
						const parent = itemMap[item.coursedirid];
						let thisNode = item;
						if(item.isCurrent)thisNode.expanded = true;
						while(thisNode.expanded)
						{
							let parentNode = itemMap[thisNode.coursedirid];
							if(parentNode?.coursedirid >= 0)
							{
								parentNode.expanded = true;
								thisNode = parentNode;
							}
							else break;
						}
						if (parent) {
							parent.children.push(item);
						} else {
							// 如果父节点不存在（数据异常），也可以选择将其作为根节点或忽略
							roots.push(item);
						}
					}
				});
				return roots;
			}

            this.plan = data.plan;
			this.lesson = data.lesson;
			this.videos = buildTree(videos);
			this.logs = data.logs;
			this.cdata = data.cdata;
			this.video = data.video;
			this.videoid = this.video.courseid;
			if(this.video.coursemoduleid == 2)this.settings = {source:this.video.course_files,width:this.width,height:this.width* 9 /16};
            else
            {
                this.settings = {source:false};
                this.finish();
            }
			this.progress.progressControl = this.lesson.csprogress == 1?false:true;
			this.progress.currentTime = this.logs[this.videoid]?this.logs[this.videoid].logprogress:0;
		},
		playvideo:function(data){
			this.videoid = data.courseid;
            this.getData();
            this.showLessons = false;
		},
		record:async function(time,player){
			this.player = player;
			const res = await course.recordVideo({
				videoid:this.videoid,
				time:time	
			})
			if(res && res.faceverify)
			{
                player.pause();
				this.showFace = true;
			}
		},
		finish:async function(){
			await course.finishVideo({
				videoid:this.videoid	
			})
		},
		faceverify:async function(face)
		{
			const data = await course.faceVerify({
				videoid:this.videoid,
				face
			});
			if(data){
				this.showFace = false;
				this.player.play();
			}
		}
    }
};
</script>

<style scoped>
/* 可以添加自定义样式 */
.current{
    color: #1E9FFF;
}
.desc{
    box-sizing: border-box;
    width: 100%;
    padding: 10px 15px;
    text-align: left;
    line-height: 2;
    color:#333333;
    background-color: #FFFFFF;
}
:deep(.desc p){
    text-indent: 2em;
}
:deep(.desc img){
    max-width: 100%!important;
    height: auto!important;
}
</style>
