<template>
	<div style="border: 1px solid #EEEEEE">
        <Toolbar
				:defaultConfig="toolbarConfig"
				:editor="editorRef"
				:mode="mode"
				style="border-bottom: 1px solid #EEEEEE"
		/>
		<Editor
				v-model="content"
				:defaultConfig="editorConfig"
				:mode="mode"
				style="height: 360px; overflow-y: hidden;"
                @onBlur="handleBlur"
                @onChange="handleChange"
                @onCreated="handleCreated"
                @onFocus="handleFocus"
		/>
	</div>
</template>
<script setup>
import '@wangeditor/editor/dist/css/style.css' // 引入 css
import { onBeforeUnmount, ref, shallowRef, onMounted } from 'vue'
import { Editor, Toolbar } from '@wangeditor/editor-for-vue'
import {layer} from "@layui/layui-vue";
import attachApi from "@/framework/api/attach.js";
import {withLayer} from "@/framework/utils/decorators.js";
const content = defineModel('content',{ default: '',type: String })
const editorRef = shallowRef()
const mode = ref("default");
const toolbarConfig = {
    toolbarKeys:[
        'sub','sup','uploadImage','insertTable','undo','redo','fullScreen'
    ]
}
const customUpload = (file, insertFn) => {
	withLayer(
			async () => {
				const formData = new FormData();
				formData.append('file', file, file.name );
				const data = await attachApi.upload(formData);
				insertFn(data.path);
			},[null,null]
	)

}
const editorConfig = {
	MENU_CONF:{
		uploadImage: {
			customUpload
		}
	}
}

// 组件销毁时，也及时销毁编辑器
onBeforeUnmount(() => {
	const editor = editorRef.value
	if (editor == null) return
	editor.destroy()
})
const handleChange = (editor) => {
    content.value = editor.isEmpty() ? "" : editor.getHtml();
}
const handleCreated = (editor) => {
	editorRef.value = editor // 记录 editor 实例，重要！
}
const handleFocus = (editor) => {
    //
}
const handleBlur = (editor) => {
    //
}
</script>