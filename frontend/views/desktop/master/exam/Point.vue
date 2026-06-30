<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:130px'> 知识点关键字：</span><lay-input v-model="search.knows"></lay-input>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-table ref="tableRef" :default-toolbar="false" :page="page" :columns="columns" :data-source="dataSource" id="knowsid" v-model:selected-keys="selectedKeys" even>
			<template #toolbar>
				<lay-button type="primary" size="sm" @click="showAddPage = true">添加知识点</lay-button>
			</template>
			<template #footer>
				<lay-button type="primary" size="sm" @click="refreshPoint()" :disabled="selectedKeys.length < 1">更新缓存</lay-button>
				<lay-button type="primary" size="sm" @click="delPoint()" :disabled="selectedKeys.length < 1">删除选中数据</lay-button>
			</template>
			<template v-slot:sequence="{ row }">
				<lay-input placeholder="排序" style="width:100%;" v-model="row.pointsequence" @change="litePoint(row.pointid,row.pointsequence)"></lay-input>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="refreshPoint(row.pointid)">更新缓存</lay-button>
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delPoint(row.pointid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :shade="false" :area="['500px']" :btn="addPageBtns" title="添加知识点">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="model" :pane="false" size="md" labelWidth="100" class="form" ref="addPageFrom">
				<lay-form-item label="知识点名称" prop="point" required>
					<lay-input v-model="model.point" placeholder="请输入知识点名称"></lay-input>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :shade="false" :area="['500px']" :btn="modifyPageBtns" title="修改知识点">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modelModify" :pane="false" size="md" labelWidth="100" class="form" ref="modifyPageFrom">
				<lay-form-item label="知识点名称" prop="point" required>
					<lay-input v-model="modelModify.point" placeholder="请输入知识点名称"></lay-input>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import examApi from '@/framework/api/admin/exam.js';
import {layer} from '@layui/layui-vue';
import {withConfirm, withLayer} from "@/framework/utils/decorators.js";
export default {
	data() {
		return {
			sectionid:0,
			section:{},
			columns:[{
				title: "选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			}, {
				title: '排序',
				customSlot: "sequence",
				key: "sequence",
				width: "60px"
			}, {
				title: 'ID',
				key: 'pointid',
				width: '20px'
			}, {
				title: '知识点名称',
				key: 'point'
			}, {
				title: '操作',
				customSlot: "operator",
				key: "operator",
				width: "180"
			}],
			dataSource:[],
			search:{'status':1},
			selectedKeys:[],
			page:{
				current: 1,
				limit: 20,
				total: 0
			},
			showAddPage:false,
			showModifyPage:false,
			model:{},
			modelModify:{},
			addPageBtns:[
				{
					text: "确认",
					callback: () => {
						console.log(this.model);
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showAddPage = false;
							this.addPoint();
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
			modifyPageBtns:[
				{
					text: "确认",
					callback: () => {
						this.$refs['modifyPageFrom'].validate().then((res) => {
							this.showModifyPage = false;
							this.modifyPoint();
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
		this.sectionid = this.$route.params.sectionid;
		this.model.knowssectionid = this.sectionid;
		await this.getSection();
		await this.getData();
	},
	methods:{
		base:async function(fn){
			await withLayer(fn,	null,this.getData);
		},
		getSection:async function(){
			await withLayer(
				async () =>{
					this.section = await examApi.getSection(this.sectionid);
				},
				[null,null]
			);
		},
		getData:async function(){
			await withLayer(
					async () => {
						const data = await examApi.getPointList({
							sectionid:this.sectionid,
							search:this.search,
							page:this.page.current
						});
						this.page.page = data.page;
						this.page.total = data.total;
						this.page.limit = data.limit;
						this.dataSource = data.data;
					},[null,null]
			);
		},
		showModify:function(row){
			this.modelModify = JSON.parse(JSON.stringify(row));
			this.showModifyPage = true;
		},
		delPoint:function(id){
			withConfirm('确定要删除吗？', async () => {
				await examApi.delPoints(id?[id]:this.selectedKeys);
			},this.getData)
		},
		addPoint:async function(){
			this.base( async() => {
				await examApi.addPoint({
					pointsectionid:this.sectionid,
					...this.model
				});
			});
		},
		modifyPoint:async function(){
			this.base( async() => {
				await examApi.modifyPoint({
					ointsectionid:this.sectionid,
					...this.modelModify
				});
			});
		},
		litePoint:async function(id,lite)
		{
			this.base( async() => {
				await examApi.modifyPoint({
					pointid:id,
					pointsequence:lite
				});
			});
		},
		refreshPoint:function(id){
			withConfirm('更新缓存可能短暂卡顿，确定要更新吗？', async () => {
				await examApi.refreshPointCache(id?[id]:this.selectedKeys);
			},this.getData)
		}
	}
}
</script>