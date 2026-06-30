<template>
	<lay-card>
		<lay-space direction="vertical">
			<lay-space size="lg">
				<lay-space></lay-space>
				<lay-space>
					<span style='width:70px'> 用户ID：</span><lay-input v-model="search.user"></lay-input>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 用户名：</span><lay-input v-model="search.username"></lay-input>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 姓名：</span><lay-input v-model="search.usertruename"></lay-input>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 身份证号：</span><lay-input v-model="search.userpassport"></lay-input>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 审核状态：</span>
					<lay-select v-model="search.userstatus" placeholder="请选择">
						<lay-select-option :value="0" label="全部"></lay-select-option>
						<lay-select-option :value="1" label="待审核"></lay-select-option>
						<lay-select-option :value="2" label="拒绝审核"></lay-select-option>
						<lay-select-option :value="3" label="通过审核"></lay-select-option>
					</lay-select>
				</lay-space>
				<lay-space>
					<lay-button type="primary" @click="getData">搜索</lay-button>
				</lay-space>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-table :default-toolbar="false" :page="page" @change="getData" :columns="columns" :data-source="dataSource" ref="table" id="userid" v-model:selected-keys="selectedKeys" even>
			<template #toolbar>
				实名认证		
			</template>
			<template #footer>
				<lay-button type="primary" size="sm" :disabled="selectedKeys.length < 1" @click="setVerify(null,3)">通过审核</lay-button>
				<lay-button type="primary" size="sm" :disabled="selectedKeys.length < 1" @click="setVerify(null,2)">拒绝审核</lay-button>
			</template>
			<template v-slot:userstatus="{ row }">
				<span v-if="row.userstatus === 1" style="color:#FFB800">待审核</span>
				<span v-else-if="row.userstatus === 2" style="color:#FF5722">拒绝审核</span>
				<span v-else-if="row.userstatus === 3" style="color:#16b777">通过审核</span>
				<span v-else style="color:#FFB800">未申请</span>
			</template>
			<template v-slot:operator="{ row }">
				<div v-if="row.userstatus > 1">
					<lay-button size="xs" type="danger" @click="setVerify(row.userid,0)">撤销</lay-button>
				</div>
				<div v-else-if="row.userstatus === 1">
					<lay-button size="xs" type="primary" @click="setVerify(row.userid,3)">通过</lay-button>
					<lay-button size="xs" type="danger" @click="setVerify(row.userid,2)">拒绝</lay-button>
				</div>
			</template>
		</lay-table>
	</lay-card>
</template>
<style scoped></style>
<script>
import userApi from '@/framework/api/admin/user.js';
import {layer} from '@layui/layui-vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
export default {
	mixins: [baseMixin],
	data() {
		return {
			columns:[{
				title:"选项",
				width: "55px",
				type: "checkbox",
				fixed: "left",
			},{
				title:'ID',
				key:'userid',
				width:'50px'
			},{
				title:'用户名',
				key:'username',
				width:'200px'
			},{
				title:'姓名',
				key:'usertruename',
				width:'200px'
			},{
				title:'身份证号',
				key:'userpassport'
			},{
				title:'状态',
				customSlot:"userstatus",
				key:"userstatus",
				width:'100px',
			},{
				title:"操作",
				width: "100px",
				customSlot:"operator",
				key:"operator",
				fixed: "right",
				ignoreExport: true ,
			}],
			dataSource:[],
			selectedKeys:[],
			page:{ current: 1, limit: 20, total: 0 },
			search:{}
		}
	},
	async mounted() {
		await this.getData();
	},
	methods:{
		getData:async function(){
			await this.execute(async () => {
				const data = await userApi.getUsers({
					limit:this.page.limit,
					page:this.page.current,
					search:this.search
				});
				this.page = data.page;
				this.dataSource = data.data;
			},null,null)
		},
		setVerify:function(id,status){
			this.confirmOperate('确定要进行操作吗？',async () => {
				let ids = this.selectedKeys;
				if(id){
					ids = [id];
				}
				await userApi.setUserVerifyStatus(ids,status);
			},this.getData);
		},
	}
}
</script>