<template>
	<lay-layout class="masterLayOut">
		<leftMenu></leftMenu>
		<lay-layout>
			<lay-header class="masterHeader">
				<topMenu></topMenu>
			</lay-header>
			<lay-body>
				<bread-crumb></bread-crumb>
				<lay-card class="pagecontent">
					<lay-quote>
						<p>官方网站： <a href="http://www.phpems.net">http://www.phpems.net</a> &nbsp;&nbsp;&nbsp;&nbsp;公众号：PHPEMS</p></lay-quote>
				</lay-card>
			</lay-body>
		</lay-layout>
	</lay-layout>
</template>
<script setup>
import {ref, provide, inject} from "vue";
import leftMenu from '@/components/master/menu/Left.vue';
import topMenu from '@/components/master/menu/Top.vue';
import breadCrumb from "@/components/master/menu/Bread.vue";
const module = ref("module")
const menu = ref("1")
provide("module",module);
provide("menu",menu);
</script>