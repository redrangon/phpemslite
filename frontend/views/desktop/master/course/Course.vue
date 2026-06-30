<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 关键字：</span><lay-input v-model="search.keyword" allow-clear style="width: 180px;"</lay-input>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card class="pagecontent">
		<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" v-model:selectedKeys="selectedKeys" :data-source="dataSource" id="courseid">
			<template #toolbar>
				<lay-button type="primary" size="sm" @click="showAddPage()">添加课件</lay-button>
				<lay-button size="sm" type="primary" @click="showStruct()" style="float:right;">课件结构图</lay-button>
				<lay-button size="sm" type="primary" @click="upLevel()" style="float:right;" v-if="dirid > 0">向上一级</lay-button>
			</template>
			<template #coursesequence="{ row }">
				<lay-input v-model="row.coursesequence" @change="modifySequence(row)"/>
			</template>
			<template #coursethumb="{ row }">
				<img :src="row.coursethumb?row.coursethumb:'/src/assets/images/noimages.png'" style="height: 40px;"/>
			</template>
			<template #coursemodule="{ row }">
				<span v-if="row.coursemodule === 'pdf'">PDF课件</span>
				<span v-else-if="row.coursemodule === 'video'">视频课件</span>
				<span v-else-if="row.coursemodule === 'html'">文本课件</span>
				<span v-else-if="row.coursemodule === 'md'">MarkDown</span>
				<span v-else>文件夹</span>
			</template>
			<template #footer>
				<lay-row>
					<lay-col md="12">
						<lay-button type="danger" size="sm" @click="delCourse()" :disabled="selectedKeys.length < 1">删除选中数据</lay-button>
					</lay-col>
					<lay-col md="12">
						<lay-page v-model="page.current" :layout="layout" v-model:limit="page.limit" :total="page.total" @change="changePage" style="float:right;"></lay-page>
					</lay-col>
				</lay-row>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="courseDir(row.courseid)" v-if="row.coursemodule === 'dir'">课件</lay-button>
				<lay-button size="xs" type="primary" @click="showMoveDir(row)">移动</lay-button>
				<lay-button size="xs" type="primary" @click="modifyPage(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delCourse(row.courseid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showMoveDirPage" :area="['480px','210px']" :btn="showMoveDirPageBtn" title="移动课件">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="targetData" :pane="false" size="md" :labelWidth="120" class="form" ref="moveDirPageFrom">
				<lay-form-item label="目标文件夹" prop="coursedirid">
					<lay-tree-select v-model="targetData.coursedirid" :data="moveData" :replaceFields="{id:'courseid',title:'coursetitle',children:'children'}"></lay-tree-select>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showStructPage" :area="['800px','60vh']" title="课件结构图">
		<div style="padding: 20px 50px 20px 20px;">
			<courseTree :data="treeData"></courseTree>
		</div>
	</lay-layer>
	<lay-layer v-model="showCoursePage" :area="['960px']" :btn="showCoursePageBtn" title="添加课件">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="model" :pane="false" size="md" :labelWidth="100" class="form" ref="addPageFrom">
				<lay-form-item label="课件类型" prop="coursemodule" required error-message="请选择课件类型">
					<lay-radio-button v-model="model.coursemodule" name="coursemodule" value="dir">文件夹</lay-radio-button>
					<lay-radio-button v-model="model.coursemodule" name="coursemodule" value="html">文本课程</lay-radio-button>
					<lay-radio-button v-model="model.coursemodule" name="coursemodule" value="video">视频课程</lay-radio-button>
					<lay-radio-button v-model="model.coursemodule" name="coursemodule" value="pdf">PDF课程</lay-radio-button>
					<lay-radio-button v-model="model.coursemodule" name="coursemodule" value="md">MarkDown</lay-radio-button>
				</lay-form-item>
				<lay-form-item label="课件名称" prop="coursetitle" required>
					<lay-input v-model="model.coursetitle" placeholder="请输入课件名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="coursethumb">
					<myThumb v-model:src="model.coursethumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="课件上传" prop="coursepath" required v-if="model.coursemodule === 'video'">
					<myUploadFile v-model="model.coursepath" filetype=".mp4" style="width:480px"></myUploadFile>
				</lay-form-item>
				<lay-form-item label="课件上传" prop="coursepath" required v-if="model.coursemodule === 'pdf'">
					<myUploadFile v-model="model.coursepath" filetype=".pdf" style="width:480px"></myUploadFile>
				</lay-form-item>
				<lay-form-item label="课件内容" prop="coursedescribe" required v-if="model.coursemodule === 'md'">
					<MDEditor v-model:content="model.coursedescribe"></MDEditor>
				</lay-form-item>
				<lay-form-item label="课件内容" prop="coursedescribe" required v-else>
					<myEditor v-model:content="model.coursedescribe"></myEditor>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyCoursePage" :area="['960px']" :btn="modifyCoursePageBtn" title="修改课件">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modelModify" :pane="false" size="md" :labelWidth="100" class="form" ref="modifyPageFrom">
				<lay-form-item label="课件类型" prop="coursemodule" required error-message="请选择课件类型">
					<lay-radio-button v-model="modelModify.coursemodule" name="coursemodule" value="dir">文件夹</lay-radio-button>
					<lay-radio-button v-model="modelModify.coursemodule" name="coursemodule" value="html">文本课程</lay-radio-button>
					<lay-radio-button v-model="modelModify.coursemodule" name="coursemodule" value="video">视频课程</lay-radio-button>
					<lay-radio-button v-model="modelModify.coursemodule" name="coursemodule" value="pdf">PDF课程</lay-radio-button>
					<lay-radio-button v-model="modelModify.coursemodule" name="coursemodule" value="md">MarkDown</lay-radio-button>
				</lay-form-item>
				<lay-form-item label="课件名称" prop="coursetitle" required>
					<lay-input v-model="modelModify.coursetitle" placeholder="请输入课件名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="coursethumb">
					<myThumb v-model:src="modelModify.coursethumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="课件上传" prop="coursepath" required v-if="modelModify.coursemodule === 'video'">
					<myUploadFile v-model="modelModify.coursepath" filetype=".mp4" style="width:480px"></myUploadFile>
				</lay-form-item>
				<lay-form-item label="课件上传" prop="coursepath" required v-if="modelModify.coursemodule === 'pdf'">
					<myUploadFile v-model="modelModify.coursepath" filetype=".pdf" style="width:480px"></myUploadFile>
				</lay-form-item>
				<lay-form-item label="课件内容" prop="coursedescribe" required v-if="modelModify.coursemodule === 'md'">
					<MDEditor v-model:content="modelModify.coursedescribe"></MDEditor>
				</lay-form-item>
				<lay-form-item label="课件内容" prop="coursedescribe" required v-else>
					<myEditor v-model:content="modelModify.coursedescribe"></myEditor>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import courseApi from '@/framework/api/admin/course.js';
