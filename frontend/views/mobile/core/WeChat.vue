<template>
    <div class="page">
        <!-- 页面导航栏 -->
        <van-nav-bar title="用户信息修改" left-arrow @click-left="$router.back()" placeholder fixed/>
        <div style="margin-top: 10px;"></div>
        <van-cell-group inset>
            <van-cell title="用户名" :value="user.username" />
            <van-cell title="身份证号" :value="user.userpassport" />
        </van-cell-group>
        <div style="margin: 16px;">
            <van-button round block type="primary" v-if="wechatEnv && useropenid" @click="unBindWechat()">
                解除微信绑定
            </van-button>
            <van-button round block type="primary"  v-if="wechatEnv && !useropenid" @click="bindWechat()">
                绑定本账号到此微信
            </van-button>
        </div>
    </div>
</template>
<script>
    import userFunc from '@/framework/api/user.js'; 
    import wechat from '@/framework/api/wechat.js'; 
    import { ref } from 'vue';
    export default{
        data(){
            return{
                wechatEnv:ref(false),
                useropenid:ref(false)
            }
        },
        props:['user'],
        async created(){
            this.wechatEnv = wechat.isWechatEnv();
            await this.loadInfo();
            if(wechat.isWechatEnv())
            {
                let code = this.$route.query.code;
                const urlParams = new URLSearchParams(window.location.search);
                if(!code)
                {                
                    code = urlParams.getAll('code').pop();
                }            
                if(code && code != '')
                {
                    await this.bindWechat(code);
                    this.$router.go(-1);
                }
            }
        },
        methods:{    
            async loadInfo(){
                if(this.wechatEnv){
                    const data = await userFunc.getUserInfo();
                    this.useropenid = data.user.useropenid;
                }
            },        
            async bindWechat(code = null){
                await wechat.bindWechat(code);
                await this.loadInfo();
            },
            async unBindWechat(){
                await wechat.unBindWechat();
                await this.loadInfo();
            }
        }
	}
</script>

<style scoped>
/* 可以添加自定义样式 */
</style>