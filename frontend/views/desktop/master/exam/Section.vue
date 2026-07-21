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
        <lay-quote>可以使用AI生成JSON格式保存为.json文件导入【<a href="javascript:" @click="showAIPromptDailog = true;">查看AI提示词</a>】。</lay-quote>
        <lay-table id="sectionid" ref="tableRef" v-model:selected-keys="selectedKeys" :columns="columns" :data-source="dataSource" :default-toolbar="false" :page="page" even>
			<template #toolbar>
				<lay-button size="sm" type="primary" @click="showAddPage = true">添加章节</lay-button>
                <lay-button size="sm" type="primary" @click="importSection">JSON导入</lay-button>
                <lay-button size="sm" type="primary" @click="exportSection">JSON导出</lay-button>
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
    <lay-layer v-model="showAIPromptDailog"  :area="['800px']" title="AI提示词">
        <div style="padding: 20px 50px 20px 20px;">
            <pre>
# 任务
返回一个json格式的提示词，格式如下：
[
	{
		section:'章节一',
		children:[
			'知识点一',
			'知识点二',
			'知识点三',
		]
	}
]
# 内容要求
请以 初中生物内容为基础，生成符合格式要求的章节知识点数据。
务必保证JSON格式正确，请勿添加多余字段。
            </pre>
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
            showAIPromptDailog:false,
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
		},
        importSection:function(){
            let input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'json');
            input.click();
            input.onchange = async () => {
                const formData = new FormData();
                try{
                    formData.append('subjectid', this.subjectid );
                    formData.append('file', input.files[0], input.files[0].name );
                    await examApi.importSection(formData);
                }catch (e) {
                    layer.confirm(e.message || '操作失败');
                }finally {
                    await this.getData();
                }
            };
        },
        exportSection:async function(){
            const id = layer.load(0);
            try{
                const data = await examApi.exportSection(this.subjectid);
                const a = document.createElement("a");
                a.download = "data.json";
                // 创建二进制对象
                const blob = new Blob([data]);
                const downloadURL = (window.URL || window.webkitURL).createObjectURL(blob);
                a.href = downloadURL;
                a.click();
                URL.revokeObjectURL(downloadURL);
                layer.close(id);
            }catch(e){
                layer.close(id);
                layer.msg(e.message || '下载失败')
            }
        }
	}
}
</script>