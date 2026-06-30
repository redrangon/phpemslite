<template>
	<div>
		<lay-card>
			<lay-table ref="tableRef" :columns="columns" :data-source="dataSource" :default-toolbar="false">
				<template #toolbar>
					{{ subject.cstitle }}
					<lay-button type="primary" size="sm" style="float: right" @click="showAdd()">添加价格</lay-button>
				</template>
				<template v-slot:operator="{ row }">
					<lay-button size="xs" type="primary" @click="showModify(row)">修改</lay-button>
					<lay-button size="xs" type="danger" @click="delPrice(row.cpid)">删除</lay-button>
				</template>
			</lay-table>
		</lay-card>
		<lay-layer v-model="showAddPage" :area="['800px']" :btn="showAddPageBtn" title="添加价格">
			<div style="padding: 20px 50px 20px 20px;">
				<lay-form :model="model" :pane="false" size="md" :labelWidth="100" class="form" ref="addPageFrom">
					<lay-form-item label="天数" prop="cpdays" required>
						<lay-input v-model="model.cpdays" placeholder="填写天数"></lay-input>
					</lay-form-item>
					<lay-form-item label="价格" prop="cpamount" required>
						<lay-input v-model="model.cpamount" placeholder="填写价格"></lay-input>
					</lay-form-item>
				</lay-form>
			</div>
		</lay-layer>
		<lay-layer v-model="showModifyPage" :area="['800px']" :btn="showModifyPageBtn" title="编辑价格">
			<div style="padding: 20px 50px 20px 20px;">
				<lay-form :model="modify" :pane="false" size="md" :labelWidth="100" class="form" ref="modifyPageFrom">
					<lay-form-item label="天数" prop="cpdays" required>
						<lay-input v-model="modify.cpdays" placeholder="填写天数"></lay-input>
					</lay-form-item>
					<lay-form-item label="价格" prop="cpamount" required>
						<lay-input v-model="modify.cpamount" placeholder="填写价格"></lay-input>
					</lay-form-item>
				</lay-form>
			</div>
		</lay-layer>
	</div>
</template>
<style scoped>
</style>
<script>
import courseApi from '@/framework/api/admin/course.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
	data() {
		return {
			columns:[{
				title:'ID',
				key:'cpid',
				width:'80px'
			},{
				title:'天数（天）',
				key:'cpdays'
			},{
				title:'价格（积分）',
				key:'cpamount',
				width:'120px'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"100px"
			}],
			dataSource:[],
            csId:0,
			subject:{},
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
							this.addPrice();
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
							this.modifyPrice();
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
		}
	},
	async mounted() {
		this.csId = this.$route.params.csid;
		this.subject = await courseApi.getSubject(this.csId);
		await this.getData()
	},
	methods:{
		getData:async function(){
			await this.execute( async () =>{
                this.dataSource = await courseApi.getSubjectPrice(this.csId);
			},null,null);
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
        delPrice:function(id){
            this.confirmOperate('确定要删除吗？', async () => {
                await courseApi.delSubjectPrice(id?[id]:this.selectedKeys);
            },null)

        },
		showAdd:function(){
			this.model = {
				cpcsid:this.csId,
			};
			this.showAddPage = true;
		},
        addPrice:async function(){
            await this.base( async() => {
                await courseApi.addSubjectPrice(this.model);
            });
        },
		showModify:async function(price){
			this.modify = JSON.parse(JSON.stringify(price));
			this.showModifyPage = true;
		},
        modifyPrice:async function(){
	        await this.base( async() => {
                await courseApi.modifySubjectPrice(this.modify);
            });
        },
	}
}
</script>