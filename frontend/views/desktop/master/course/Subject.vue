<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 关键字：</span>
				<lay-input v-model="search.keyword" allow-clear style="width: 180px;"></lay-input>
			</lay-space>
			<lay-space>
				<span style='width:70px'> 分类：</span>
				<lay-tree-select v-model="search.cscatid" :data="laycats" placeholder="选择分类" allow-clear></lay-tree-select>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card class="pagecontent">
		<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" v-model:selectedKeys="selectedKeys" :data-source="dataSource" id="csid">
			<template #toolbar>
				<lay-button type="primary" size="sm" @click="showAddPage = true">添加课程</lay-button>
			</template>
			<template #cssequence="{ row }">
				<lay-input v-model="row.cssequence" @change="modifySequence(row)"/>
			</template>
			<template #csthumb="{ row }">
				<lay-avatar :src="row.csthumb?row.csthumb:'/src/assets/images/noimages.png'" size="lg"></lay-avatar>
			</template>
			<template #footer>
				<lay-row>
					<lay-col md="12">
						<lay-button type="danger" size="sm" @click="delSubject()" :disabled="selectedKeys.length < 1">删除选中数据</lay-button>
					</lay-col>
					<lay-col md="12">
						<lay-page v-model="page.current" :layout="layout" v-model:limit="page.limit" :total="page.total" @change="changePage" style="float:right;"></lay-page>
					</lay-col>
				</lay-row>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="normal" @click="showPrice(row.csid)">价格</lay-button>
                <lay-button size="xs" type="normal" @click="showMember(row.csid)">人员</lay-button>
                <lay-button size="xs" type="normal" @click="showVideos(row.csid)">课件</lay-button>
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delSubject(row.csid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :area="['960px','90%']" :btn="showAddPageBtn" title="添加课程">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="model" :pane="false" size="md" :labelWidth="100" class="form" ref="addPageFrom">
				<lay-form-item label="课程标题" prop="cstitle" required>
					<lay-input v-model="model.cstitle" placeholder="请填写课程标题"></lay-input>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="csthumb">
					<myThumb v-model:src="model.csthumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="课程分类" prop="cscatid" required>
					<lay-tree-select v-model="model.cscatid" :data="laycats" placeholder="选择分类"></lay-tree-select>
				</lay-form-item>
				<lay-form-item label="控制进度" prop="csprogress">
					<lay-radio v-model="model.csprogress" name="csprogress" :value="0" label="禁止"></lay-radio>
					<lay-radio v-model="model.csprogress" name="csprogress" :value="1" label="允许"></lay-radio>
				</lay-form-item>
                <lay-form-item label="人脸对比时间" prop="csfacetime">
                    <lay-radio v-model="model.csfacetime" :value="0" label="不验证" name="csfacetime"></lay-radio>
                    <lay-radio v-model="model.csfacetime" :value="10" label="10分钟" name="csfacetime"></lay-radio>
                    <lay-radio v-model="model.csfacetime" :value="20" label="20分钟" name="csfacetime"></lay-radio>
                    <lay-radio v-model="model.csfacetime" :value="30" label="30分钟" name="csfacetime"></lay-radio>
                    <lay-radio v-model="model.csfacetime" :value="40" label="40分钟" name="csfacetime"></lay-radio>
                    <lay-radio v-model="model.csfacetime" :value="50" label="50分钟" name="csfacetime"></lay-radio>
                    <lay-radio v-model="model.csfacetime" :value="60" label="60分钟" name="csfacetime"></lay-radio>
                </lay-form-item>
                <lay-form-item label="课程简介" prop="csdescribe">
                    <lay-textarea placeholder="请输入课程简介" v-model="model.csdescribe"></lay-textarea>
                </lay-form-item>
				<lay-form-item label="课程详情" prop="cstext">
					<myEditor v-model:content="model.cstext"></myEditor>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :area="['960px','90%']" :btn="showModifyPageBtn" title="编辑课程">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modify" :pane="false" size="md" :labelWidth="100" class="form" ref="modifyPageFrom">
				<lay-form-item label="课程标题" prop="cstitle" required>
					<lay-input v-model="modify.cstitle" placeholder="请填写课程标题"></lay-input>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="csthumb">
					<myThumb v-model:src="modify.csthumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="课程分类" prop="cscatid" required>
					<lay-tree-select v-model="modify.cscatid" :data="laycats" placeholder="选择分类"></lay-tree-select>
				</lay-form-item>
				<lay-form-item label="控制进度" prop="csprogress">
					<lay-radio v-model="modify.csprogress" name="csprogress" :value="0" label="禁止"></lay-radio>
					<lay-radio v-model="modify.csprogress" name="csprogress" :value="1" label="允许"></lay-radio>
				</lay-form-item>
                <lay-form-item label="人脸对比时间" prop="csfacetime">
                    <lay-radio v-model="modify.csfacetime" :value="0" label="不验证" name="csfacetime"></lay-radio>
                    <lay-radio v-model="modify.csfacetime" :value="10" label="10分钟" name="csfacetime"></lay-radio>
                    <lay-radio v-model="modify.csfacetime" :value="20" label="20分钟" name="csfacetime"></lay-radio>
                    <lay-radio v-model="modify.csfacetime" :value="30" label="30分钟" name="csfacetime"></lay-radio>
                    <lay-radio v-model="modify.csfacetime" :value="40" label="40分钟" name="csfacetime"></lay-radio>
                    <lay-radio v-model="modify.csfacetime" :value="50" label="50分钟" name="csfacetime"></lay-radio>
                    <lay-radio v-model="modify.csfacetime" :value="60" label="60分钟" name="csfacetime"></lay-radio>
                </lay-form-item>
                <lay-form-item label="课程简介" prop="csdescribe">
                    <lay-textarea placeholder="请输入课程简介" v-model="modify.csdescribe"></lay-textarea>
                </lay-form-item>
                <lay-form-item label="课程详情" prop="cstext">
                    <myEditor v-model:content="modify.cstext"></myEditor>
                </lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import courseApi from '@/framework/api/admin/course.js';
import myThumb from '@/components/desktop/Thumb.vue';
import myEditor from '@/components/master/Editor.vue';
import {withConfirm, withLayer} from "@/framework/utils/decorators.js";
export default {
	data() {
		return {
			columns:[{
				title:'复选',
				type: "checkbox",
				width:'80px',
				fixed: "left"
			},{
				title:'排序',
				customSlot: "cssequence",
				key:'cssequence',
				width:'80px'
			},{
				title:'ID',
				key:'csid',
				width:'20px'
			},{
				title:'缩略图',
				customSlot: "csthumb",
				key:'csthumb',
				width:'60px'
			},{
				title:'课程名称',
				key:'cstitle'
			},{
				title:'课程分类',
				key:'catname',
				width:'200px'
			},{
				title:'发布时间',
				key:'cstime',
				width:'200px'
			},{
                title:'开通人数',
                key:'csnumber',
                width:'80px'
            },{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"240px"
			}],
			dataSource:[],
			laycats:[],
			search:{},
			selectedKeys:[],
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{ current: 1, limit: 10, total: 0 },
			model:{},
			modify:{},
			showAddPage:false,
			showModifyPage:false,
			showAddPageBtn:[
				{
					text: "确认",
					callback: () => {
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showAddPage = false;
							this.addSubject();
						}).catch( res => {
							console.log(res);
						});
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showAddPage = false;
					}
				}
			],
			showModifyPageBtn:[
				{
					text: "确认",
					callback: () => {
						this.$refs['modifyPageFrom'].validate().then((res) => {
							this.showModifyPage = false;
							this.modifySubject();
						}).catch( res => {
							console.log(res);
						});
					}
				},
				{
					text: "取消",
					callback: () => {
						this.showModifyPage = false;
					}
				}
			]
		}
	},
	async mounted() {
		this.laycats = await courseApi.getCategroyTree();
		await this.getData();
	},
	components:{
		myThumb:myThumb,
		myEditor:myEditor
	},
	methods:{
		base:async function(fn){
			await withLayer(fn,	null,this.getData);
		},
		getData:async function(){
			await withLayer(
					async () => {
						const data = await courseApi.getSubjectList({
							search:this.search,
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
		addSubject:function(){
			this.base(async () => {
				await courseApi.addSubject(this.model);
			});
		},
		modifySubject:function(){
			this.base(async () => {
				await courseApi.modifySubject(this.modify);
			});
		},
		modifySequence:function(row){
			this.base(async () => {
				await courseApi.modifySubject({
					csid:row.csid,
					cssequence:row.cssequence,
				});
			});
		},
		delSubject:function(id){
			withConfirm('确定要删除吗？', async () => {
				await courseApi.delSubject(id?[id]:this.selectedKeys);
			},this.getData)
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		showVideos:function(id){
			this.$router.push('/desktop/master/course/course/'+id);
		},
		showPrice:function(id){
			this.$router.push('/desktop/master/course/price/'+id);
		},
        showMember:function(id){
            this.$router.push('/desktop/master/course/member/'+id);
        },
		showModify:function(course){
			this.modify = course
			this.showModifyPage = true
		}
	}
}
</script>