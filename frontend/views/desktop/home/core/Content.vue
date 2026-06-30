<template>
	<lay-card class="pagecontent">
		<h2 class="title">
			<lay-icon type="layui-icon-return" @click="$router.go(-1)" style="cursor: pointer;"></lay-icon> 培训通知
		</h2>
		<lay-card v-if="content" class="content">
			<h2>{{ content.contenttitle }}</h2>
			<p style="text-align: center;color:#999999;">{{ content.contentinputtime }}</p>
			<lay-line></lay-line>
			<div v-html="content.contenttext" class="content"></div>
		</lay-card>
	</lay-card>
</template>
<script>
	import ajax from '@/api/app';
	import {layer} from '@layui/layui-vue';
	import { ref } from 'vue';
	export default{
		data(){
			return {
				contentid:ref(),
				content:ref()
			}
		},
		emits:['setVal'],
		created() {
			this.contentid = this.$route.params.contentid;
			this.getData();
			this.$emit('setVal',{bcmus:{
				title:'培训通知',
				back:true
			}});
		},
		methods:{
			getData:async function(){
				const id = layer.load(0);
				let data = await ajax({
					url:'plan.php',
					data:{
						api:'getcontent',
						contentid:this.contentid
					}
				});
				this.content = data?data:{};
				layer.close(id);
			}
		}
	}
</script>
<style scoped>
.layui-card{
	border-radius: 5px;
}

.planbox{
	line-height:30px;
	color:#999999;
}
.planbox .pimg{
	width: 100%;
}
.planbox .pimg img{
	width: 100%;
	height:140px;
	border-radius: 5px;
}
.planbox h4{
	color:#333333;
	font-size:14px;
	line-height:40px;
	font-weight: bold;
}
.content{
	line-height:2;
	font-size: 16px;;
}
.content h2{
	padding:10px 0;
	text-align: center;
}
</style>