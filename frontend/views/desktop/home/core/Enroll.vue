<template>
	<lay-card class="pagecontent">
		<lay-card>
			<lay-tab v-model="tabCurrent" type="brief" :activeBarTransition="true">
				<lay-tab-item id="1">
					<template #title>
						<span class="tabtitle">我的报名</span>
					</template>
					<div style="margin-top: 20px;">
						<lay-row space="20">
							<template v-for="(plan,pid) in plans" :key="pid">
								<div class="planbox">
									<lay-row space="20">
										<lay-col xs="24" sm="8" class="img">
											<div class="img-wrapper">
												<img :src="plan.planthumb" :alt="plan.planname">
											</div>
										</lay-col>
										<lay-col xs="24" sm="16">
											<h3 class="title">{{ plan.planname }}</h3>
											<div class="info-row">
												<span class="info-label">培训时间</span>
												<span class="info-value">{{ plan.planstarttime }} - {{ plan.planendtime }}</span>
											</div>
											<div class="info-row">
												<span class="info-label">报名费用</span>
												<span class="price" :class="{ 'price-free': !plan.planprice }">
													{{ plan.planprice ? '￥' + plan.planprice : '免费' }}
												</span>
											</div>
											<div class="btn-wrapper">
												<lay-button type="primary" radius size="md" @click="enrollPlan(plan.planid)">
													报名信息
												</lay-button>
											</div>
										</lay-col>
									</lay-row>
								</div>
							</template>
						</lay-row>
						<lay-page v-if="page.total > page.limit" v-model="page.current"  :layout="layout" v-model:limit="page.limit" :total="page.total"  @change="pageChange" theme="blue" style="float:right"></lay-page>
					</div>
				</lay-tab-item>
			</lay-tab>
		</lay-card>
		<lay-layer v-model="showEnrollPage" :area="['900px','90%']" :btn="enrollPageBtns" title="报名参加">
			<lay-card>
				<lay-form :model="model" :pane="false" size="md" labelWidth="100" class="form" ref="addPageFrom">
					<lay-form-item label="照片" prop="mphoto" required>
						<myThumb v-model:src="model.mphoto" style="width:90px;height:120px;"></myThumb>
					</lay-form-item>
					<lay-form-item label="姓名" prop="mname" required>
						<lay-input v-model="model.mname" disabled></lay-input>
					</lay-form-item>
					<lay-form-item label="性别" prop="msex">
						<lay-radio v-model="model.msex" name="msex" value="男" label="男"></lay-radio>
						<lay-radio v-model="model.msex" name="msex" value="女" label="女"></lay-radio>
					</lay-form-item>
					<lay-form-item label="通行证ID" prop="mpassport" required>
						<lay-input v-model="model.mpassport" disabled></lay-input>
					</lay-form-item>
					<lay-form-item label="身份证正反面" prop="mpassportimga" mode="inline">
						<myThumb v-model:src="model.mpassportimga" style="width:160px;height:120px;"></myThumb>
					</lay-form-item>
					<lay-form-item label="单位全称" prop="munitallname" required>
						<lay-input v-model="model.munitallname" placeholder="请输入单位全称"></lay-input>
					</lay-form-item>
					<lay-form-item label="学历" prop="medu">
						<lay-select v-model="model.medu" placeholder="请选择学历">
							<lay-select-option value="初中" label="初中"></lay-select-option>
							<lay-select-option value="高中" label="高中"></lay-select-option>
							<lay-select-option value="大专" label="大专"></lay-select-option>
							<lay-select-option value="本科" label="本科"></lay-select-option>
							<lay-select-option value="研究生" label="研究生"></lay-select-option>
						</lay-select>
					</lay-form-item>
					<lay-form-item label="职称" prop="mjobtitle">
						<lay-input v-model="model.mjobtitle" placeholder="请输入职称"></lay-input>
					</lay-form-item>
					<lay-form-item label="政治面貌" prop="mface">
						<lay-select v-model="model.mface" placeholder="请选择政治面貌">
							<lay-select-option value="团员" label="团员"></lay-select-option>
							<lay-select-option value="党员" label="党员"></lay-select-option>
							<lay-select-option value="预备党员" label="预备党员"></lay-select-option>
							<lay-select-option value="无党派人士" label="无党派人士"></lay-select-option>
							<lay-select-option value="群众" label="群众"></lay-select-option>
						</lay-select>
					</lay-form-item>
					<lay-form-item label="档案编号" prop="marchivesnumber">
						<lay-input v-model="model.marchivesnumber" placeholder="请输入档案编号"></lay-input>
					</lay-form-item>
					<lay-form-item label="参加工作年份" prop="mjobtime">
						<lay-date-picker type="year" v-model="model.mjobtime"></lay-date-picker>
					</lay-form-item>
				</lay-form>
			</lay-card>
		</lay-layer>
		<lay-layer v-model="showPlanInfoPage" :area="['900px']" title="计划详情">
			<lay-card>
				<lay-tab v-model="tabCurrent2" type="card" tabPosition="left">
					<lay-tab-item id="11">
						<template #title>
							<span>培训详情</span>
						</template>
						<div class="content">
							{{ plan.plandescribe }}
							<lay-empty v-if="!plan.plandescribe"></lay-empty>
						</div>						
					</lay-tab-item>
					<lay-tab-item id="12">
						<template #title>
							<span>培训课程</span>
						</template>
						<lay-container>	
							<lay-card>						
								<lay-table :columns="courseColumns" :data-source="lessons">
									<template #thumb="{row}">
										<lay-avatar :src="row.csthumb?row.csthumb:'/src/assets/images/noimages.png'" size="lg"></lay-avatar>
									</template>
								</lay-table>
							</lay-card>
						</lay-container>
					</lay-tab-item>
					<lay-tab-item id="13">
						<template #title>
							<span>参与考试</span>
						</template>
						<lay-container>	
							<lay-card>
								<lay-table :columns="examColumns" :data-source="basics">
									<template #thumb="{row}">
										<lay-avatar :src="row.basicthumb?row.basicthumb:'/src/assets/images/noimages.png'" size="lg"></lay-avatar>
									</template>
								</lay-table>
							</lay-card>
						</lay-container>
					</lay-tab-item>
					<lay-tab-item id="14">
						<template #title>
							<span>获得凭证</span>
						</template>
						<lay-container>	
							<lay-card>
								<lay-table :columns="certColumns" :data-source="certs">
									<template #thumb="{row}">
										<lay-avatar :src="row.cethumb?row.cethumb:'/src/assets/images/noimages.png'" size="lg"></lay-avatar>
									</template>
								</lay-table>
							</lay-card>
						</lay-container>
					</lay-tab-item>
				</lay-tab>
			</lay-card>
		</lay-layer>
		<lay-layer v-model="showPage" :area="['900px']" title="报名信息">
			<div style="padding: 20px;">
				<table class="table">
					<thead>
						<td colspan="7">学员报名表</td>
					</thead>
					<tr>
						<th>姓名</th>
						<td>{{ member.pmname }}</td>
						<th>性别</th>
						<td>{{ member.pmsex }}</td>
						<th>通行证ID</th>
						<td>{{ member.pmpassport }}</td>
						<td rowspan="4"><img :src="member.pmphoto" width="120"></td>
					</tr>
					<tr>
						<th>政治面貌</th>
						<td>{{ member.pmface }}</td>
						<th>文化程度</th>
						<td>{{ member.pmedu }}</td>
						<th>职称</th>
						<td>{{ member.pmjobtitle }}</td>
					</tr>
					<tr>
						<th>档案编号</th>
						<td colspan="2">{{ member.pmnumber }}</td>
						<th colspan="2">参加工作时间</th>
						<td>{{ member.pmjobtime }}年</td>
					</tr>
					<tr>
						<th>单位全称</th>
						<td colspan="5">{{ member.pmunitallname }}</td>
					</tr>
					<thead>
						<td colspan="7">证件信息</td>
					</thead>
					<tr>
						<td colspan="7">
							<img :src="member.pmpassporta" style="max-width:50%;">
						</td>
					</tr>
				</table>
			</div>
		</lay-layer>		
	</lay-card>
