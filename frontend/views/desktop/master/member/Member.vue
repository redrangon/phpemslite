<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 姓名：</span><lay-input v-model="search.mname" allow-clear style="width:180px;"></lay-input>
			</lay-space>
			<lay-space>
				<span style='width:100px'> 通行证ID：</span><lay-input v-model="search.mpassport" allow-clear style="width:180px;"></lay-input>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-table id="mid" ref="tableRef" v-model:selected-keys="selectedKeys" :columns="columns" :data-source="dataSource" :default-toolbar="false" even>
			<template #toolbar>
				档案管理
                <span style="float:right;">
                    <lay-space>
                        <lay-button size="sm" type="primary" @click="showImportPage = true">批量导入</lay-button>
                        <lay-button size="sm" type="primary" @click="showAddPage = true">添加档案</lay-button>
                        <a href="public/attach/member.xlsx">
                            <lay-button size="sm" type="primary">模板下载</lay-button>
                        </a>
                    </lay-space>
                </span>
			</template>
			<template #footer>
				<lay-button :disabled="selectedKeys.length < 1" size="sm" type="primary" @click="">导出选中档案</lay-button>
				<lay-button :disabled="selectedKeys.length < 1" size="sm" type="danger" @click="delMember()">删除选中档案</lay-button>
				<lay-page v-model="page.current" v-model:limit="page.limit" :layout="layout" :total="page.total"  style="float:right;" @change="changePage"></lay-page>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="showPageData(row)">查看</lay-button>
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delMember(row.mid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :area="['960px','90%']" :btn="showAddPageBtns" title="添加档案">
		<div style="padding: 20px;">
			<lay-form ref="addPageForm" :labelWidth="140" :model="model" :pane="false" class="form" size="md">
				<lay-form-item label="照片" prop="mphoto" required>
					<myThumb v-model:src="model.mphoto" style="width:90px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="姓名" prop="mname" required>
					<lay-input v-model="model.mname" placeholder="请输入姓名"></lay-input>
				</lay-form-item>
				<lay-form-item label="出生日期" prop="mbirthday" required>
					<lay-date-picker v-model="model.mbirthday"></lay-date-picker>
				</lay-form-item>
				<lay-form-item label="性别" prop="msex" required>
					<lay-radio v-model="model.msex" label="男" name="msex" value="男"></lay-radio>
					<lay-radio v-model="model.msex" label="女" name="msex" value="女"></lay-radio>
				</lay-form-item>
				<lay-form-item label="通行证ID" prop="mpassport" required>
					<lay-input v-model="model.mpassport" placeholder="请输入通行证ID"></lay-input>
				</lay-form-item>
				<lay-form-item label="电话" prop="mphone" required>
					<lay-input v-model="model.mphone" placeholder="请输入电话"></lay-input>
				</lay-form-item>
				<lay-form-item label="地址" prop="maddress" required>
					<lay-input v-model="model.maddress" placeholder="请输入地址"></lay-input>
				</lay-form-item>
				<lay-form-item label="文化程度" prop="medu" required>
					<lay-select v-model="model.medu" placeholder="请选择文化程度">
						<lay-select-option label="初中" value="初中"></lay-select-option>
						<lay-select-option label="高中" value="高中"></lay-select-option>
						<lay-select-option label="大专" value="大专"></lay-select-option>
						<lay-select-option label="本科" value="本科"></lay-select-option>
						<lay-select-option label="研究生" value="研究生"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="单位全称" prop="munit" required>
					<lay-input v-model="model.munit" placeholder="请输入单位全称"></lay-input>
				</lay-form-item>
				<lay-form-item label="公司名称" prop="mcompany" required>
					<lay-input v-model="model.mcompany" placeholder="请输入公司名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="部门名称" prop="mteam" required>
					<lay-input v-model="model.mteam" placeholder="请输入部门名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="职务" prop="mjob" required>
					<lay-input v-model="model.mjob" placeholder="请输入职务"></lay-input>
				</lay-form-item>
				<lay-form-item label="职称" prop="mjobtitle" required>
					<lay-input v-model="model.mjobtitle" placeholder="请输入职称或技能等级"></lay-input>
				</lay-form-item>
				<lay-form-item label="政治面貌" prop="mpolitic" required>
					<lay-select v-model="model.mpolitic" placeholder="请选择政治面貌">
						<lay-select-option label="团员" value="团员"></lay-select-option>
						<lay-select-option label="党员" value="党员"></lay-select-option>
						<lay-select-option label="无党派人士" value="无党派人士"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="身份证照片" prop="mpassportimg" required>
					<myThumb v-model:src="model.mpassportimg" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="参加工作年份" prop="mjobtime" required>
					<lay-date-picker v-model="model.mjobtime" type="year"></lay-date-picker>
				</lay-form-item>
				<lay-form-item label="工作经历" prop="mresume">
					<myEditor v-model:content="model.mresume"></myEditor>
				</lay-form-item>
				<lay-form-item label="其他有关情况陈述" prop="mtext">
					<myEditor v-model:content="model.mtext"></myEditor>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :area="['960px','90%']" :btn="showModifyPageBtns" title="编辑档案">
		<div style="padding: 20px;">
			<lay-form ref="modifyPageForm" :labelWidth="140" :model="modelModify" :pane="false" class="form" size="md">
				<lay-form-item label="照片" prop="mphoto" required>
					<myThumb v-model:src="modelModify.mphoto" style="width:90px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="姓名" prop="mname" required>
					<lay-input v-model="modelModify.mname" placeholder="请输入姓名"></lay-input>
				</lay-form-item>
				<lay-form-item label="出生日期" prop="mbirthday" required>
					<lay-date-picker v-model="modelModify.mbirthday"></lay-date-picker>
				</lay-form-item>
				<lay-form-item label="性别" prop="msex" required>
					<lay-radio v-model="modelModify.msex" label="男" name="msex" value="男"></lay-radio>
					<lay-radio v-model="modelModify.msex" label="女" name="msex" value="女"></lay-radio>
				</lay-form-item>
				<lay-form-item label="通行证ID" prop="mpassport" required>
					<lay-input v-model="modelModify.mpassport"></lay-input>
				</lay-form-item>
				<lay-form-item label="电话" prop="mphone" required>
					<lay-input v-model="modelModify.mphone" placeholder="请输入电话"></lay-input>
				</lay-form-item>
				<lay-form-item label="地址" prop="maddress" required>
					<lay-input v-model="modelModify.maddress" placeholder="请输入地址"></lay-input>
				</lay-form-item>
				<lay-form-item label="文化程度" prop="medu" required>
					<lay-select v-model="modelModify.medu" placeholder="请选择文化程度">
						<lay-select-option label="初中" value="初中"></lay-select-option>
						<lay-select-option label="高中" value="高中"></lay-select-option>
						<lay-select-option label="大专" value="大专"></lay-select-option>
						<lay-select-option label="本科" value="本科"></lay-select-option>
						<lay-select-option label="研究生" value="研究生"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="单位" prop="munit" required>
					<lay-input v-model="modelModify.munit" placeholder="请输入单位全称"></lay-input>
				</lay-form-item>
				<lay-form-item label="公司名称" prop="mcompany" required>
					<lay-input v-model="modelModify.mcompany" placeholder="请输入公司名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="部门名称" prop="mteam" required>
					<lay-input v-model="modelModify.mteam" placeholder="请输入部门名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="职务" prop="mjob" required>
					<lay-input v-model="modelModify.mjob" placeholder="请输入职务"></lay-input>
				</lay-form-item>
				<lay-form-item label="职称" prop="mjobtitle" required>
					<lay-input v-model="modelModify.mjobtitle" placeholder="请输入职称或技能等级"></lay-input>
				</lay-form-item>
				<lay-form-item label="政治面貌" prop="mpolitic" required>
					<lay-select v-model="modelModify.mpolitic" placeholder="请选择政治面貌">
						<lay-select-option label="团员" value="团员"></lay-select-option>
						<lay-select-option label="党员" value="党员"></lay-select-option>
						<lay-select-option label="无党派人士" value="无党派人士"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="身份证照片" prop="mpassportimg" required>
					<myThumb v-model:src="modelModify.mpassportimg" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="参加工作年份" prop="mjobtime" required>
					<lay-date-picker v-model="modelModify.mjobtime" type="year"></lay-date-picker>
				</lay-form-item>
				<lay-form-item label="工作经历" prop="mresume">
					<myEditor v-model:content="modelModify.mresume"></myEditor>
				</lay-form-item>
				<lay-form-item label="其他有关情况陈述" prop="mtext">
					<myEditor v-model:content="modelModify.mtext"></myEditor>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showPage" :area="['900px','90%']" :btn="showPageBtns" title="档案详情">
		<div style="padding: 20px;">
			<table class="table">
				<thead>
					<tr><td colspan="7">学员登记表</td></tr>
				</thead>
				<tbody>
                    <tr>
                        <th>职称</th>
                        <td colspan="3">{{ show.mjobtitle }}</td>
                        <th>职务</th>
                        <td>{{ show.mjob }}</td>
                        <td rowspan="4"><img :src="show.mphoto" width="90"></td>
                    </tr>
                    <tr>
                        <th>姓名</th>
                        <td>{{ show.mname }}</td>
                        <th>性别</th>
                        <td>{{ show.msex }}</td>
                        <th>通行证ID</th>
                        <td>{{ show.mpassport }}</td>
                    </tr>
                    <tr>
                        <th>出生年月</th>
                        <td>{{ show.mbirthday }}</td>
                        <th>参加工作年份</th>
                        <td>{{ show.mjobtime }}</td>
                        <th>文化程度</th>
                        <td>{{ show.medu }}</td>
                    </tr>
                    <tr>
                        <th>政治面貌</th>
                        <td>{{ show.mpolitic }}</td>
                        <th>部门名称</th>
                        <td>{{ show.mteam }}</td>
                        <th>参加工作年份</th>
                        <td>{{ show.mjobtime }}年</td>
                    </tr>
                    <tr>
                        <th>手机号</th>
                        <td colspan="2">{{ show.mphone }}</td>
                        <th>地址</th>
                        <td colspan="3">{{ show.maddress }}</td>
                    </tr>
                    <tr>
                        <th colspan="3">单位名称</th>
                        <td colspan="4">{{ show.munit }}</td>
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
								<img :src="show.mpassportimga" style="margin:5px;max-width: 96%">
							</td>
						</tr>
						<tr>
							<th colspan="7">工作经历</th>
						</tr>
						<tr>
							<td class="text-left" colspan="7" v-html="show.mworktext"></td>
						</tr>
						<tr>
							<th colspan="7">其他有关情况陈述</th>
						</tr>
						<tr>
							<td class="text-left" colspan="7" v-html="show.mtext"></td>
						</tr>
                        </tbody>
					</table>
				</lay-tab-item>
			</lay-tab>
		</div>
	</lay-layer>
	<lay-layer v-model="showImportPage" :area="['700px']" :btn="showImportPageBtns" title="批量导入">
		<div style="padding: 20px;">
			<lay-form ref="importPageForm" :labelWidth="100" :model="file" :pane="false" class="form" size="md">
				<lay-form-item label="档案信息" prop="member" required>
					<myUploadFile v-model="file.member" filetype=".xlsx" placeholder="上传档案信息表"></myUploadFile>
				</lay-form-item>
				<lay-form-item label="档案附件" prop="zip">
					<myUploadFile v-model="file.zip" filetype=".zip" placeholder="上传档案附件压缩包"></myUploadFile>
				</lay-form-item>
			</lay-form>
			<lay-field title="上传说明">
				<p>档案信息表为.xlsx格式；档案附件支持.zip压缩包格式；<a href="public/attach/member.xlsx" style="color:#16baaa">下载导入模板</a></p>
				<p>档案档案附件文件名称格式为通行证ID+类型代码：如：620123199107010011zp；</p>
				<p>类型代码说明：</p>
				<p>身份证：仅通行证ID即可，无类型代码；头像：通行证ID+zp；</p>
			</lay-field>
		</div>
	</lay-layer>
</template>
<script>
import memberApi from '@/framework/api/admin/member.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import myThumb from '@/components/desktop/Thumb.vue';
import myUploadFile from '@/components/desktop/UploadFile.vue';
import myEditor from '@/components/master/Editor.vue';
export default {
	mixins:[baseMixin],
	data() {
		return {
			columns:[{
				title:"选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			},{
				title:'ID',
				key:'mid',
				width:'50px'
			},{
				title:'姓名',
				key:'mname',
				width:"150px"
			},{
				title:'通行证号',
				key:'mpassport',
				width:"240px"
			},{
				title:'性别',
				key:'msex',
				width:"50px"
			},{
				title:'学历',
				key:'medu',
				width:"260px"
			},{
				title:'单位',
				key:'munit'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"150px"
			}],
			dataSource:[],
			search:{},
			selectedKeys:[],
			page:{ current: 1, limit: 20, total: 0 },
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			model:{},
			modelModify:{},
			file:{},
			show:{},
			tplFile:'',
			showAddPage:false,
			showModifyPage:false,
			showImportPage:false,
			showPage:false,
			showPageBtns:ref([
				{
					text: "关闭",
					callback: () => {
						this.showPage = false;
					}
				}
			]),
			showAddPageBtns:ref([
				{
					text: "提交",
					callback: () => {
						this.$refs['addPageForm'].validate().then((res) => {
							this.showAddPage = false;
							this.addMember();					
						}).catch( res => {
							console.log(res);
						});	
					}
				},
				{
					text: "关闭",
					callback: () => {
						this.showAddPage = false;
					}
				}
			]),
			showModifyPageBtns:[
				{
					text: "提交",
					callback: () => {
						this.$refs['modifyPageForm'].validate().then((res) => {
							this.showModifyPage = false;
							this.modifyMember();
						}).catch( res => {
							console.log(res);
						});
					}
				},
				{
					text: "关闭",
					callback: () => {
						this.showModifyPage = false;
					}
				}
			],
			showImportPageBtns:[
				{
					text: "提交",
					callback: () => {
						this.$refs['importPageForm'].validate().then((res) => {
							this.showImportPage = false;
							this.importMember();
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
			]
		}
	},
	components:{
		myThumb,
		myUploadFile,
		myEditor
	},
	async mounted() {
		await this.getData();
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await memberApi.getMemberList({
					search:this.search,
					page:this.page.current,
					limit:this.page.limit
				});
				this.page = {
					current: data.page,
					limit: data.limit,
					total: data.total
				};
				this.dataSource = data.data;
			}, null, null);
		},
		delMember:function(id){
			this.confirmDelete(async () => {
				await memberApi.delMember(id?[id]:this.selectedKeys);
			}, this.getData);
		},
		addMember:async function(){
			await this.base(async () => {
				await memberApi.addMember(this.model)
				this.showAddPage = false;
			}, '添加成功');
		},
		modifyMember:async function(){
			await this.base(async () => {
				await memberApi.modifyMember(this.modelModify)
				this.showModifyPage = false;
			}, '修改成功');
		},
		showModify:function(row){
			this.modelModify = {...row};
			this.showModifyPage = true
		},
		showPageData:function(row){
			this.show = {...row};
			this.search.passport = this.show.mpassport;
			this.searchType = 'files';
			this.showPage = true
		},
		importMember:async function(){
			await this.base(async () => {
				await memberApi.importMember({
					file:this.file
				})
				this.showImportPage = false;
			}, '导入成功');
		},
		changePage:async function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
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
	.table .text-left{
		text-align: left;
	}
	.table .left{
		text-align: left;
	}
</style>