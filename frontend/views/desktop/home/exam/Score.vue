<template>
	<lay-card class="pagecontent">
		<lay-card><paperMenu></paperMenu></lay-card>
		<lay-card v-if="isready">
			<lay-panel shadow="never" v-if="page.total > 0">
				<div class="sectionitem">
					您的最高分： <span style="color:#1E9FFF">{{ topscore.score }}</span> 分；您的最好名次：第 <span style="color:#1E9FFF">{{ topscore.index }}</span> 名。
				</div>
			</lay-panel>
			<table class="table">
				<thead>
					<td width="80">名次</td>
					<td>姓名</td>
					<td>得分</td>
					<td>考试时间</td>
					<td width="120">用时</td>
				</thead>
				<template v-for="(score,sid) in scores" :key="sid">
				<tr>
					<td>第 <span style="color:#1E9FFF">{{ (page.current - 1) * page.limit + (sid + 1) }}</span> 名</td>
					<td>{{ score.usertruename }}</td>
					<td>{{ score.ehscore }}</td>
					<td>{{ score.ehstarttime }}</td>
					<td>{{ timeFormat(score.ehtime) }}</td>
				</tr>
				</template>
			</table>
		</lay-card>
		<lay-page v-if="page.total > page.limit" v-model="page.current" theme="blue" :limit="page.limit" :total="page.total" style="float:right;" @change="changePage"></lay-page>
	</lay-card>
</template>
<script>
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
import paperMenu from '@/components/core/papermenu.vue';
export default {
	data() {
		return {
			page:{current:1,limit:10,total:1},
			scores:ref(),
			topscore:ref(),
			basic:ref(),
			isready:ref(false)
		}
	},
	async mounted(){
		await this.getData();
		this.isready = true
		this.$emit('setVal',{bcmus:{
			title:this.basic.basic,
			back:true
		}});
	},
	components:{paperMenu},
	methods:{
		getData:async function(){
			const id = layer.load(0);
			let data = await ajax({
				url:'/exam.php',
				data:{
					api:'getscores',
					page:this.page.current,
					limit:this.page.limit
				}
			});
			this.scores = data.scores?data.scores.data:{};
			this.topscore = data.topscore?data.topscore:{};
			this.basic = data.basic?data.basic:{};
			this.page = data.scores?data.scores.page:{};
			layer.close(id);
		},
		changePage:function({current,limit}){
			this.page.current = current;
			this.page.limit = limit;
			this.getData();
		},
		timeFormat:function(time){
			let format = 0;
			if(time >= 60){
				if(time % 60){
					format = parseInt(time / 60) +'分'+ time % 60 + '秒'
				}else{
					format = parseInt(time / 60) +'分'
				}
			}else{
				format = time + '秒'
			}
			return format;
		}
	}
}
</script>
<style scoped>

.sectiontitle{
	font-size:16px;
	line-height: 2;
	border-bottom: 1px solid #fafafa;
	margin-bottom:10px;
	width: 100%;
}
.sectionitem{
	line-height:2;
	font-size:16px;
	border-bottom:1px solid #fafafa;
}
.sectionitem span{
	color:#999999;
	margin-left:10px;
}
.sectionitem button{
	float:right;
}
.table {
	border-collapse:collapse;
	border:1px solid #aaa;
	width:100%;
	text-align: center;
	margin-bottom:20px;
	font-size: 16px;
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