<template>
	<div class="page">
        <van-nav-bar title="证书验证" placeholder fixed></van-nav-bar>
		<van-space direction="vertical" fill v-if="ceq" style="margin-top:20px;">
			<van-cell-group inset v-if="infoshow">
				<van-cell title="姓名" :value="ceq.member.pmname" />
				<van-cell title="身份证号" :value="ceq.member.pmpassport" />
				<van-cell title="性别" :value="ceq.member.pmsex" />
				<van-cell title="学历" :value="ceq.member.pmedu" />
				<van-cell title="参加培训" :value="ceq.member.planname" />
				<van-cell title="证书名称" :value="ceq.ce.cetitle" />
				<van-cell title="证书编号" :value="ceq.ceqsn" v-if="ceq.ceqsn" />
				<van-cell title="发证时间" :value="ceq.ceqtime" />
				<van-cell title="证书有效期至" :value="ceq.ceqexpiretime" />
			</van-cell-group>
            <van-cell-group inset v-else>
                <van-form @submit="onSubmit">
                    <van-cell-group inset>
                        <van-span style="line-height:40px;">请输入身份证号进行验证：</van-span>
                    </van-cell-group>
                    <van-cell-group inset>
                        <van-field v-model="formData.passport" name="身份证号" placeholder="请输入身份证号" :rules="[{ required: true, message: '请输入身份证号' }]" required/>
                    </van-cell-group>
                    <div style="margin: 16px;">
                        <van-button round block type="primary" native-type="submit">开始验证</van-button>
                    </div>
                </van-form>
            </van-cell-group>
		</van-space>
        <van-space direction="vertical" fill v-else style="margin-top:20px;">没有找到相关证书</van-space>
	</div>
</template>
<style scoped>
.title{
    font-size: 1.2em;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px
}
</style>
<script>
    import plan from '@/framework/api/plan.js'; 
import { showToast } from 'vant';
    import { ref } from 'vue';
    export default{
        data(){
            return{
                ceqid:ref(),
                ceq:ref(),
                infoshow:ref(false),
                formData:ref({})
            }
        },
        async created(){
            this.ceqid = this.$route.params.ceqid;
            await this.getCeq();
        },
        methods:{            
            getCeq:async function(){ 
                let data = await plan.getCeq({
                    ceqid:this.ceqid,
                });
                this.ceq = data.ceq
            },
            onSubmit() {
                if(this.formData.passport == this.ceq.ceqpassport){
                    this.infoshow = true;
                }else{
                    showToast('身份证号不匹配，请核对后重新输入');
                }
            },
		}
	}
</script>
