<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 姓名：</span><lay-input v-model="search.name"></lay-input>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-space size="sm" style="margin-bottom:20px;">
			<lay-button :type="search.status == 0?'primary':'default'" @click="setcestatus('0')">全部</lay-button>
			<lay-button :type="search.status == '1'?'warm':'default'" @click="setcestatus('1')">黄色预警</lay-button>
			<lay-button :type="search.status == '2'?'danger':'default'" @click="setcestatus('2')">红色预警</lay-button>
		</lay-space>
		<lay-table ref="tableRef" :default-toolbar="false" :columns="columns" :data-source="dataSource" id="ceid" v-model:selected-keys="selectedKeys" even>
			<template #toolbar>
				证件管理
			</template>
			<template #footer>
				<lay-button type="primary" size="sm" @click="" :disabled="selectedKeys.length < 1">导出选中档案</lay-button>
				<lay-button type="danger" size="sm" @click="delProfile()" :disabled="selectedKeys.length < 1">删除选中档案</lay-button>
				<lay-page v-model="page.current" v-model:limit="page.limit" :total="page.total"  @change="changePage" style="float:right;"></lay-page>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="primary" @click="showPageData(row)">查看</lay-button>
				<lay-button size="xs" type="danger" @click="delProfile(row.ceid)">删除</lay-button>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showPage" :area="['900px','90%']" :btn="showPageBtns" title="档案详情">
		<div style="padding: 20px;">
			<table class="table">
				<thead>
					<td colspan="7">学员登记表</td>
				</thead>
				<tr>
					<th>姓名</th>
					<td>{{ member.pmname }}</td>
					<th>性别</th>
					<td>{{ member.pmsex }}</td>
					<th>身份证号</th>
					<td>{{ member.pmpassport }}</td>
					<td rowspan="4"><img :src="member.pmphoto" width="90"></td>
				</tr>
				<tr>
					<th>年龄</th>
					<td>{{ member.age }}</td>
					<th>学历</th>
					<td>{{ member.pmedu }}</td>
					<th>政治面貌</th>
					<td>{{ member.pmface }}</td>
				</tr>
				<tr>
					<th>人员类别</th>
					<td>{{ member.pmusertype }}</td>
					<th>考核类别</th>
					<td>{{ member.cetitle }}</td>
					<th>职称或技能等级</th>
					<td>{{ member.pmjobtitle }}</td>
				</tr>
				<tr>
					<th>工龄</th>
					<td>{{ member.joblength }}</td>
					<th>知识成绩</th>
					<td>{{ member.pmexamscore }}</td>
					<th>实操成绩</th>
					<td>{{ member.pmopscore }}</td>
				</tr>
				<tr>
					<th>发证时间</th>
					<td colspan="3">{{ member.ceqtime }}</td>
					<th>到期时间</th>
					<td colspan="2">{{ member.ceqexpiretime }}</td>
				</tr>
				<tr>
					<th>单位全称</th>
					<td colspan="3">{{ member.pmunitallname }}</td>
					<th>科队名称</th>
					<td colspan="2">{{ member.pmteam }}</td>
				</tr>
				<thead>
					<td colspan="7">证书信息</td>
				</thead>
				<tr>
					<td colspan="7">
						<img :src="member.cardimg" width="100%">
					</td>
				</tr>
				<thead>
					<td colspan="7">身份证件信息</td>
				</thead>
				<tr>
					<td colspan="7">
						<img :src="member.pmpassporta" width="100%">
					</td>
				</tr>
			</table>
		</div>
	</lay-layer>
</template>
<script>
import ajax from '@/api/app';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
	data() {
		return {
			columns:[],
			dataSource:[],
			search:ref({status:0}),
			selectedKeys:ref(),
			page:{ current: 1, limit: 20, total: 0 },
			member:ref({}),
			showPage:ref(false),
			showPageBtns:ref([
				{
					text: "关闭",
					callback: () => {
						this.showPage = false;
					}
				}
			])
		}
	},
	emits:['setVal'],
	setup(){
		const columns = [{ 
			title:"选项", 
			width: "55px", 
			type: "checkbox", 
			fixed: "left",
		},{
			title:'ID',
			key:'ceqid',
			width:'50px'
		},{
			title:'姓名',
			key:'pmname',
			width:'100px'
		},{
			title:'身份证号',
			key:'pmpassport',
			width:"200px"
		},{
			title:'性别',
			key:'pmsex',
			width:"50px"
		},{
			title:'学历',
			key:'pmedu',
			width:"100px"
		},{
			title:'人员类别',
			key:'pmusertype',
			width:"200px"
		},{
			title:'考核类别',
			key:'cetitle',
			width:"200px"
		},{
			title:'发证日期',
			key:'ceqtime',
			width:"150px"
		},{
			title:'到期日期',
			key:'ceqexpiretime',
			width:"150px"
		},{
			title:'操作',
			customSlot:"operator", 
			key:"operator",
			width:"100"
		}];		
		return {columns}
	},
	created() {
		this.$emit('setVal',{bcmus:[{
				title:'首页',
				path:'/'
			},{
				title:'计划',
				path:'/plan'
			},{
				title:'证件档案',
				path:'/plan/profile'
			}
		]});
		this.getData();
	},
	methods:{
		getData:async function(){
			const id = layer.load(0);
			let data = await ajax({
				url:'/plan.php',
				data:{
					api:'getprofiles',
					search:this.search,
					page:this.page.current,
					limit:this.page.limit
				}
			});
			this.page = data.page?data.page:{};
			this.dataSource = Object.values(data.data?data.data:{});
			layer.close(id);
		},
		delProfile:function(id){
			layer.confirm("您确定要删除吗？", {
				title:'删除确认',
				btn: [
					{
						text:'确定', 
						callback: (layerid) => { 
							let ids = this.selectedKeys;
							console.log(ids);
							if(id){
								ids = [id];
							}
							layer.close(layerid);
							ajax({
								url:'plan.php',
								data:{
									api:'delprofile',
									ids:ids
								},
								success:() => {
									this.getData();
								}
							});								 
						}
					},
					{
						text:'取消', 
						callback: (layerid) => { 
							layer.close(layerid); 
						}
					}
				]
			});
		},
		showPageData:function(row){
			this.member = JSON.parse(JSON.stringify(row));
			this.showPage = true
		},
		changePage:function({ current, limit }){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		setcestatus:function(status){
			this.search.status = status
			this.getData();
		}
	}
}
</script>
<style scoped>
	.table {
		border-collapse:collapse;
		border:1px solid #aaa;
		width:100%;
		text-align: center;
	}
	.table thead{
		background-color: #fafafa;
		font-weight: bold;
	}
	.table th {
		border:1px solid #ddd;
		padding:10px;
		width:80px;
	}
	.table td {
		padding:10px;
		border:1px solid #ddd;
		min-width:80px;
	}
	.table .left{
		text-align: left;
	}
</style>