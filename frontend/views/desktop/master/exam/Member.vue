<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 通行证ID</span><lay-input v-model="search.passport" allow-clear style="width: 180px;"></lay-input>
			</lay-space>
			<lay-space>
				<span style='width:70px'> 用户名</span><lay-input v-model="search.username" allow-clear style="width: 180px;"></lay-input>
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
					<lay-button size="sm" type="danger" @click="refreshNumber()">更新人员数量</lay-button>
				</span>
			</template>
			<template #footer>
                <lay-button :disabled="selectedKeys.length < 1" size="sm" type="primary" @click="verifyMember()">移除成员</lay-button>
				<lay-page v-model="page.current"  v-model:limit="page.limit" :layout="layout" :total="page.total"  style="float:right;" @change="changePage"></lay-page>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="showPageData(row)">档案</lay-button>
				<lay-button size="xs" type="danger" @click="delExamMember(row.emid)">移出</lay-button>
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
					<lay-button size="sm" type="primary" @click="getUsersData">搜索</lay-button>
				</lay-space>
			</lay-space>

			<lay-table id="mpassport" ref="utableRef" v-model:selectedKeys="memberSelectedKeys" :columns="memberColumns" :data-source="usersSource">
				<template #footer>
					<lay-row>
						<lay-col md="12">
                            <lay-space>
                                <lay-date-picker v-model="endTime" placeholder="选择到期时间" allow-clear></lay-date-picker>
                                <lay-button size="sm" type="primary" @click="addExamMember">开通课程</lay-button>
                            </lay-space>
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
                        <th>通行证ID</th>
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
                        <tbody>
                        <tr>
                            <th colspan="7">证件信息</th>
						</tr>
						<tr>
							<td colspan="7">
								<img :src="member.mpassportimg" style="margin:5px;">
							</td>
						</tr>
						<tr>
						    <th colspan="7">工作经历</th>
						</tr>
                            <tr>
                                <td class="text-left" colspan="7" v-html="member.mworktext"></td>
                            </tr>
                            <tr>
                                <th colspan="7">其他有关情况陈述</th>
                            </tr>
                            <tr>
                                <td class="text-left" colspan="7" v-html="member.mtext"></td>
                            </tr>
                        </tbody>
					</table>
				</lay-tab-item>
			</lay-tab>
		</div>
	</lay-layer>
</template>
<script>
import examApi from '@/framework/api/admin/exam.js';
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
				key:'emid',
				width:'20px'
			},{
				title:'姓名',
				key:'mname',
				width:'150px'
			},{
				title:'通行证ID',
				key:'empassport'
			},{
                title:'到期时间',
                key:'emendtime',
                width:'120px'
            },{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"100px"
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
				title:'通行证ID',
				key:'mpassport',
				width:'240px'
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
			search:{},
			memberSearch:{},
			basicId:0,
            basic:{},
			model:{},
			modify:{},
			member:{},
            endTime:'',
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
		this.basicId = this.$route.params.basicid;
		await this.getData()
	},
	components:{
		myUploadFile,
		myThumb
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await examApi.getMemberList({
                    basicId:this.basicId,
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
				this.memberPage.current = data.page;
				this.memberPage.limit = data.limit;
				this.memberPage.total = data.total;
				this.usersSource = data.data;
			}, null, null);
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
		addExamMember:async function(){
            await this.base(async () => {
                this.showAddPage = false;
                await examApi.addMemberByPassport({
                    basicId:this.basicId,
                    mids:this.memberSelectedKeys,
                    endTime:this.endTime
                });
            });
		},
		delExamMember:function(emid){
            this.confirmDelete(async () => {
                await examApi.deleteMember([emid]);
            },this.getData);
		},
		showPageData:async function(row){
			this.member = await memberApi.getMember({passport:row.empassport});
			this.showPage = true
		},
        refreshNumber:function(){
            this.confirmOperate('确定要刷新吗？',async () => {
                await examApi.refreshNumber(this.basicId);
            });
		},
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