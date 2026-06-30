<template>
	<lay-card class="pagecontent">
		<lay-card style="position: relative;">
			<lay-page-header :content="basic.basic" @back="$router.go(-1)" class="planbread"></lay-page-header>			
			<lay-tab v-model="tabCurrent" type="brief" activeBarTransition>
				<lay-tab-item id="1">
					<template #title>
						<span class="tabtitle">章节练习</span>
					</template>
					<lay-container v-if="sections.length > 0">
						<template v-for="(section,sid) in sections" :key="sid">
							<div class="sectiontitle" @click="toggleSection(sid)">
								<lay-icon type="layui-icon-right" 
										  :class="['expand-icon', expandedSections[sid] ? 'expanded' : 'collapsed']"></lay-icon>
								{{ section.section }}
							</div>
                            <lay-space direction="vertical" fill wrap style="padding:10px 20px;"
                                       v-for="(point,pid) in section.points"
                                       :key="pid"
                                       v-show="expandedSections[sid]">
                                <div class="sectionitem">
                                    {{ point.point }}
                                    <span>（共 {{ point.pointallnumber}} 题<span v-if="point.progress > 0">，上次做到 {{ point.progress }} 题</span>）</span>
                                    <lay-button type="normal" @click="toPaper(point.pointid)">开始练习</lay-button>
                                </div>
                            </lay-space>
						</template>
					</lay-container>
				</lay-tab-item>
			</lay-tab>
		</lay-card>
	</lay-card>
</template>
<script>
import examApi from '@/framework/api/exam.js'
import {ref} from 'vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {useAuthStore} from "@/stores/auth.js";

export default {
	setup() {
		const basic = ref({});
		basic.value = useAuthStore().basic;
		return {basic}
	},
	mixins: [baseMixin],
	data() {
		return {
			tabCurrent:"1",
			sections:[],
			expandedSections: {} // 记录每个章节的展开状态
		}
	},
	async mounted(){
		if(this.basic.basicexam.model === 2)
		{
			this.$router.replace('/desktop/home/exam/exam');
			return;
		}
		await this.getData();
	},
	methods:{
		getData:async function(){
			await this.execute( async () => {
				this.sections = await examApi.getExerciseList();
				// 初始化所有章节为展开状态
				this.sections.forEach((section, sid) => {
					this.expandedSections[sid] = true;
				});
			},null,null);
		},
		toggleSection:function(sid){
			// 切换指定章节的展开状态
			this.$set ? this.$set(this.expandedSections, sid, !this.expandedSections[sid]) 
					  : (this.expandedSections[sid] = !this.expandedSections[sid]);
		},
		toPaper:function(point){
			this.$router.push('/desktop/home/exam/exercise/' + point);
		}
	}
}
</script>
<style scoped>
.sectiontitle{
	font-size:16px;
	line-height: 3;
	border-bottom: 1px solid #fafafa;
	margin-bottom:10px;
	width: 100%;
	font-weight: 600;
	cursor: pointer;
	display: flex;
	align-items: center;
	user-select: none;
}
.sectiontitle:hover{
	color: #16baaa;
}
.expand-icon{
	margin-right: 8px;
	font-size: 16px;
	transition: transform 0.3s ease-in-out;
}
.expand-icon.expanded{
	transform: rotate(90deg);
}
.expand-icon.collapsed{
	transform: rotate(0deg);
}
.sectionitem{
	line-height:30px;
	padding-bottom:5px;
	border-bottom:1px solid #fafafa;
	font-size:16px;
	clear: both;
	overflow: hidden;
}
.sectionitem span{
	color:#999999;
	margin-left:10px;	
}
.sectionitem button{
	float:right;
}
</style>