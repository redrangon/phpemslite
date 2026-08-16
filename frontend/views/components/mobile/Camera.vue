<template>
    <div style="width:100%;padding:2px;text-align: center;">
		<video ref="videoCam" muted width="90%" playsinline></video>
		<van-button type="primary" v-if="status" @click="getPhoto">点击拍摄</van-button>
    </div>
</template>
<script>
import { showSuccessToast, showFailToast,closeToast,showLoadingToast } from 'vant';
import {ref} from 'vue';
export default {
	props:['faceverify'],
    name:'myCamera',
    data(){
        return {
            url:ref(),
            defaulturl:'/src/assets/images/upload.png',
			status:ref(true)
        }
    },
	mounted(){
		this.init();
	},
    methods:{
		getPhoto:function(){
			showLoadingToast();
            let canvas = document.createElement('canvas');
			canvas.setAttribute("width","100%");
			canvas.setAttribute("height","280");
			let video = this.$refs.videoCam;
			canvas.getContext('2d').drawImage(video, 0, 0, 480, 360);
			this.url = canvas.toDataURL("image/png");
			if(this.faceverify)this.faceverify(this.url);
            else closeToast();
		},
		init:function(){
			let video = this.$refs.videoCam;
			const errBack = function(){
				showFailToast("您的电脑上没有安装摄像头")
			}
			const videoObj = {
				video: {facingModel:'user'}, //使用摄像头对象
				audio: false  //不适用音频
			};
			if(navigator.getUserMedia) { // Standarda
				navigator.getUserMedia(videoObj, function(stream) {
					video.srcObject = stream
					video.play();
				}, errBack);
			} else if(navigator.webkitGetUserMedia) { // WebKit-prefixed
				navigator.webkitGetUserMedia(videoObj, function(stream){
					let _URL=window.URL || window.webkitURL;
					video.src = _URL.createObjectURL(stream);
					video.play();
				}, errBack);
			}
			else if(navigator.mediaDevices.getUserMedia) {
				navigator.mediaDevices.getUserMedia(videoObj).then(function(stream){
					video.srcObject = stream;
					video.play();
				}).catch(errBack);
			} else if(navigator.mozGetUserMedia) { // Firefox-prefixed
				navigator.mozGetUserMedia(videoObj, function(stream){
					video.src = window.URL.createObjectURL(stream);
					video.play();
				}, errBack);
			}
		}
    }
}
</script>