<template>
    <lay-space style="width:480px;border:1px dotted #DDDDDD;padding:2px;text-align: center;" direction="vertical">
		<video ref="videoCam" muted autoplay playsinline width="480" height="360"></video>
		<lay-button type="primary" v-if="status" @click="getPhoto" :disabled="disabled">点击拍摄</lay-button>
    </lay-space>
</template>
<script>
import {layer} from '@layui/layui-vue';
import {ref} from 'vue';
export default {
	props:['faceverify'],
    name:'myCamera',
    data(){
        return {
            url:'',
            defaultUrl:'/src/assets/images/upload.png',
			status:true,
	        disabled:false,
	        mediaStream: null
        }
    },
	beforeUnmount() {
		this.stopCamera();
	},
	mounted(){
		this.init();
	},
    methods:{
	    stopCamera() {
		    if (this.mediaStream) {
			    // 获取流中的所有轨道（视频轨、音频轨）并逐一停止
			    this.mediaStream.getTracks().forEach(track => {
				    track.stop();
			    });
			    // 清空视频源的绑定
			    if (this.$refs.videoCam) {
				    this.$refs.videoCam.srcObject = null;
			    }
			    this.mediaStream = null;
			}
	    },
		getPhoto:async function(){
			try {
				this.disabled = true;
				const canvas = document.createElement('canvas');
				canvas.setAttribute("width", 480);
				canvas.setAttribute("height", 360);
				const video = this.$refs.videoCam;

				// 确保视频有画面后再截图
				if (video.readyState === video.HAVE_ENOUGH_DATA) {
					canvas.getContext('2d').drawImage(video, 0, 0, 480, 360);
					this.url = canvas.toDataURL("image/png");

					if (this.faceverify) {
						await this.faceverify(this.url);
					}
				} else {
					throw new Error('视频流尚未准备就绪，请稍后再试');
				}
			} catch (e) {
				layer.msg(e.message || '操作失败，请重试', { icon: 2 });
				this.disabled = false;
			} finally {
				// 拍照完成后，如果业务需要可以保持摄像头开启，或者在这里调用 this.stopCamera()
				// this.disabled = false; // 如果拍照后允许重拍，取消此行注释
			}
		},
		init:function(){
			const video = this.$refs.videoCam;
			const videoObj = {
				video: {
					facingMode: 'user',
					width: { ideal: 480 },
					height: { ideal: 360 }
				},
				audio: false
			};
			// 仅保留现代标准 API，移除过时的浏览器前缀
			if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
				navigator.mediaDevices.getUserMedia(videoObj)
					.then((stream) => {
						this.mediaStream = stream; // 保存流对象
						video.srcObject = stream;
						video.play().catch(err => {
							console.error("视频播放失败:", err);
							layer.msg("视频流启动失败", { icon: 2 });
						});
					})
					.catch((err) => {
						console.error("摄像头调用失败:", err);
						let errorMsg = "无法访问摄像头";
						if (err.name === 'NotAllowedError') {
							errorMsg = "您已拒绝摄像头权限，请在浏览器设置中允许访问";
						} else if (err.name === 'NotFoundError') {
							errorMsg = "未检测到摄像头设备";
						}
						layer.msg(errorMsg, { time: 3000, icon: 2 });
					});
			} else {
				layer.msg("当前浏览器不支持摄像头访问，请使用Chrome或Edge等现代浏览器", { icon: 2 });
			}
		}
    }
}
</script>