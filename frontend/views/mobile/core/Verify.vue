<template>
	<div style="width:100%;">
        <!-- 导航栏 -->
        <van-nav-bar title="实名认证" left-arrow @click-left="$router.go(-1)" placeholder fixed/>
        <div class="card-container">
	        <van-cell-group>
	            <van-cell title="请先完成实名信息后再开始学习。务必正确填写姓名和身份证号，此信息将用来填入报名信息和发放证书，提交后将无法更改。" v-if="userstatus <= 0" />
	            <van-cell title="您的实名认证申请已提交，请等待审核。" v-if="userstatus === 1" />
	            <van-cell title="您的实名认证申请被拒绝，请核验认证信息后重新提交。" v-if="userstatus === 2" />
	            <van-cell title="您已完成实名认证，如需要修改实名信息，请联系管理员。" v-if="userstatus === 3" />
	        </van-cell-group>
	        <div style="margin-top: 10px;"></div>
	        <div v-if="userstatus === 1 || userstatus === 3">
	            <van-cell-group>
	                <van-cell title="姓名" :value="usertruename" />
	                <van-cell title="身份证号" :value="userpassport" />
	                <van-cell title="照片">
	                    <template #value>
	                        <van-image :src="userphoto[0].url" width="120" />
	                    </template>
	                </van-cell>
	            </van-cell-group>
		        <div style="margin: 16px;" v-if="userstatus === 1">
			        <van-button block type="primary" @click="clearVerify">
				        撤回申请
			        </van-button>
		        </div>
	        </div>
	        <!-- 表单 -->
	        <van-form @submit="onSubmit" v-else-if="userstatus <= 0 ||  userstatus === 2">
	            <van-cell-group>
	                <!-- 姓名 -->
	                <van-field v-model="usertruename" label="姓名" placeholder="请输入姓名" required />
	                <!-- 身份证号 -->
	                <van-field v-model="userpassport" label="身份证号" placeholder="请输入身份证号" required />
	                <van-field name="userphoto" label="上传照片" required>
	                    <template #input>
	                        <!-- 照片上传 -->
	                        <van-uploader v-model="userphoto" :after-read="afterRead" accept="image/*" :multiple="false" max-count="1" preview-size="120" reupload>
	                            <template #upload>
	                                <van-button square type="primary">上传照片</van-button>
	                            </template>
	                        </van-uploader>
	                    </template>
	                </van-field>
	            </van-cell-group>
	            <!-- 提交按钮 -->
	            <div style="margin: 16px;">
	                <van-button block type="primary" native-type="submit">
	                    提交
	                </van-button>
	            </div>
	        </van-form>
        </div>
    </div>
</template>

<script>
    import { ref } from 'vue';
    import userApi from '@/framework/api/user.js';
    import attachApi from '@/framework/api/attach.js';
    import baseMixin from "@/framework/mixins/baseMixin.js";
    export default {
		mixins: [baseMixin],
        data() {
            return {
                usertruename:'',
                userpassport:'',
                userphoto:[],
                isVerify:false,
	            userstatus:0
            };
        },
        async mounted(){
            await this.getData();
        },
        methods: {
            async getData(){
	            const user = await userApi.getCurrentUser();
	            this.userpassport = user.userpassport;
	            this.usertruename = user.usertruename;
	            this.userphoto = [{url:user.userphoto??null}];
	            this.userstatus = user.userstatus;
            },
            async onSubmit() {
                const user = {
                    usertruename:this.usertruename,
                    userpassport:this.userpassport,
                    userphoto:this.userphoto[0].url
                };
                await userApi.verifyUser(user);
                await this.getData();
            },
            afterRead:async function(file) {
                const data = await attachApi.mobileUpload(file);
                this.userphoto = [{url:data.path??null}];
            },
	        clearVerify:async function(){
		        this.confirmOperate(
			        '确定要撤回申请吗？',
			        async () => {
				        await userApi.cancelVerify();
			        }, this.getData
		        );
	        },
        }
    };
</script>

<style scoped>

    /* 可添加自定义样式 */
    van-field {
        margin-bottom: 15px;
    }

    van-uploader {
        margin-bottom: 15px;
    }
</style>
