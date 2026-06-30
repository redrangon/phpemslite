<template>
	<div style="width:100%;">
        <!-- 导航条 -->
        <van-nav-bar title="我的证书" left-arrow @click-left="$router.go(-1)" placeholder fixed/>
        <div class="card-container">
            <!-- 证书列表 -->
	        <van-list v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoad" v-if="page?.total > 0">
	            <van-cell-group class="menu-list">
	                <van-cell v-for="(cert,cid) in dataSource" :key="cid" :title="cert.cetitle" @click="showCertImg(cert)">
                        <template #title>
                            <div>{{cert.cetitle}}</div>
                        </template>
	                    <template #icon>
	                        <img :src="cert.cethumb" alt="证书缩略图" class="thumb" />
	                    </template>
                        <template #label>
                            <div style="font-size: 14px;">到期时间 {{cert.cemexpirytime}}</div>
                        </template>
	                </van-cell>
	            </van-cell-group>
	        </van-list>
	        <van-empty description="当前没有证书" v-else></van-empty>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue';
import certApi from '@/framework/api/cert.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
import { showImagePreview  } from 'vant';
export default {
	mixins: [baseMixin],
    data() {
        return {
	        dataSource:[],
            loading: false,
            finished: false,
            page:{
				current:1,
	            total:1,
	            limit:10
            },
        };
    },
    async mounted() {
		await this.getData()
	},
    methods: {
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
        onLoad:async function() {
            await this.execute(async() => {
                const data = await certApi.getMyCertList({
                    page:this.page.current + 1,
                    limit:this.page.limit
                });
                setTimeout(() => {
                    this.page.current = data.page
                    this.page.limit = data.limit
                    this.page.total = data.total
                    data.data.map((item) => {
                        this.dataSource.push(item);
                    })
                    this.loading = false;
                    if(this.dataSource.length >= this.page.total){
                        this.finished = true;
                    }
                }, 100);
            },null,null)
        },
	    showCertImg:function(cert){
		    this.execute(async() => {
			    const data = await certApi.getMyCertImage(cert.cemid);
			    showImagePreview([data.image]);
		    },null,null)

		}
    }
};
</script>

<style scoped>
.thumb {
    width: 60px;
    object-fit: cover;
    margin: 5px auto;
}
.menu-list div{
	padding:10px 20px;
	font-size: 16px;
	background: transparent;
}
</style>
