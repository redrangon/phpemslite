<template>
	<lay-card class="pagecontent">
		<lay-card>
			<lay-tab v-model="tabCurrent" type="brief">
				<lay-tab-item id="1">
					<template #title>
						<span class="tabtitle">我的证书</span>
					</template>
					<lay-container>
						<lay-card>
							<lay-row space="20">
								<lay-col xs="8" sm="8" v-for="(cert,cid) in dataSource" :key="cid">
									<div class="plan-card pointer" @click="showCertImg(cert)">
										<div class="plan-thumb">
											<img :src="cert.cethumb" />
										</div>
										<div class="plan-content">
											<h4 class="plan-title">{{ cert.cetitle }}</h4>
											<div class="desc">
												<span>到期时间：{{cert.cemexpirytime}}</span>
											</div>
										</div>
									</div>
								</lay-col>
							</lay-row>
						</lay-card>
						<lay-page v-if="dataSource && dataSource.length > page.limit" v-model="page.current"  :layout="layout" v-model:limit="page.limit" :total="page.total"  @change="pageChange" theme="blue"></lay-page>
					</lay-container>
				</lay-tab-item>
			</lay-tab>
		</lay-card>
	</lay-card>
</template>
<script>
import certApi from '@/framework/api/cert.js';
import {layer} from '@layui/layui-vue';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {ref} from 'vue';
export default {
	mixins: [baseMixin],
	data() {
		return {
			dataSource:[],
			tabCurrent:"1",
			layout:['count', 'prev', 'page', 'next', 'refresh', 'skip'],
			page:{
				current:1,
				total:1,
				limit:9
			},
		}
	},
	async mounted() {
		await this.getData()
	},
	methods:{
		getData:async function(){
			await this.execute(async() => {
				const data = await certApi.getMyCertList({
					page:this.page.current,
					limit:this.page.limit
				});
				this.page.current = data.page;
				this.page.limit = data.limit
				this.page.total = data.total
				this.dataSource = data.data;
			},null,null)
		},
		pageChange:function({current,limit}){
			this.page.current = current
			this.page.limit = limit
			this.getData()
		},
		showCertImg:function(cert){
			this.execute(async() => {
				const data = await certApi.getMyCertImage(cert.cemid);
				layer.photos({
					imgList:[{src:data.image,alt:cert.cetitle}]
				})
			},null,null)
		}
	}
}
</script>
<style scoped>
.tabtitle{
	font-size: 16px;;
	padding-left:20px;
	padding-right: 20px;
}
.certcard{
	line-height:30px;
	background:#FFFFFF;
	border-radius: 10px;
	cursor: pointer;
}
.certcard h4{
	color:#666666;
	font-size:16px;
	line-height:40px;
	margin-top: 10px;
	font-weight: 800;
	height:40px;
	overflow:hidden;
}
.certcard .desc{
	color: #999999;
	height:30px;
	line-height:30px;
	overflow:hidden;
}
</style>