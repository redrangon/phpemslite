<template>
	<lay-card class="pagecontent">
		<lay-table :columns="columns" :data-source="dataSource" v-model:selectedKeys="selectedKeys" even>
			<template #name="{ row }">
				<lay-input v-model="row.catlite" @change="modifySequence(row)"/>
			</template>
			<template #catthumb="{ row }">
				<lay-avatar :src="row.catthumb?row.catthumb:'@/assets/images/upload.png'" size="lg"></lay-avatar>
			</template>
			<template #toolbar>
				<span v-if="parentid === 0">一级分类</span>
				<span v-else>
					<lay-button border-style="none" size="xs" @click="parentCate(currentcat.catparent)"><lay-icon type="layui-icon-return"></lay-icon></lay-button> {{ currentcat.catname }}
				</span>
				<lay-button type="primary" size="sm" @click="showAdd()" style="float:right;">添加分类</lay-button>
			</template>
			<template #footer>
				<lay-row>
					<lay-page v-model="page.current" :layout="layout" v-model:limit="page.limit" :total="page.total"  @change="changePage" style="float:right;"></lay-page>
				</lay-row>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="parentCate(row.catid)">子分类</lay-button>
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="deleteData(row.catid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :area="['800px']" :btn="showAddPageBtn" title="添加分类">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="model" :pane="false" size="md" :labelWidth="100" class="form" ref="addPageFrom">
				<lay-form-item label="分类名称" prop="catname" required>
					<lay-input v-model="model.catname" placeholder="请填写分类名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="上级分类" prop="catparent">
					<lay-tree-select v-model="model.catparent" :data="laycats" allow-clear placeholder="选择上级分类"></lay-tree-select>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="catthumb">
					<myThumb v-model:src="model.catthumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="分类排序" prop="catlite" required>
					<lay-input v-model="model.catlite" placeholder="请填写分类排序"></lay-input>
				</lay-form-item>
                <lay-form-item label="分类简介" prop="catdes">
                    <lay-textarea v-model="model.catdes" placeholder="请输入分类简介"></lay-textarea>
                </lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :area="['800px']" :btn="showModifyPageBtn" title="编辑分类">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modify" :pane="false" size="md" :labelWidth="100" class="form" ref="modifyPageFrom">
				<lay-form-item label="分类名称" prop="catname" required>
					<lay-input v-model="modify.catname" placeholder="请填写分类名称"></lay-input>
				</lay-form-item>
				<lay-form-item label="上级分类" prop="catparent" required>
					<lay-tree-select v-model="modify.catparent" :data="laycats" placeholder="选择上级分类"></lay-tree-select>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="catthumb">
					<myThumb v-model:src="modify.catthumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="分类排序" prop="catlite" required>
					<lay-input v-model="modify.catlite" placeholder="请填写分类排序"></lay-input>
				</lay-form-item>
                <lay-form-item label="分类简介" prop="catdes">
                    <lay-textarea v-model="modify.catdes" placeholder="请输入分类简介"></lay-textarea>
                </lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import courseApi from '@/framework/api/admin/course.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import myThumb from '@/components/desktop/Thumb.vue';
import {withConfirm, withLayer} from "@/framework/utils/decorators.js";
export default {
	data() {
		return {
			columns:[{
				title:'排序',
				customSlot: "name",
				key:'catlite',
				width:'80px'
			},{
				title:'ID',
				key:'catid',
				width:'20px'
			},{
				title:'缩略图',
				key:'catthumb',
				customSlot:'catthumb',
				width:'60px'
			},{
				title:'分类名称',
				key:'catname'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"155"
			}],
			dataSource:[],
			sequence:[],
			currentcat:{},
			selectedKeys:[],
			parentid:0,
			layout:['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'],
			page:{
				limit:20,
				current:1,
				total:0
			},
			model:{},
			modify:{},
			laycats:[],
			showAddPage:false,
			showModifyPage:false,
			showAddPageBtn:[
				{
					text: "确认",
					callback: () => {
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showAddPage = false;
							this.addCategory();
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
							this.modifyCategory();
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
			],
			replaceFields:{
				id:'value',
				title:'label'
			}
		}
	},
	components:{
		myThumb:myThumb,
	},
	async mounted() {
		await this.getData();
	},
	methods:{
		base:async function(fn){
			await withLayer(fn,	null,this.getData);
		},
		getData:async function(){
			await withLayer(
					async () => {
						const data = await courseApi.getCategoryList({
							parentid:this.parentid,
							limit:this.page.limit,
							page:this.page.current
						});
						this.page.page = data.page;
						this.page.total = data.total;
						this.page.limit = data.limit;
						this.dataSource = data.data;
					},[null,null]
			);
		},
		changePage:function(current,limit){
			this.page.current = current
			this.page.limit = limit
			this.getData();
		},
		parentCate:function(catid){
			withLayer(
					async () => {
						this.parentid = catid;
						this.currentcat = await courseApi.getCategory(catid);
					},[null,null],this.getData
			);
		},
		showAdd:async function(){
			this.laycats = await courseApi.getCategroyTree();
			this.showAddPage = true;
		},
		addCategory:function(){
			this.base( async () => {
				await courseApi.addCategory(this.model);
				this.model = {};
			});
		},
		showModify:async function(row){
			this.modify = {...row};
			this.laycats = await courseApi.getCategroyTree();
			this.showModifyPage = true
		},
		modifyCategory:function(){
			this.base( async () => {
				await courseApi.modifyCategory(this.modify);
			});
		},
		modifySequence:function(row){
			this.base( async () => {
				await courseApi.modifyCategory({
					catid:row.catid,
					catlite:row.catlite
				});
			});
		},
		deleteData:function(id){
			withConfirm('确定要删除吗？', async () => {
				await courseApi.delCategory(id?[id]:this.selectedKeys);
			},this.getData)
		}
	}
}
</script>