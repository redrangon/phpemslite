<template>    
    <lay-layout style="flex-direction:column;" class="cardLayout">
		<headerMenu></headerMenu>
        <lay-layout class="frontLayOut">
			<leftMenu></leftMenu>
			<lay-body class="frontLayBody">
				<router-view></router-view>
			</lay-body>
		</lay-layout>
    </lay-layout>
</template>
<script setup>
import {useAuthStore} from "@/stores/auth.js";
import headerMenu from "@/components/desktop/menu/Header.vue";
import leftMenu from "@/components/desktop/menu/Left.vue";
import {useRouter} from "vue-router";
const user = useAuthStore();
const router = useRouter();
if(!user.isLoggedIn)router.push('/desktop/home/auth/login');
</script>
<style src="@/assets/css/desktop/card.css"></style>
<style>
.tabtitle{
	font-size: 16px;;
	padding-left:20px;
	padding-right: 20px;
}
.planbox {
	width: auto;
	background-color: #ffffff;
	border-radius: 12px;
	padding: 20px;
	margin: 15px 20px;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
	transition: all 0.3s ease;
	border: 1px solid #e8e8e8;
}

.planbox:hover {
	box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
	transform: translateY(-4px);
}

.planbox .img-wrapper {
	width: 100%;
	height: 200px;
	overflow: hidden;
	border-radius: 8px;
	position: relative;
	background-color: #f5f5f5;
	margin: auto;
}

.planbox .img-wrapper img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	transition: transform 0.4s ease;
}

.planbox:hover .img-wrapper img {
	transform: scale(1.08);
}

.planbox .title {
	color: #333333;
	font-size: 18px;
	font-weight: 700;
	margin: 0 0 12px 5px;
	line-height: 2;
}

.planbox .desc {
	color: #666666;
	line-height: 1.8;
	margin-bottom: 12px;
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
	overflow: hidden;
	text-overflow: ellipsis;
}

.planbox .info-row {
	display: flex;
	align-items: center;
	margin-bottom: 10px;
	padding: 8px 12px;
	background-color: #f7f9fc;
	border-radius: 6px;
}

.planbox .info-label {
	color: #999999;
	font-size: 14px;
	font-weight: 500;
	width: 80px;
	flex-shrink: 0;
}

.planbox .info-value {
	color: #333333;
	font-size: 14px;
	font-weight: 600;
	flex: 1;
}

.planbox .price {
	color: #ff4d4f;
	font-size: 20px;
	font-weight: 700;
	letter-spacing: 1px;
}

.planbox .price-free {
	color: #52c41a;
	font-size: 18px;
	font-weight: 600;
}

.planbox .btn-wrapper {
	margin-top: 16px;
	text-align: right;
	padding-top: 12px;
	border-top: 1px solid #f0f0f0;
}
</style>