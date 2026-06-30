<template>
	<div style="width:100%;">
        <!-- 页面导航栏 -->
        <van-nav-bar title="用户信息修改" left-arrow @click-left="$router.back()" placeholder fixed/>
		<div class="card-container">
	        <!-- 表单容器 -->
	        <van-form @submit="onSubmit">
	            <!-- 头像上传 -->
	            <van-cell-group class="menu-list">
	                <!-- 性别选择 -->
	                <van-field v-model="gender" label="性别" :options="genderOptions" placeholder="请选择性别" @click="showGenderPopup = true" />

	                <!-- 地址输入框 -->
	                <van-field v-model="address" label="地址" placeholder="请输入地址" clearable />
	                <!-- 手机号输入框 -->
	                <van-field v-model="phone" label="手机号" placeholder="请输入手机号" clearable type="tel" />
	            </van-cell-group>
	            <!-- 提交按钮 -->
	            <div style="margin: 16px;">
	                <van-button block type="primary" native-type="submit">
	                    提交修改
	                </van-button>
	            </div>
	        </van-form>
		</div>
		<van-popup v-model:show="showGenderPopup" position="bottom">
			<van-picker :columns="genderOptions" @confirm="onGenderConfirm" @cancel="showGenderPopup = false" />
		</van-popup>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import userApi from '@/framework/api/user.js';
import {withLayer,withConfirm} from "@/framework/utils/mobile/decorators.js";

// 性别
const gender = ref('');
// 性别选项
const genderOptions = ref([{text:'男',value:'男'}, {text:'女',value:'女'}]);
// 显示性别选择弹窗
const showGenderPopup = ref(false);
// 地址
const address = ref('');
// 手机号
const phone = ref('');
// 确认性别选择
const onGenderConfirm = (value) => {
    gender.value = value.selectedValues[0];
    showGenderPopup.value = false;
};

// 提交表单
const onSubmit = async () => {
	await withLayer(async () => {
		const user = {
			usergender: gender.value,
			useraddress: address.value,
			userphone: phone.value
		};
		await userApi.modifyProfile(user);
	})
};

onMounted(async () => {
	const user = await userApi.getCurrentUser();
    gender.value = user.usergender;
    phone.value = user.userphone;
    address.value = user.useraddress;
});


</script>

<style scoped>
.menu-list div{
	padding:20px;
	font-size: 16px;
	background: transparent;
}
</style>