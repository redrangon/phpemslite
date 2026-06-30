<script setup lang="ts">
</script>
<template>
	<lay-layout class="login_container">
		<lay-container style="margin-top:15vh;width: 1100px;">
			<lay-row space="30">
				<lay-col sm="15" xs="15">
					<lay-space direction="vertical" fill wrap>
						<h1 style="line-height:80px;color:#FFFFFF;font-weight: 600;font-size: 36px">
							PHPEMS 12 LITE
						</h1>
                        <p style="margin-bottom: 40px;font-size: 20px;color:#FFFFFF;font-weight: 600;">基于PHP + VUE开发的开源在线考试学习系统</p>
						<lay-row space="30">
							<lay-col xs="8" sm="8"><img src="../../../../assets/images/pic1.png"></lay-col>
							<lay-col xs="8" sm="8" style="text-align: center;"><img src="../../../../assets/images/pic2.png">
							</lay-col>
							<lay-col xs="8" sm="8" style="text-align: right;"><img src="../../../../assets/images/pic3.png">
							</lay-col>
						</lay-row>
						<lay-space class="textinfo" direction="vertical" fill wrap>
							<lay-space>推荐浏览器 ： Firefox、 Chrome、 Edge</lay-space>
							<lay-space>推荐分辨率 ： 1366 * 768 及以上</lay-space>
						</lay-space>
					</lay-space>
				</lay-col>
				<lay-col xs="1" sm="1"></lay-col>
				<lay-col sm="8" xs="8">
					<router-view />
				</lay-col>
			</lay-row>
		</lay-container>
		<lay-footer class="footer">
			Copyright&nbsp;&nbsp;2024-2026&nbsp;&nbsp;新乡市落笔千言网络技术有限公司
		</lay-footer>
	</lay-layout>
</template>
<style scoped>
@import "@/assets/css/desktop/auth.css";
.textinfo{
	padding:10px 0px;
	background-color: rgba(0,0,0,0.3);
	color:#FFFFFF;
	text-align: center;
}
</style>