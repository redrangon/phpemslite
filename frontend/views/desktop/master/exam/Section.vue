<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:130px'> 章节关键字：</span><lay-input v-model="search.section"></lay-input>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-table id="sectionid" ref="tableRef" v-model:selected-keys="selectedKeys" :columns="columns" :data-source="dataSource" :default-toolbar="false" :page="page" even>
			<template #toolbar>
				<lay-button size="sm" type="primary" @click="showAddPage = true">添加章节</lay-button>
			</template>
			<template #footer>
				<lay-button :disabled="selectedKeys.length < 1" size="sm" type="primary" @click="refreshSection()">刷新缓存</lay-button>
			</template>
			<template v-slot:sequence="{ row }">
				<lay-input v-model="row.sectionsequence" placeholder="排序" style="width:100%;" @change="liteSection(row.sectionid,row.sectionsequence)"></lay-input>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="showPoint(row.sectionid)">知识点</lay-button>
				<lay-button size="xs" type="primary" @click="refreshSection(row.sectionid)">更新缓存</lay-button>
				<lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
				<lay-button size="xs" type="danger" @click="delSection(row.sectionid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :area="['500px']" :btn="addPageBtns" :shade="false" title="添加章节">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form ref="addPageFrom" :model="model" :pane="false" class="form" labelWidth="100" size="md">
				<lay-form-item label="章节名称" prop="section" required>
					<lay-input v-model="model.section" placeholder="请输入章节名称"></lay-input>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :area="['500px']" :btn="modifyPageBtns" :shade="false" title="修改章节">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form ref="modifyPageFrom" :model="modelModify" :pane="false" class="form" labelWidth="100" size="md">
				<lay-form-item label="章节名称" prop="section" required>
					<lay-input v-model="modelModify.section" placeholder="请输入章节名称"></lay-input>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script>
import examApi from '@/framework/api/admin/exam.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
    mixins:[baseMixin],
	data() {
		return {
			subjectid:0,
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
				key: 'sectionid',
				width: '20px'
			}, {
				title: '章节名称',
				key: 'section'
			}, {
				title: '操作',
				customSlot: "operator",
				key: "operator",
				width: "230px"
			}],
			dataSource:[],
			search:{},
			selectedKeys:[],
			page:{ current: 1, limit: 20, total: 0 },
			showAddPage:false,
			showModifyPage:false,
			model:{},
			modelModify:{},
			addPageBtns:[
				{
					text: "确认",
					callback: () => {
						this.$refs['addPageFrom'].validate().then((res) => {
							this.showAddPage = false;
							this.addSection();
						}).catch( res => {});
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
							this.modifySection();
						}).catch( res => {});
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
		this.subjectid = this.$route.params.subjectid;
		this.model.sectionsubjectid = this.subjectid;
		await this.getData();
	},
	methods:{
		getData:async function(){
			await this.execute(
					async () => {
						const data = await examApi.getSectionList({
							subjectid:this.subjectid,
							page:this.page.current,
							search:this.search
						});
						this.page.page = data.page;
						this.page.total = data.total;
						this.page.limit = data.limit;
						this.dataSource = data.data;
					},null,null
			);
		},
		showModify:function(row){
			this.modelModify = JSON.parse(JSON.stringify(row));
			this.showModifyPage = true;
		},
		delSection:function(id){
			this.confirmDelete('确定要删除吗？', async () => {
				await examApi.delSections(id?[id]:this.selectedKeys);
			},this.getData)
		},
		addSection:function(){
			this.base( async() => {
				await examApi.addSection(this.model)
			});
		},
		modifySection:function(){
			this.base( async() => {
				await examApi.modifySection(this.modelModify);
			});
		},
		refreshSection:function(id){
			this.confirmOperate('刷新数据可能短暂卡顿，确定要刷新吗？', async () => {
				await examApi.refreshSectionCache(id?[id]:this.selectedKeys);
			},this.getData)
		},
		liteSection:async function(id,lite)
		{
			await this.base( async() => {
				await examApi.modifySection({
					sectionid:id,
					sectionsequence:lite
				});
			});
		},
		showPoint:function(id){
			this.$router.push('/desktop/master/exam/point/' + this.subjectid + '/' + id);
		}
	}
}
</script>