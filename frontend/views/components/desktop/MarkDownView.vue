<template>
    <div ref="previewEl" class="vditor-preview"></div>
</template>

<script setup>
    import { onMounted, ref, watch } from 'vue'
    // ✅ 正确导入 Vditor（默认导入）
    import Vditor from 'vditor'
    // ✅ 引入样式
    import 'vditor/dist/index.css'

    const props = defineProps({
        content: {
            type: String,
            default: ''
        }
    })

    const previewEl = ref(null)

    // ✅ 使用 Vditor.preview（静态方法）
    const renderPreview = () => {
        if (!previewEl.value) return
        // 清空内容
        previewEl.value.innerHTML = ''
        // 调用静态方法预览
        Vditor.preview(
            previewEl.value,
            props.content || '',
            {
                cdn: 'public/vditor',
                mode: 'light',
                hljs: {
                    style: 'github'
                },
                math: {
                    engine: 'KaTeX'
                },
                mermaid: true,
                anchor: 0
            }
        )
    }

    onMounted(() => {
        renderPreview()
    })

    watch(() => props.content, renderPreview)
</script>

<style scoped></style>