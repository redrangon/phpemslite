<template>
	<div>
		<editor :modelValue="content" :init="init"></editor>
	</div>
</template>
<script>
import tinymce from 'tinymce'
import Editor from '@tinymce/tinymce-vue'
import attachApi from "@/framework/api/attach.js";
import 'tinymce/models/dom'; // 引入dom模块。从 Tinymce6，开始必须有此模块导入
import 'tinymce/themes/silver'; //默认主题
import 'tinymce/icons/default'; //引入编辑器图标icon，不引入则不显示对应图标
import 'tinymce/langs/zh_CN'; //引入编辑器语言包
 
/* 引入编辑器插件
 * 位于 ./node_modules/tinymce/plugins 目录下，版本不同，插件会有所差异。根据自己版本来导入，若不存在的，不能导入，会报错。
 */
import 'tinymce/skins/ui/oxide/skin.min.css'
import 'tinymce/plugins/advlist'; //高级列表
import 'tinymce/plugins/anchor'; //锚点
import 'tinymce/plugins/autolink'; //自动链接
import 'tinymce/plugins/autoresize'; //编辑器高度自适应,注：plugins里引入此插件时，Init里设置的height将失效
import 'tinymce/plugins/autosave'; //自动存稿
import 'tinymce/plugins/charmap'; //特殊字符
import 'tinymce/plugins/code'; //编辑源码
import 'tinymce/plugins/codesample'; //代码示例
import 'tinymce/plugins/directionality'; //文字方向
import 'tinymce/plugins/emoticons'; //表情
import 'tinymce/plugins/fullscreen'; //全屏
import 'tinymce/plugins/help'; //帮助
import 'tinymce/plugins/image'; //插入编辑图片
import 'tinymce/plugins/importcss'; //引入css
import 'tinymce/plugins/insertdatetime'; //插入日期时间
import 'tinymce/plugins/link'; //超链接
import 'tinymce/plugins/lists'; //列表插件
import 'tinymce/plugins/media'; //插入编辑媒体
import 'tinymce/plugins/nonbreaking'; //插入不间断空格
import 'tinymce/plugins/pagebreak'; //插入分页符
import 'tinymce/plugins/preview'; //预览
import 'tinymce/plugins/quickbars'; //快速工具栏
import 'tinymce/plugins/save'; //保存
import 'tinymce/plugins/searchreplace'; //查找替换
import 'tinymce/plugins/table'; //表格
import 'tinymce/plugins/visualblocks'; //显示元素范围
import 'tinymce/plugins/visualchars'; //显示不可见字符
import 'tinymce/plugins/wordcount'; //字数统计
import 'tinymce/plugins/fullscreen';
import {withLayer} from "@/framework/utils/decorators.js";

export default {
	components: {
		Editor
	},
	props:['content'],
	emits: ['update:content'],
	data () {
		return {
			init: {
				license_key: 'gpl',
				base_url: '/tinymce/',
				language: 'zh_CN', // 中文包名称
				skin_url: '/tinymce/skins/ui/oxide', // skin路径
				content_css: '/tinymce/skins/content/default/content.css',
				skin:'oxide-dark',
				height: 400, // 设置高度
				plugins: 'lists image media table wordcount fullscreen link code',
				toolbar: 'code undo redo formatselect bold italic forecolor backcolor alignleft aligncenter alignright bullist numlist lists image link media table removeformat fullscreen',
				branding: false,
				promotion: false,
				menubar: false,
				theme: 'silver',
                valid_elements: 'math[*],semantics[*],mglyph[*],mspace[*],mi[*],mo[*],mn[*],ms[*],mtext[*],menclose[*],merror[*],mfenced[*],mfrac[*],mpadded[*],mphantom[*],mroot[*],mrow[*],msqrt[*],mstyle[*],mover[*],msup[*],msub[*],msubsup[*],munder[*],munderover[*],annotation[*],img[*],p[*],div[*],table[*],th[*],tr[*],td[*],a[*],video[*]',  // 允许span标签及所有属性
				images_upload_handler: async function(blobInfo,progress){
					const formData = new FormData();
					formData.append('api','upload');
					formData.append('file', blobInfo.blob(), blobInfo.filename());
					const data = await attachApi.upload(formData)
					return data.path;
				},
				file_picker_callback: function (callback, value, meta) {
					//文件分类
					let filetype='.pdf, .txt, .zip, .rar, .7z, .doc, .docx, .xls, .xlsx, .ppt, .pptx, .mp3, .mp4';
					//后端接收上传文件的地址
					//为不同插件指定文件类型及后端地址
					switch(meta.filetype){
						case 'image':
							filetype='.jpg, .jpeg, .png, .gif';
							break;
						case 'media':
							filetype='.mp3, .mp4';
							break;
						case 'file':
						default:
					}
					//模拟出一个input用于添加本地文件
					let input = document.createElement('input');
					input.setAttribute('type', 'file');
					input.setAttribute('accept', filetype);
					input.click();
					input.onchange = function() {
						const formData = new FormData();
						formData.append('api','upload');
						formData.append('file', this.files[0], this.files[0].name );
						withLayer(
								async () => {
									const data = await attachApi.upload(formData)
									callback(data.path);
								},[null,null]
						)
					};
				}
			}
		}
	},
	mounted () {
		//tinymce.init(this.init)
	},
	watch:{
		content:function(){
			this.$emit('update:content', this.content);
		}
	}
}
</script>