</template>
<script>
import planApi from '@/framework/api/plan.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import myEditor from '@/components/desktop/Editor.vue';
import myThumb from '@/components/desktop/Thumb.vue';
import userApi from '@/framework/api/user.js';
export default {
	data() {
		return {
			plan:[],
			enrollPlanId:[],
			model:{},
			lessons:[],
			basics:[],
			certs:[],
			courseColumns:[{
				title:'缩略图',
				customSlot:'thumb',
				width:"80px"
			},{
				title:'课程名称',
				key:'cstitle'
			}],
			examColumns:[{
				title:'缩略图',
				customSlot:'thumb',
				width:"80px"
			},{
				title:'考场名称',
				key:'basic'
			}],
			certColumns:[{
				title:'缩略图',
				customSlot:'thumb',
				width:"80px"
			},{
				title:'凭证名称',
				key:'cetitle'
			},{
				title:'有效期',
				key:'cedays',
				width:"150"
			}],
			enrollColumns:[{
				title:'培训信息',
				key:'planname'
			},{
				title:'培训时间',
				customSlot:'plantime',
				width:"200px"
			},{
				title:'报名时间',
				key:'pmtime',
				width:"120px"
			},{
				title:'审核',
				customSlot:'pmverify',
				width:"80"
			},{
				title:'缴费',
				customSlot:'pmpayment',
				width:"80"
			},{
				title:'操作',
				customSlot:'operator',
				width:"80"
			}],
			enrolls:[],
			plans:[],
			showPage:false,
			showPlanInfoPage:false,
			showEnrollPage:false,
			member:[],
			payqrcode:[],
			tabCurrent:"1",
			tabCurrent2:"11",
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{current:1,total:1,limit:9},
			enrollPage:{current:1,total:1,limit:10},
			planPage:{current:1,total:1,limit:10},
			enrollPageBtns:[
				{
					text: "确认",
					callback: () => {
						console.log(this.model);
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showEnrollPage = false;
							this.addPlanSignup();
						}).catch( res => {
							//
						});
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showEnrollPage = false;
					}
				}
			]
		}
	},
	components:{
		myEditor,
		myThumb
	},
	async mounted() {
		this.tabCurrent = this.$route.params.tabid??ref("1");
		await this.getData()
	},
	methods:{
		getData:async function(){
			if(this.tabCurrent === '1'){
				await this.getMyPlans()
			}
			if(this.tabCurrent === '2'){
				await this.getEnrolls()
			}
			if(this.tabCurrent === '3'){
				await this.getPlans()
			}	
		},
		getMyPlans:async function(){
			layer.closeAll();
			const id = layer.load(0);
			let data = await planApi.getMyPlans({
				page:this.page.current,
				limit:this.page.limit
			});
			this.page.current = data.page
			this.page.total = data.total
			this.page.limit = data.limit
			this.myplans = data.data;
			layer.close(id);
		},
		getEnrolls:async function(){
			layer.closeAll();
			const id = layer.load(0);
			let data = await planApi.getMyEnrolls({
				page:this.enpage.current,
				limit:this.enpage.limit
			});
			this.enpage = data.page
			this.myenrolls = data.data;
			layer.close(id);	
		},
		getPlans:async function(){
			layer.closeAll();
			const id = layer.load(0);
			let data = await planApi.getPlanList({
				page:this.plpage.current,
				limit:this.plpage.limit
			});
			this.plpage = data.plans.page
			this.plans = data.plans.data;
			layer.close(id);
		},
		planCreateOrder:async function(pmid){
			const id = layer.load(0);
			const data = await planApi.planCreateOrder({
				pmid:pmid
			});
			console.log(data);
			this.$router.push('/home/planpay/' + data.ordersn);
			layer.close(id);
		},
		addPlanSignup:async function(){
			await plan.signUpPlan({
				signup:this.model,
				planid:this.enrollplanid
			});
			this.getData();
		},
		showPageData:function(member){
			this.member = member;
			this.showPage = true;		
		},
		showPlanInfo:async function(pl){
			this.tabCurrent2 = ref('11');
			const data = await plan.getPlan({
				planid:pl.planid	
			})
			this.lessons = data.lessons;
			this.basics = data.basics;
			this.certs = data.certs;
			this.plan = pl;
			this.showPlanInfoPage = true;
		},
		showEnroll:function(planid){
			console.log(this.user);
			this.model = ref({})
			this.model.mphoto = this.user.userphoto??'';
			this.model.mname = this.user.usertruename??'';
			this.model.mpassport = this.user.userpassport;
			this.enrollplanid = planid;
			this.showEnrollPage = true;	
		},
		pagechange:function({current,limit}){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		toplandetail:function(planid){
			this.$router.push('/home/plandetail/' + planid)
		}
	},
	watch:{
		'tabCurrent':function(){
			this.getData();
		}
	}
}
</script>
<style scoped>
.tabtitle{
	font-size: 16px;;
	padding-left:20px;
	padding-right: 20px;
}
.lessoncard{
	line-height:30px;
	padding: 25px;
	border-radius: 10px;
	cursor: pointer;
	box-sizing: border-box;
	border:1px solid #EEEEEE;
}
.lessoncard h4{
	font-size:16px;
	line-height:40px;
	margin-top: 10px;
	font-weight: 400;
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;  
}
.progress{
	padding:20px 10px;
}
::v-deep(.progress > .lay-progress-circle > .layui-progress-text){
	font-size: 16px;
}
.tableheader{
	font-size: 16px;
}
.lessontitle{
	font-size: 16px;
	font-weight: 400;
}
.lessondesc{
	font-size: 12px;
	color: #666666;	
}
.planbox{
	width: 93%;
	background-color: #FFFFFF;
	border-radius: 5px;
	margin-top: 10px;
	padding:15px;
}
.planbox img{
	width: 100%;
	border-radius: 5px;
}
.planbox h3,.planbox p{
	line-height: 35px;
}
.planbox .desc{
	line-height: 25px;
	color:#666666;
}
.layui-loading-spinning{
	background-color:unset;
}
.planbox .title{
	color:#666666;
	font-size:16px;
	line-height: 2;
	margin-bottom: 10px;
	margin-top: 10px;
	font-weight: 800;
}

.planbox .desc{
	color: #999999;
	text-indent: 10px;
	line-height: 2;
}
</style>