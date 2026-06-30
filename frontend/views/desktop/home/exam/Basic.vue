<template>

</template>
<script setup>
import examApi from '@/framework/api/exam.js';
import { useRouter } from 'vue-router';
import {useAuthStore} from "@/stores/auth.js";
import {onMounted,ref} from "vue";

const store = useAuthStore();
const router = useRouter();
onMounted(async() => {
	if (store.basic.basicexam.model === 2) {
		await router.replace('/desktop/home/exam/exam');
	}
	else {
		await router.replace('/desktop/home/exam/exercise');
	}
})
</script>
<style scoped>
</style>