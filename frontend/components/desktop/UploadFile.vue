<template>
	<lay-space>
		<lay-space><lay-input v-model="filepath" :style="style?style:'width:360px'"></lay-input></lay-space>
		<lay-space>
			<lay-button type="primary" :loading="loadState" @click="upload_handler">
				{{btnTitle}}
			</lay-button>
		</lay-space>
	</lay-space>
</template>
<script setup>
import attachApi from "@/framework/api/attach.js";
import http from "@/framework/http"
import { ref, watch } from 'vue';

// 定义 props
const props = defineProps(['modelValue', 'filetype', 'style']);
const emit = defineEmits(['update:modelValue']);

// 响应式数据
const filepath = ref(props.modelValue);
const btnTitle = ref('上传');
const loadState = ref(false);

// 监听 filepath 变化并触发事件
watch(filepath, (value) => {
	if(value && value.length > 0){
		btnTitle.value = '重新上传';
	}
	else
	{
		btnTitle.value = '上传';
	}
	emit('update:modelValue', filepath.value);
});

// 上传处理函数
const upload_handler = () => {
	let input = document.createElement('input');
	input.setAttribute('type', 'file');
	input.setAttribute('accept', props.filetype ? props.filetype : '.zip,.rar');
	input.click();

	input.onchange = async () => {
		let formData = new FormData();
		formData.append('api', 'upload');
		formData.append('file', input.files[0], input.files[0].name);
		try{
			let res = await attachApi.upload(formData,{
				onUploadProgress: (e) => {
					loadState.value = true;
					btnTitle.value = '已上传' + parseInt(e.progress * 100) + '%';
				}
			});
			filepath.value = res.path ? res.path : null;
		}catch (e) {
			filepath.value = null;
		}finally {
			loadState.value = false;
		}
	};
};
</script>