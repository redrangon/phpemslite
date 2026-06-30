<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 身份证号</span><lay-input v-model="search.pmpassport" allow-clear style="width: 180px;"></lay-input>
			</lay-space>
			<lay-space>
				<span style='width:70px'> 姓名</span><lay-input v-model="search.pmname" allow-clear style="width: 180px;"></lay-input>
			</lay-space>
			<lay-space>
				<span style='width:70px'> 单位</span><lay-input v-model="search.pmunit" allow-clear style="width: 180px;"></lay-input>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-table id="pmid" ref="tableRef" v-model:selected-keys="selectedKeys" :columns="columns" :data-source="dataSource" :default-toolbar="false">
			<template #toolbar>
				<lay-button size="sm" type="primary" @click="userAddPage">添加人员</lay-button>
				<span style="float:right;">
					<lay-button size="sm" type="primary" @click="showImportPage = true">导入人员信息</lay-button>
					<lay-button size="sm" type="danger" @click="statsPlanMember()">更新训练状态</lay-button>
				</span>
			</template>
			<template v-slot:pmprogress="{row}">
				<span v-if="row.pmprogress === 1">已完成</span>
				<span v-else>训练中</span>
			</template>
			<template v-slot:pmresult="{row}">
				<span v-if="row.pmresult === 1">已通过</span>
				<span v-else>训练中</span>
			</template>
			<template v-slot:pmstatus="{row}">
				<span v-if="row.pmstatus === 1">已完成</span>
				<span v-else>训练中</span>
			</template>
			<template v-slot:pmverify="{row}">
				<span v-if="row.pmverify === 1" style="color:#16baaa">已审核</span>
				<span v-else style="color:#FFB800">待审核</span>
			</template>
			<template v-slot:pmpayment="{row}">
				<span v-if="row.pmpayment === 1" style="color:#16baaa">已缴费</span>
				<span v-else style="color:#FFB800">待缴费</span>
			</template>
			<template #footer>
				<lay-button :disabled="selectedKeys.length < 1" size="sm" type="primary" @click="verifyMember()">通过审核</lay-button>
				<lay-button :disabled="selectedKeys.length < 1" size="sm" type="primary" @click="paymentMember()">已缴费</lay-button>
				<lay-page v-model="page.current"  v-model:limit="page.limit" :layout="layout" :total="page.total"  style="float:right;" @change="changePage"></lay-page>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="showPageData(row)">档案</lay-button>
				<lay-button size="xs" type="primary" @click="planCourse(row.pmpassport)">进度</lay-button>
				<lay-button size="xs" type="primary" @click="planExam(row.pmpassport)">成绩</lay-button>
				<lay-button size="xs" type="danger" @click="delPlanMember(row.pmid)">移出</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :area="['840px','80%']" title="添加人员">
		<lay-card>
			<lay-space size="lg" style="margin:10px auto">
				<lay-space></lay-space>
				<lay-space>
					<span style='width:70px'> 身份证</span><lay-input v-model="memberSearch.mpassport" allow-clear size="sm" style="width: 140px;"></lay-input>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 姓名</span><lay-input v-model="memberSearch.mname" allow-clear size="sm" style="width: 140px;"></lay-input>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 单位</span><lay-input v-model="memberSearch.munit" allow-clear style="width: 140px;"></lay-input>
				</lay-space>
				<lay-space>
					<lay-button size="sm" type="primary" @click="getUsersData">搜索</lay-button>
				</lay-space>
			</lay-space>

			<lay-table id="mpassport" ref="utableRef" v-model:selectedKeys="memberSelectedKeys" :columns="memberColumns" :data-source="usersSource">
				<template #footer>
					<lay-row>
						<lay-col md="12">
							<lay-button size="sm" type="primary" @click="addPlanMember">加入计划</lay-button>
						</lay-col>
						<lay-col md="12">
							<lay-page v-model="memberPage.current"  v-model:limit="memberPage.limit" :layout="memberLayout" :total="memberPage.total"  style="float:right;" @change="memberChangePage"></lay-page>
						</lay-col>
					</lay-row>
				</template>
			</lay-table>
		</lay-card>
	</lay-layer>
	<lay-layer v-model="showPage" :area="['900px','90%']" :btn="showPageBtns" title="档案详情">
		<div style="padding: 20px;">
			<table class="table">
				<thead>
                <tr><th colspan="7">学员登记表</th></tr>
				</thead>
                <tbody>
                    <tr>
                        <th>职称</th>
                        <td colspan="3">{{ member.mjobtitle }}</td>
                        <th>职务</th>
                        <td>{{ member.mjob }}</td>
                        <td rowspan="4"><img :src="member.mphoto" width="90"></td>
                    </tr>
                    <tr>
                        <th>姓名</th>
                        <td>{{ member.mname }}</td>
                        <th>性别</th>
                        <td>{{ member.msex }}</td>
                        <th>身份证号</th>
                        <td>{{ member.mpassport }}</td>
                    </tr>
                    <tr>
                        <th>出生年月</th>
                        <td>{{ member.mbirthday }}</td>
                        <th>参加工作年份</th>
                        <td>{{ member.mjobtime }}</td>
                        <th>文化程度</th>
                        <td>{{ member.medu }}</td>
                    </tr>
                    <tr>
                        <th>政治面貌</th>
                        <td>{{ member.mpolitic }}</td>
                        <th>科队名称</th>
                        <td>{{ member.mteam }}</td>
                        <th>参加工作年份</th>
                        <td>{{ member.mjobtime }}年</td>
                    </tr>
                    <tr>
                        <th colspan="3">单位名称</th>
                        <td colspan="4">{{ member.munit }}</td>
                    </tr>
                </tbody>
			</table>
			<lay-tab v-model="searchType" type="brief">
				<lay-tab-item id="files" title="个人经历">
					<table class="table">
						<thead>
                            <tr><th colspan="7">证件信息</th></tr>
						</thead>
                        <tbody>
						<tr>
							<td colspan="7">
								<img :src="member.mpassportimga" style="margin:5px;">
							</td>
						</tr>
						<tr>
						    <th colspan="7">工作经历</th>
						</tr>
                            <tr>
                                <td class="text-left" colspan="7" v-html="member.mworktext"></td>
                            </tr>
                            <thead>
                            <th colspan="7">其他有关情况陈述</th>
                            </thead>
                            <tr>
                                <td class="text-left" colspan="7" v-html="member.mtext"></td>
                            </tr>
                        </tbody>
					</table>
				</lay-tab-item>
			</lay-tab>
		</div>
	</lay-layer>
	<lay-layer v-model="showImportPage" :area="['700px']" :btn="showImportPageBtns" title="批量导入">
		<div style="padding: 20px;">
			<lay-form ref="importPageFrom" :labelWidth="100" :model="file" :pane="false" class="form" size="md">
				<lay-form-item label="档案信息" prop="member" required>
					<myUploadFile v-model="file.member" filetype=".xlsx" placeholder="上传档案信息表"></myUploadFile>
				</lay-form-item>
				<lay-form-item label="档案附件" prop="zip">
					<myUploadFile v-model="file.zip" filetype=".zip" placeholder="上传档案附件压缩包"></myUploadFile>
				</lay-form-item>
			</lay-form>
			<lay-field title="上传说明">
				<p>档案信息表为.xlsx格式；档案附件支持.zip压缩包格式；<a href="" style="color:#16baaa">下载导入模板</a></p>
				<p>档案档案附件文件名称格式为身份证号+类型代码：如：620123199107010011zp；</p>
				<p>类型代码说明：</p>
				<p>头像：身份证号+zp；身份证：仅身份证号；</p>
			</lay-field>
		</div>
	</lay-layer>
