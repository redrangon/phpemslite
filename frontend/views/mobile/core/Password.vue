<template>
	<div style="width:100%;">
        <!-- 导航栏 -->
        <van-nav-bar title="修改密码" left-arrow @click-left="$router.go(-1)"  placeholder fixed/>
		<div class="card-container">
	        <!-- 表单 -->
	        <van-form @submit="onSubmit">
	            <van-cell-group class="menu-list">
	                <!-- 原密码 -->
	                <van-field v-model="oldPassword" label="原密码" type="password" placeholder="请输入原密码" required autocomplete="old-passport"/>
	                <!-- 新密码 -->
	                <van-field v-model="newPassword" label="新密码" type="password" placeholder="请输入新密码" required autocomplete="new-passport"/>
	                <!-- 确认新密码 -->
	                <van-field v-model="confirmPassword" label="确认密码" type="password" placeholder="请再次输入新密码" required autocomplete="confirm-passport"/>
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
import userApi from '@/framework/api/user.js';
import baseMixin from "@/framework/mixins/baseMixin.js";
import {showFailToast,showSuccessToast } from 'vant'
export default {
    data() {
        return {
            oldPassword: '',
            newPassword: '',
            confirmPassword: ''
        };
    },
	mixins: [baseMixin],
    methods: {
        async onSubmit() {
            // 简单的验证逻辑
            if (this.newPassword !== this.confirmPassword) {
                showFailToast('两次输入的新密码不一致');
                return;
            }
	        await this.execute(async () => {
		        await userApi.modifyPassword({
			        newpassword: this.newPassword,
			        newpassword2: this.confirmPassword,
			        oldpassword: this.oldPassword
		        });
		        showSuccessToast('密码修改成功');
		        await userApi.logout();
	        });
        }
    }
};
</script>

<style scoped>
/* 可添加自定义样式 */
van-field {
    margin-bottom: 15px;
}
.menu-list div{
	padding:20px;
	font-size: 16px;
	background: transparent;
}
</style>