import myThumb from '@/components/desktop/Thumb.vue';
import myUploadFile from '@/components/desktop/UploadFile.vue';
import myEditor from '@/components/master/Editor.vue';
import MDEditor from "@/components/master/MDEditor.vue";
import courseTree from '@/components/master/CourseTree.vue';
import {withConfirm, withLayer} from "@/framework/utils/decorators.js";
import baseMixin from "@/framework/mixins/baseMixin.js";

export default {
	mixins: [baseMixin],
	data() {
		return {
			csid:0,
			uplevelid:0,
			moveDirId:0,
			columns:[{
				title:'复选',
				type: "checkbox",
				width:'80px',
				fixed: "left"
			},{
				title:'排序',
				customSlot: "coursesequence",
				key:'coursesequence',
				width:'80px'
			},{
				title:'ID',
				key:'courseid',
				width:'20px'
			},{
				title:'缩略图',
				customSlot: "coursethumb",
				key:'coursethumb',
				width:'80px'
			},{
				title:'课程名称',
				key:'coursetitle'
			},{
				title:'课件类型',
				key:'coursemodule',
				customSlot:'coursemodule',
				width:'120px'
			},{
				title:'发布时间',
				key:'courseinputtime',
				width:'200px'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"200px"
			}],
			dataSource:[],
			treeData:[],
			moveData:[],
			targetData:{},
			dirid:0,
			sequence:{},
			selectedKeys:[],
			current:1,
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{ current: 1, limit: 10, total: 0 },
			showMoveDirPage:false,
			showCoursePage:false,
			showStructPage:false,
			showModifyCoursePage:false,
			search:{},
			model:{},
			modelModify:{},
			showCoursePageBtn:[
				{
					text: "确认",
					callback: () => {
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showCoursePage = false;
							this.addCourse();
						}).catch( res => {
							console.log(res);
						});
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showCoursePage = false;
					}
				}
			],
			modifyCoursePageBtn:[
				{
					text: "确认",
					callback: () => {
						this.$refs['modifyPageFrom'].validate().then((res) => {
							this.showModifyCoursePage = false;
							this.modifyCourse();
						}).catch( res => {
							console.log(res);
						});
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showModifyCoursePage = false;
					}
				}
			],
			showMoveDirPageBtn:[
				{
					text: "确认",
					callback: () => {
						this.$refs['moveDirPageFrom'].validate().then((res) => {
							this.showMoveDirPage = false;
							this.moveCourse();
						}).catch( res => {
							console.log(res);
						});
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showMoveDirPage = false;
					}
				}
			]
		}
	},
	async mounted() {
		this.csid = this.$route.params.csid;
		this.dirid = this.$route.params.dirid;
		await this.getData();
	},
	components:{
		myThumb,
		myUploadFile,
		myEditor,
		MDEditor,
		courseTree
	},
	watch:{
		dirid:async function(value){
			if(value > 0)
			{
				const data = await courseApi.getCourse(value);
				this.uplevelid = data?.coursedirid??0
			}
			await this.getData();
		}
	},
	methods:{
		base:async function(fn){
			await withLayer(fn,	null,this.getData);
		},
		getData:async function(){
			await withLayer(
					async () => {
						const data = await courseApi.getCourseList({
							csid:this.csid,
							search:this.search,
							dirid:this.dirid??0,
							limit:this.page.limit,
							page:this.page.current
						})
						this.page.page = data.page;
						this.page.total = data.total;
						this.page.limit = data.limit;
						this.dataSource = data.data;
					},[null,null]
			);
		},
		getAllCourse:async function(){
			const data = await courseApi.getAllCourse(this.csid);
			return courseApi.buildCourseTree(data);
		},
		showStruct:async function(){
			this.treeData = await this.getAllCourse();
			this.showStructPage = true;
		},
		showAddPage:function(){
			this.showCoursePage = true;
			this.model = {
				coursemodule:'dir',
				coursecsid:this.csid,
				coursedirid:this.dirid??0
			};
		},
		modifyPage:function(row){
			this.modelModify = {...row};
			this.showModifyCoursePage = true;
		},
		showMoveDir:async function(row){
			this.showMoveDirPage = true;
			this.targetData = {
				courseid:row.courseid,
				coursedirid:row.coursedirid,
			};
			const data = await this.getAllCourse();
			this.moveData = [
				{
					courseid:0,
					coursetitle:'根目录',
					children:data
				}
			];
		},
		addCourse:function(){
			this.base(async () => {
				await courseApi.addCourse(this.model);
			});
		},
		modifyCourse:function(){
			this.base(async () => {
				await courseApi.modifyCourse(this.modelModify);
			});
		},
		moveCourse:function(){
			this.base(async () => {
				await courseApi.modifyCourse(this.targetData);
			});
		},
		modifySequence:function(row){
			this.base(async () => {
				await courseApi.modifyCourse({
					courseid:row.courseid,
					coursesequence:row.coursesequence
				});
			});
		},
		delCourse:function(id){
			withConfirm('确定要删除吗？', async () => {
				await courseApi.delCourse(id?[id]:this.selectedKeys);
			},this.getData)
		},
		changePage:function({current,limit}){
			this.page.current = current
			this.page.limit = limit
			this.getData();
		},
		courseDir:function(courseId){
			this.$router.push({ path: '/desktop/master/course/course/'+this.csid + '/' + courseId });
			this.dirid = courseId;
		},
		upLevel:function(){
			if(this.uplevelid > 0)this.$router.push({ path: '/desktop/master/course/course/'+this.csid + '/' + this.uplevelid  });
			else this.$router.push({ path: '/desktop/master/course/course/'+this.csid  });
			this.dirid = this.uplevelid??0;
		}
	}
}
</script>