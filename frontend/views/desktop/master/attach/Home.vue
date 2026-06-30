<template>
	<lay-card class="pagecontent">
		<lay-quote>本模块用于对上传文件的配置及管理</lay-quote>
	</lay-card>
</template>
<style scoped></style>
<script>
export default {
	data() {
		return {
			multiple:true,
			news:[]
		}
	},
	emits: ['setVal'],
	created() {
		this.$emit('setVal',{bcmus:[{
			title:'首页',
			path:'/'
		},{
			title:'附件',
			path:'/attach'
		}]});
	},
	methods:{
		//
	}
}
</script>