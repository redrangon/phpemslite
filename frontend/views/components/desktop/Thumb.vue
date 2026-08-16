<template>
    <div style='width:240px;height:180px;border:1px dotted #DDDDDD;padding:2px;position:relative;'>
	    <div v-if="loading">
		    <lay-loading></lay-loading>
	    </div>
	    <img :src="url?url:defaulturl" @click="upload_handler" style="width: 100%;height:100%;object-fit: cover;" v-else>
	    <span style="position: absolute;right:5px;top:5px;z-index:99;font-size: 16px;" @click.stop="clearThumb">
		    <lay-icon type="layui-icon-clear"></lay-icon>
	    </span>
    </div>
</template>
<script>
import attachApi from "@/framework/api/attach.js";
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
	props:['src'],
	emits: ['update:src'],
    name:'myThumb',
    data(){
        return {
            url:ref(),
            defaulturl:'@/assets/images/upload.png',
	        loading:ref(false)
        }
    },
	mounted(){
		this.url = this.src;
	},
    methods:{
        upload_handler: function(){
            let input = document.createElement('input');
			input.setAttribute('type', 'file');
			input.setAttribute('accept', '.jpg,.png');
			input.click();
			input.onchange = async() => {
				const formData = new FormData();
				formData.append('api','upload');
				formData.append('file', input.files[0], input.files[0].name );
				try{
					let res = await attachApi.upload(formData,{
						onUploadProgress: (e) => {
							this.loading = true;
						}
					});
					this.url = res.path ??null;
				}catch (e) {
					this.url = null;
					layer.msg('上传失败，请稍后重试')
				}finally {
					this.loading = false;
				}
			};
        },
	    clearThumb:function(){
		    this.url = '';
	    }
    },
	watch:{
		url:function(){
			this.$emit('update:src', this.url);
		},
		src:function(){
			this.url = this.src;
		}
	}
}
</script>