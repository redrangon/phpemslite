<template>
	<lay-card>
		<lay-table :columns="courseColumns" :data-source="datacourseSource">
			<template #toolbar>
				课程
				<lay-button size="xs" type="primary" style="float:right;"  @click="setCourse()"> + 添加课程</lay-button>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="danger"  @click="delItem(row.piid)">移除</lay-button>
			</template>
		</lay-table>
		<lay-table :columns="examColumns" :data-source="examSource" style="margin-top:20px;">
			<template #toolbar>
				考场
				<lay-button size="xs" type="primary" style="float:right;"  @click="setExam()"> + 添加考场</lay-button>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary"  @click="statsbasic(row.basicid)">统计</lay-button>
				<lay-button size="xs" type="danger"  @click="delItem(row.piid)">移除</lay-button>
			</template>
		</lay-table>
		<lay-table :columns="certColumns" :data-source="certSource" style="margin-top:20px;">
			<template #toolbar>
				凭证
				<lay-button size="xs" type="primary" style="float:right;"  @click="setCert()"> + 添加凭证</lay-button>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="danger"  @click="delItem(row.piid)">移除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showCoursePage" :area="['800px']" title="添加课程">
		<lay-card>
			<lay-space>
				<lay-space></lay-space>
				<lay-space>
					<span style='width:70px'> 关键字：</span><lay-input v-model="search.course" size="sm" style="width: 180px;" allow-clear></lay-input>
				</lay-space>
				<lay-space>
					<lay-button type="primary" size="sm" @click="getCourseData">搜索</lay-button>
				</lay-space>
			</lay-space>
		</lay-card>
		<lay-card>
			<lay-table :columns="courselistcolumns" :data-source="courses" v-model:selectedKeys="courseselectedKeys" id="csid">
				<template #footer>
					<lay-button type="primary"  @click="joincourse">加入计划</lay-button>
				</template>
			</lay-table>
		</lay-card>
	</lay-layer>
	<lay-layer v-model="showBasicPage" :area="['800px']" title="添加考场">
		<lay-card>
			<lay-space>
				<lay-space></lay-space>
				<lay-space>
					<span style='width:70px'> 关键字：</span><lay-input v-model="search.basic" size="sm" style="width: 180px;" allow-clear></lay-input>
				</lay-space>
				<lay-space>
					<lay-button type="primary" size="sm" @click="getBasicData">搜索</lay-button>
				</lay-space>
			</lay-space>
		</lay-card>
		<lay-card>
			<lay-table :columns="basiclistcolumns" :data-source="basics" v-model:selectedKeys="basicselectedKeys" id="basicid">
				<template #footer>
					<lay-button type="primary"  @click="joinbasic">加入计划</lay-button>
				</template>
			</lay-table>
		</lay-card>
	</lay-layer>
	<lay-layer v-model="showCertPage" :area="['800px']" title="添加凭证">
		<lay-card>
			<lay-space>
				<lay-space></lay-space>
				<lay-space>
					<span style='width:70px'> 关键字：</span><lay-input v-model="search.cert" size="sm" style="width: 180px;" allow-clear></lay-input>
				</lay-space>
				<lay-space>
					<lay-button type="primary" size="sm" @click="getCertData">搜索</lay-button>
				</lay-space>
			</lay-space>
		</lay-card>
		<lay-card>
			<lay-table :columns="certlistcolumns" :data-source="certs" v-model:selectedKeys="certselectedKeys" id="ceid">
				<template v-slot:cethumb="{row}">
					<img :src="row.cethumb?row.cethumb:'/src/assets/images/noimages.png'" style="height:40px;max-width:80px;">
				</template>
				<template #footer>
					<lay-button type="primary"  @click="joincert">加入计划</lay-button>
				</template>
			</lay-table>
		</lay-card>
	</lay-layer>
</template>
<style scoped></style>
<script>
import planApi from '@/framework/api/admin/plan.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import baseMixin from '@/framework/mixins/baseMixin.js';

export default {
	data() {
		return {
			courseColumns:[{
				title:"ID",
				width: "50px",
				key: "csid"
			},{
				title:'课程名称',
				key:'cstitle'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"55"
			}],
			examColumns:[{
				title:"ID",
				width: "50px",
				key: "basicid"
			},{
				title:'考场名称',
				key:'basicname'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"55"
			}],
			certColumns:[{
				title:"ID",
				width: "50px",
				key: "ceid"
			},{
				title:'凭证名称',
				key:'cetitle'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"55"
			}],
			courseSource:[],
			examSource:[],
			certSource:[],
			courses:[],
			basics:[],
			certs:[],
			search:ref({}),
			planid:ref(),
			courseSelectedKeys:[],
			basicSelectedKeys:[],
			certSelectedKeys:[],
			showCoursePage:false,
			showBasicPage:false,
			showCertPage:false,
			showQrcode:false
		}
	},
	mixins:[baseMixin],
	async mounted() {
		this.planid = this.$route.params.planid;
		await this.getData();
	},
	methods:{
		getData:async function(){
			await this.getExamData();
			await this.getCourseData();
			await this.getCertData();
		},
		getExamData:async function(){
			await this.execute(async () => {
				this.examSource = await planApi.getItemList({
					planid: this.planid,
					itemtype: 'exam'
				});
			}, null, null);
		},
		getCourseData:async function(){
			await this.execute(async () => {
				this.courseSource = await planApi.getItemList({
					planid: this.planid,
					itemtype: 'course'
				});
			}, null, null);
		},
		getCertData:async function(){
			await this.execute(async () => {
				this.certSource = await planApi.getItemList({
					planid: this.planid,
					itemtype: 'cert'
				});
			}, null, null);
		},
		joincourse:async function(){
			await plan.addPlanSetting('course',this.planid,this.courseselectedKeys);
			this.showCoursePage = false
			this.getData()
		},
		joinbasic:async function(){
			await plan.addPlanSetting('basic',this.planid,this.basicselectedKeys);
			this.showBasicPage = false
			this.getData()
		},
		joincert:async function(){
			await plan.addPlanSetting('cert',this.planid,this.certselectedKeys);
			this.showCertPage = false
			this.getData()
		},
		setCourse:async function(){
			await this.getCourseData()
			this.showCoursePage = true
		},
		setExam:async function(){
			await this.getBasicData()
			this.showBasicPage = true
		},
		setCert:async function(){
			await this.getCertData()
			this.showCertPage = true
		},
		delItem:function(piid){
			plan.delPlanSetting(piid,this.getData);
		},
		openLink:function(title,link){
			layer.confirm(link,{title:title})
		},
		openQrcode:function(link)
		{
			this.mlink = link
			this.showQrcode = true
		},
		planmember:function(planid){
			this.$router.push('/plan/planmember/'+planid);
		},
		statsbasic:function(basicid){
			this.$router.push('/plan/planexamstats/'+this.planid+'/'+basicid);
		}
	}
}
</script>