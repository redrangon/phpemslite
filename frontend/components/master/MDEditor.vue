<template>
	<div v-if="loading">
		<lay-loading></lay-loading>
	</div>
	<div ref="vditorRef" class="vditor-container"></div>
</template>

<script setup>
import { onMounted, ref, onUnmounted, watch } from 'vue'
import attachApi from "@/framework/api/attach.js";
import Vditor from 'vditor'
import 'vditor/dist/index.css'
import 'vditor/dist/js/icons/ant.js'
import {layer} from "@layui/layui-vue";
const vditorRef = ref(null)
const vditorInstance = ref(null)
const props = defineProps({
    content: {
        type: String,
        default: ''
    }
})
const emit = defineEmits(['update:content'])
const loading = ref(true);
watch(
    () => props.content,
    (newVal) => {
        if (vditorInstance.value && newVal !== vditorInstance.value.getValue()) {
            vditorInstance.value.setValue(newVal)
        }
    }
)
onMounted(() => {
    vditorInstance.value = new Vditor(vditorRef.value, {
		value: props.content,
        icon: 'ant',
        cdn: 'public/vditor',
        mode: 'ir', // 'sv' | 'wysiwyg'
        height: 500,
        placeholder: '开始输入...',
        toolbarConfig: {
            pin: true, // 固定工具栏
        },
        customWysiwygToolbar:{},
        cache: {
            enable: false, // 开发时关闭缓存
        },
        upload: {
            url: '/api/upload', // 自定义上传接口
            fieldName: 'file',
            max: 10 * 1024 * 1024, // 10MB
            accept: 'image/*',
            async handler(file) {
                const formData = new FormData()
                formData.append('file', file[0]);
				try{
					const response = await attachApi.upload(formData);
					vditorInstance.value.insertValue(`![${file[0].name}](${response.path})`);
				}
				catch (e) {
					layer.msg(e.msg || '上传失败')
				}
            }
        },
        after: () => {
	        loading.value = false;
        },
        input: (value) => {
            // 内容变化时触发 v-model 更新
            emit('update:content', value)
        }
    })
})

onUnmounted(() => {
    if (vditorInstance.value) {
        vditorInstance.value.destroy()
        vditorInstance.value = null
    }
})
</script>

<style scoped>
.vditor-container {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
	line-height: 2.5;
}
</style>