</template>
<script>
import planApi from '@/framework/api/admin/plan.js';
import memberApi from '@/framework/api/admin/member.js';
import {layer} from '@layui/layui-vue';
import myUploadFile from '@/components/desktop/UploadFile.vue';
import myThumb from '@/components/desktop/Thumb.vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
	data() {
		return {
			dataSource:[],
			usersSource:[],
			columns:[{
				title:"选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			},{
				title:'ID',
				key:'pmid',
				width:'20px'
			},{
				title:'姓名',
				key:'pmname',
				width:'150px'
			},{
				title:'身份证号',
				key:'pmpassport'
			},{
				title:'课程学习',
				customSlot:'pmprogress',
				key:'pmprogress',
				width:'100px'
			},{
				title:'考试成绩',
				customSlot:'pmresult',
				key:'pmresult',
				width:'100px'
			},{
				title:'训练状态',
				customSlot:'pmstatus',
				key:'pmstatus',
				width:'100px'
			},{
				title:'审核状态',
				customSlot:'pmverify',
				key:'pmverify',
				width:'100px'
			},{
				title:'缴费状态',
				customSlot:'pmpayment',
				key:'pmpayment',
				width:'100px'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"200px"
			}],
			memberColumns:[{
				title:'复选',
				type: "checkbox",
				width:'50px',
				fixed: "left"
			},{
				title:'ID',
				key:'mid',
				width:'50px'
			},{
				title:'姓名',
				key:'mname',
				width:'100px'
			},{
				title:'身份证号',
				key:'mpassport',
				width:'240px'
			},{
				title:'单位',
				key:'munit'
			}],
			searchType: 'files',
			file:{},
			memberSelectedKeys:[],
			selectedKeys:[],
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			memberLayout:['count', 'prev', 'page', 'next'],
			page:{
				limit:20,
				current:1,
				total:0
			},
			memberPage:{
				limit:20,
				current:1,
				total:0
			},
			statsPage:{
				current: 1,
				limit: 200,
				total: 0
			},
			search:{},
			memberSearch:{},
			planId:0,
			plan:{},
			model:{},
			modify:{},
			member:{},
			showAddPage:false,
			showPage:false,
			showMemberPage:false,
			showPageBtns:[
				{
					text: "关闭",
					callback: () => {
						this.showPage = false;
					}
				}
			],
			showImportPage:false,
			showImportPageBtns:[
				{
					text: "提交",
					callback: () => {
						this.$refs['importPageFrom'].validate().then((res) => {
							this.showImportPage = false;
							this.importPlanMember();
						}).catch( res => {
							console.log(res);
						});
					}
				},
				{
					text: "关闭",
					callback: () => {
						this.showImportPage = false;
					}
				}
			],
			memberPageBtns:[
				{
					text: "确认",
					callback: () => {
						console.log(this.model);
						this.$refs['memberPageFrom'].validate().then((res) => {
							this.showMemberPage = false;
							this.modifyMember();
						}).catch( res => {
							console.log(res);
						});
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showMemberPage = false;
					}
				}
			]
		}
	},
	async mounted() {
		this.planId = this.$route.params.planid;
		await this.getData()
	},
	components:{
		myUploadFile,
		myThumb
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await planApi.getMemberList({
					planId:this.planId,
					search:this.search,
					limit:this.page.limit,
					page:this.page.current
				});
				this.dataSource = data.data;
				this.page.current = data.page;
				this.page.limit = data.limit;
				this.page.total = data.total;
			}, null, null);
		},
		getUsersData:async function(){
			await this.execute(async () => {
				const data = await memberApi.getMemberList({
					search:this.memberSearch,
					page:this.memberPage.current,
					limit:this.memberPage.limit
				});
				this.memberPage.current = data.page; // Changed from this.memberPage.page = data.page;
				this.memberPage.limit = data.limit;
				this.memberPage.total = data.total;
				this.usersSource = data.data;
			}, null, null);
		},
		statsPlanMember:async function(){
			await this.base(async () => {
				let status = true;
				let data;
				while(status){
					data = await planApi.statsPlanMember({
						planId:this.planId,
						limit:this.statsPage.limit,
						page:this.statsPage.current
					});
					this.statsPage.current = data.page + 1;
					this.statsPage.toal = data.toal;
					this.statsPage.limit = data.limit;
					status = data.status;
				}
				this.statsPage.current = 1;
			});
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		memberChangePage:function({current}){
			this.memberPage.current = current
			this.getUsersData()
		},
		userAddPage:function(){
			this.getUsersData()
			this.showAddPage = true
		},
		addPlanMember:async function(){
            await this.base(async () => {
                this.showAddPage = false;
                await planApi.addPlanMemberFromMember({
                    planid:this.planId,
                    mids:this.memberSelectedKeys
                });
            });
		},
		delPlanMember:function(pmid){
            this.confirmDelete(async () => {
                await planApi.delPlanMember([pmid]);
            },this.getData);
		},
		verifyMember:function(){
            this.confirmOperate('确定要通过审核吗？',async () => {
                await planApi.verifyPlanMember(this.selectedKeys);
            },this.getData);
		},
		paymentMember:function(){
            this.confirmOperate('确定已经交过费用了吗？',async () => {
                await planApi.payPlanMember(this.selectedKeys);
            },this.getData);
		},
		showPageData:async function(row){
			this.member = await memberApi.getMember({passport:row.pmpassport});
			this.showPage = true
		},
		planCourse:function(passport){
			this.$router.push('/desktop/master/plan/course/'+this.planId+'/'+passport);
		},
		planExam:function(passport){
			this.$router.push('/desktop/master/plan/exam/'+this.planId+'/'+passport);
		},
		importPlanMember:async function(){
			await planApi.importPlanMember(this.planId,this.file);
			await this.getData();
		}
	}
}
</script>
<style scoped>
	.table {
		border-collapse:collapse;
		border:1px solid #aaa;
		width:100%;
		text-align: center;
	}
	.table thead{
		background-color: #fafafa;
		font-weight: bold;
	}
	.table th {
		border:1px solid #ddd;
		padding:10px;
		width:80px;
	}
	.table td {
		padding:10px;
		border:1px solid #ddd;
		min-width:80px;
	}
	.table .left{
		text-align: left;
	}
</style>