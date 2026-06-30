<template>
	<div>
		<lay-card>
		<lay-table :columns="columns" :data-source="dataSource">
			<template #toolbar>
				<span v-if="member">{{member.pmname}}</span> 学习进度
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary"  @click="showProgress(row.csid)">进度详情</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showProgressPage" :area="['1000px']" title="进度详情">
		<lay-table :columns="detailColumns" :data-source="progressSource" style="margin: 10px;">
			<template v-slot:logstatus="{row}">
				<lay-icon type="layui-icon-success" color="#16baaa" v-if="row.logstatus === 1"></lay-icon>
				<lay-icon type="layui-icon-error" v-else></lay-icon>
			</template>
			<template v-slot:logendtime="{row}">
				<span v-if="row.logendtime && row.logendtime !== 0">{{ row.logendtime }}</span>
				<span v-else>未学完</span>
			</template>
			<template v-slot:logcameras="{row}">
				<span v-if="row.logfaces && row.logfaces?.length > 0" @click="openCamera(row.logfaces)">{{row.logfaces?.length}}图</span>
				<span v-else>无</span>
			</template>
		</lay-table>
	</lay-layer>
	</div>
</template>
<style scoped></style>
<script>
import {layer} from '@layui/layui-vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import planApi from "@/framework/api/admin/plan.js";
import memberApi from "@/framework/api/admin/member.js";

export default {
	mixins: [baseMixin],
	data() {
		return {
			columns:[{
				title:"ID",
				width: "50px",
				key: "csid"
			},{
				title:'课程名称',
				key:'cstitle'
			},{
				title:'完成度百分比',
				key:'logRate',
				width:'120'
			},{
				title:'总课时',
				key:'courseNumber',
				width:'120px'
			},{
				title:'已学课时',
				key:'finishNumber',
				width:'120px'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"80px"
			}],
			detailColumns:[{
				title:'课件名称',
				key:'coursetitle'
			},{
				title:'开始学习时间',
				key:'logtime',
				width:'160px'
			},{
				title:'已学时长',
				key:'logprogress',
				width:'120px'
			},{
				title:'学完时间',
				customSlot:"logendtime",
				key:'logendtime',
				width:'160px'
			},{
				title:'完成情况',
				key:"logstatus",
				customSlot:'logstatus',
				width:"80px"
			},{
				title:'人脸识别',
				key:"logcameras",
				customSlot:'logcameras',
				width:"80px"
			}],
			dataSource:[],
			progressSource:[],
			planid:0,
			passport:'',
			member:{},
			csid:0,
			showProgressPage:false,
			courseSubjects:{}
		}
	},
	async mounted() {
		this.planid = this.$route.params.planid
		this.passport = this.$route.params.passport
		this.member = await memberApi.getMember({passport:this.passport});
		await this.getData();
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				this.dataSource = await planApi.getCourseProgressStats({
					planid: this.planid,
					passport: this.passport
				});
			}, null, null);
		},
		getLogsData:async function(){
			await this.execute(async () => {
				this.progressSource = await planApi.getCourseProgress({
					planid: this.planid,
					passport: this.passport,
					csid: this.csid
				});
			},null,null);
		},
		showProgress:async function(csid){
			this.csid = csid
			await this.getLogsData();
			this.showProgressPage = true
		},
		openCamera:function(cameras){
			let imgs = [];
			for(let x in cameras){
				imgs[x] = {src:cameras[x]}
			}
			layer.photos({
				imgList:imgs
			})
		},
		planMember:function(id){
			this.$router.push('/plan/planmember/'+id);
		}
	}
}
</script>