<template>
	<lay-card>
		<lay-space size="lg">
			<lay-space></lay-space>
			<lay-space>
				<span style='width:70px'> 附件ID：</span><lay-input v-model="search.attid"></lay-input>
			</lay-space>
			<lay-space>
				<span style='width:70px'> 起止时间：</span>
				<lay-date-picker v-model="search.range" range :placeholder="['开始日期','结束日期']" :allow-clear="true"></lay-date-picker>
			</lay-space>
			<lay-space>
				<lay-button type="primary" @click="getData">搜索</lay-button>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-table :default-toolbar="false" :columns="columns" :data-source="dataSource" ref="table" id="attid" v-model:selected-keys="selectedKeys" even>
			<template #footer>
				<lay-col md="12">
					<lay-button type="primary" size="sm" @click="deleteData()" :disabled="selectedKeys.length < 1">删除选中数据</lay-button>
				</lay-col>
				<lay-col md="12">
					<lay-page v-model="page.current" v-model:limit="page.limit" :total="page.total" :layout="layout" @change="changePage" style="float:right;"></lay-page>
				</lay-col>
			</template>
			<template v-slot:operator="{ row }">
				<lay-button size="xs" type="danger" @click="deleteData(row.attid)">删除</lay-button>				
			</template>
		</lay-table>
	</lay-card>
</template>
<style scoped>
</style>
<script>
import attach from '@/framework/api/attach.js';
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
	data() {
		return {
			columns:attach.columns.default,
			dataSource:ref(),
			selectedKeys:ref(),
			page:{ current: 1, limit: 20, total: 0 },
			search:{},
			layout:ref(['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip'])
		}
	},
	emits: ['setVal'],
	async created() {
		this.$emit('setVal',{bcmus:[{
			title:'首页',
			path:'/'
		},{
			title:'附件',
			path:'/attach'
		},{
			title:'附件管理',
			path:'/attach/type'
		}]});
		await this.getData();
	},
	methods:{
		getData:async function(){
			const id = layer.load(0);
			const data = await attach.getAttachList({
				limit:this.page.limit,
				page:this.page.current,
				search:this.search
			})
			this.page = data.page;
			this.dataSource = data.data;
			layer.close(id);
		},
		changePage:function(){
			this.getData();
		},
		deleteData:function(id){
			let ids = this.selectedKeys;
			if(id){
				ids = [id];
			}
			attach.delAttach(ids,this.getData)					
		}
	}
}
</script>