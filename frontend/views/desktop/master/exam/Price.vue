<template>
	<div>
		<lay-card>
			<lay-table ref="tableRef" :columns="columns" :data-source="dataSource" :default-toolbar="false">
				<template #toolbar>
					{{ basic.basic }}
					<lay-button type="primary" size="sm" style="float: right" @click="showAdd()">添加价格</lay-button>
				</template>
				<template v-slot:operator="{ row }">
					<lay-button size="xs" type="primary" @click="showModify(row)">修改</lay-button>
					<lay-button size="xs" type="danger" @click="delPrice(row.epid)">删除</lay-button>
				</template>
			</lay-table>
		</lay-card>
		<lay-layer v-model="showAddPage" :area="['800px']" :btn="showAddPageBtn" title="添加价格">
			<div style="padding: 20px 50px 20px 20px;">
				<lay-form :model="model" :pane="false" size="md" :labelWidth="100" class="form" ref="addPageFrom">
					<lay-form-item label="天数" prop="epdays" required>
						<lay-input v-model="model.epdays" placeholder="填写天数"></lay-input>
					</lay-form-item>
					<lay-form-item label="价格" prop="epamount" required>
						<lay-input v-model="model.epamount" placeholder="填写价格"></lay-input>
					</lay-form-item>
				</lay-form>
			</div>
		</lay-layer>
		<lay-layer v-model="showModifyPage" :area="['800px']" :btn="showModifyPageBtn" title="编辑价格">
			<div style="padding: 20px 50px 20px 20px;">
				<lay-form :model="modify" :pane="false" size="md" :labelWidth="100" class="form" ref="modifyPageFrom">
					<lay-form-item label="天数" prop="epdays" required>
						<lay-input v-model="modify.epdays" placeholder="填写天数"></lay-input>
					</lay-form-item>
					<lay-form-item label="价格" prop="epamount" required>
						<lay-input v-model="modify.epamount" placeholder="填写价格"></lay-input>
					</lay-form-item>
				</lay-form>
			</div>
		</lay-layer>
	</div>
</template>
<style scoped>
</style>
<script>
import examApi from '@/framework/api/admin/exam.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
	data() {
		return {
			columns:[{
				title:'ID',
				key:'epid',
				width:'80px'
			},{
				title:'天数（天）',
				key:'epdays'
			},{
				title:'价格（积分）',
				key:'epamount',
				width:'120px'
			},{
				title:'操作',
				customSlot:"operator",
				key:"operator",
				width:"100px"
			}],
			dataSource:[],
            basicId:0,
			basic:{},
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
		this.basicId = this.$route.params.basicid;
		this.basic = await examApi.getBasic(this.basicId);
		await this.getData()
	},
	methods:{
		getData:async function(){
			await this.execute( async () =>{
                this.dataSource = await examApi.getBasicPrice(this.basicId);
			},null,null);
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
        delPrice:function(id){
	        this.confirmOperate('确定要删除吗？', async () => {
                await examApi.delBasicPrice(id?[id]:this.selectedKeys);
            },this.getData)
        },
		showAdd:function(){
			this.model = {
				epbasicid:this.basicId,
			};
			this.showAddPage = true;
		},
        addPrice:async function(){
            await this.base( async() => {
                await examApi.addBasicPrice(this.model);
            });
        },
		showModify:async function(price){
			this.modify = JSON.parse(JSON.stringify(price));
			this.showModifyPage = true;
		},
        modifyPrice:async function(){
	        await this.base( async() => {
                await examApi.modifyBasicPrice(this.modify);
            });
        },
	}
}
</script>