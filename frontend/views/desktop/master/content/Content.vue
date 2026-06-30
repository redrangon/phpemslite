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
				<lay-tree-select v-model="search.contentcatid" :data="laycats" placeholder="选择分类" allow-clear></lay-tree-select>
			</lay-space>
			<lay-space>
				<span style='width:70px'> 状态：</span>
				<lay-select v-model="search.contentstatus" placeholder="选择状态" style="width: 120px;" allow-clear>
					<lay-select-option :value="0" label="禁用"></lay-select-option>
					<lay-select-option :value="1" label="启用"></lay-select-option>
				</lay-select>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card class="pagecontent">
		<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" v-model:selectedKeys="selectedKeys" :data-source="dataSource" id="contentid">
			<template #toolbar>
				<lay-button type="primary" size="sm" @click="showAddPage = true">添加内容</lay-button>
			</template>
			<template #contentsequence="{ row }">
				<lay-input v-model="row.contentsequence" @change="modifySequence(row)"/>
			</template>
			<template #contentthumb="{ row }">
				<lay-avatar :src="row.contentthumb?row.contentthumb:'/src/assets/images/noimages.png'" size="lg"></lay-avatar>
			</template>
			<template #contentstatus="{ row }">
				<lay-switch :model-value="!!row.contentstatus" @change="modifyStatus(row, $event)"></lay-switch>
			</template>
			<template #footer>
				<lay-row>
					<lay-col md="12">
						<lay-button type="danger" size="sm" @click="delContent()" :disabled="selectedKeys.length < 1">删除选中数据</lay-button>
					</lay-col>
					<lay-col md="12">
						<lay-page v-model="page.current" :layout="layout" v-model:limit="page.limit" :total="page.total" @change="changePage" style="float:right;"></lay-page>
					</lay-col>
				</lay-row>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delContent(row.contentid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :area="['1060px','80%']" :btn="showAddPageBtn" title="添加内容">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="model" :pane="false" size="md" :labelWidth="100" class="form" ref="addPageFrom">
				<lay-form-item label="内容标题" prop="contenttitle" required>
					<lay-input v-model="model.contenttitle" placeholder="请填写内容标题"></lay-input>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="contentthumb">
					<myThumb v-model:src="model.contentthumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="内容分类" prop="contentcatid" required>
					<lay-tree-select v-model="model.contentcatid" :data="laycats" placeholder="选择分类"></lay-tree-select>
				</lay-form-item>
				<lay-form-item label="标签" prop="contenttags">
					<lay-input v-model="model.contenttags" placeholder="请填写标签"></lay-input>
				</lay-form-item>
				<lay-form-item label="关键字" prop="contentkeywords">
					<lay-input v-model="model.contentkeywords" placeholder="请填写关键字"></lay-input>
				</lay-form-item>
				<lay-form-item label="外部链接" prop="contentlink">
					<lay-input v-model="model.contentlink" placeholder="请填写外部链接"></lay-input>
				</lay-form-item>
				<lay-form-item label="排序" prop="contentsequence">
					<lay-input-number v-model="model.contentsequence" placeholder="请填写排序值"></lay-input-number>
				</lay-form-item>
				<lay-form-item label="状态" prop="contentstatus">
					<lay-radio v-model="model.contentstatus" name="contentstatus" :value="0" label="禁用"></lay-radio>
					<lay-radio v-model="model.contentstatus" name="contentstatus" :value="1" label="启用"></lay-radio>
				</lay-form-item>
				<lay-form-item label="内容摘要" prop="contentdescribe">
					<lay-textarea placeholder="请输入内容摘要" v-model="model.contentdescribe"></lay-textarea>
				</lay-form-item>
				<lay-form-item label="内容详情" prop="contenttext">
					<myEditor v-model:content="model.contenttext"></myEditor>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :area="['1060px','80%']" :btn="showModifyPageBtn" title="编辑内容">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modify" :pane="false" size="md" :labelWidth="100" class="form" ref="modifyPageFrom">
				<lay-form-item label="内容标题" prop="contenttitle" required>
					<lay-input v-model="modify.contenttitle" placeholder="请填写内容标题"></lay-input>
				</lay-form-item>
				<lay-form-item label="缩略图" prop="contentthumb">
					<myThumb v-model:src="modify.contentthumb" style="width:160px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="内容分类" prop="contentcatid" required>
					<lay-tree-select v-model="modify.contentcatid" :data="laycats" placeholder="选择分类"></lay-tree-select>
				</lay-form-item>
				<lay-form-item label="标签" prop="contenttags">
					<lay-input v-model="modify.contenttags" placeholder="请填写标签"></lay-input>
				</lay-form-item>
				<lay-form-item label="关键字" prop="contentkeywords">
					<lay-input v-model="modify.contentkeywords" placeholder="请填写关键字"></lay-input>
				</lay-form-item>
				<lay-form-item label="外部链接" prop="contentlink">
					<lay-input v-model="modify.contentlink" placeholder="请填写外部链接"></lay-input>
				</lay-form-item>
				<lay-form-item label="排序" prop="contentsequence">
					<lay-input-number v-model="modify.contentsequence" placeholder="请填写排序值"></lay-input-number>
				</lay-form-item>
				<lay-form-item label="状态" prop="contentstatus">
					<lay-radio v-model="modify.contentstatus" name="contentstatus" :value="0" label="禁用"></lay-radio>
					<lay-radio v-model="modify.contentstatus" name="contentstatus" :value="1" label="启用"></lay-radio>
				</lay-form-item>
				<lay-form-item label="内容摘要" prop="contentdescribe">
					<lay-textarea placeholder="请输入内容摘要" v-model="modify.contentdescribe"></lay-textarea>
				</lay-form-item>
				<lay-form-item label="内容详情" prop="contenttext">
					<myEditor v-model:content="modify.contenttext"></myEditor>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import contentApi from '@/framework/api/admin/content.js';
import {layer} from '@layui/layui-vue';
import myThumb from '@/components/desktop/Thumb.vue';
import {ref} from 'vue';
import myEditor from '@/components/master/Editor.vue';
import baseMixin from '@/framework/mixins/baseMixin.js';
export default {
	mixins: [baseMixin],
	data() {
		return {
			columns:[{
				title:'复选',
				type: "checkbox",
				width:'80px',
				fixed: "left"
			},{
				title:'排序',
				customSlot: "contentsequence",
				key:'contentsequence',
				width:'80px'
			},{
				title:'ID',
				key:'contentid',
				width:'60px'
			},{
				title:'缩略图',
				customSlot: "contentthumb",
				key:'contentthumb',
				width:'80px'
			},{
				title:'内容标题',
				key:'contenttitle'
			},{
				title:'内容分类',
				key:'catname',
				width:'150px'
			},{
				title:'状态',
				customSlot: "contentstatus",
				key:'contentstatus',
				width:'80px'
			},{
				title:'发布时间',
				key:'contentinputtime',
				width:'180px'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"100px"
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
							this.addContent();
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
							this.modifyContent();
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
		this.laycats = await contentApi.getCategroyTree();
		await this.getData();
	},
	components:{
		myThumb:myThumb,
		myEditor:myEditor
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await contentApi.getContentList({
					search:this.search,
					limit:this.page.limit,
					page:this.page.current
				})
				this.page.page = data.page;
				this.page.total = data.total;
				this.page.limit = data.limit;
				this.dataSource = data.data;
			}, null, null);
		},
		addContent:function(){
			this.base(async () => {
				await contentApi.addContent(this.model);
			}, '添加成功');
		},
		modifyContent:function(){
			this.base(async () => {
				await contentApi.modifyContent(this.modify);
			}, '修改成功');
		},
		modifySequence:function(row){
			this.base(async () => {
				await contentApi.modifyContent({
					contentid:row.contentid,
					contentsequence:row.contentsequence,
				});
			}, '修改成功');
		},
		modifyStatus:function(row, value){
			this.base(async () => {
				await contentApi.modifyContent({
					contentid:row.contentid,
					contentstatus:value ? 1 : 0,
				});
			}, '修改成功');
		},
		delContent:function(id){
			this.confirmDelete(async () => {
				await contentApi.delContent(id?[id]:this.selectedKeys);
			}, this.getData);
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		showModify:function(content){
			this.modify = content
			this.showModifyPage = true
		}
	}
}
</script>