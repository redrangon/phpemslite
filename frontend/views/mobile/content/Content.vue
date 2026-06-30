<template>
	<div style="width:100%;">
        <van-nav-bar :title="content.contenttitle" left-arrow @click-left="$router.go(-1)"  placeholder fixed/>
		<div class="card-container">
	        <van-cell-group>
	            <h2 class="title" style="padding:15px 10px">{{ content.contenttitle }}</h2>
	            <div style="text-align: center;color:#999999;margin-top: 10px;">{{ content.contentinputtime }}</div>
	            <div class="desc" v-html="content.contenttext"></div>
	        </van-cell-group>
        </div>
    </div>
</template>

<script>
import baseMixin from "@/framework/mixins/baseMixin.js";
import contentApi from "@/framework/api/content.js";
export default {
	mixins: [baseMixin],
    data() {
        return {
	        contentId:0,
            content:{}
        };
    },
    async mounted() {
		this.contentId = this.$route.params.contentid;
        await this.getData();
	},
    methods: {
        getData:async function(){
	        await this.execute( async () =>{
		        const data = await contentApi.getContent(this.contentId);
		        if(!data){
			        this.$router.go(-1)
		        }
		        this.content = data;
		    },null,null);
		}
    }
};
</script>

<style scoped>
.title{
	font-size: 18px;
	font-weight: bold;
    text-align: center;	
    padding:0px 15px;
}
.desc{
	padding:10px 15px;
	font-size: 16px;
	line-height: 2;
}
</style>
