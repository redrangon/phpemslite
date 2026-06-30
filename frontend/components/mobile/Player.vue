<template>
	<div ref="video"></div>
</template>
<script>
	import videojs from "video.js";
	import "video.js/dist/video-js.css";
	import cn from 'video.js/dist/lang/zh-CN.json';
	videojs.addLanguage('zh-CN',cn);
	import {ref} from 'vue';
	export default {
		name: 'myPlayer',
		props:['settings','progress','action'],
		data() {
			return {
				time:ref(),
				player:ref(),
				default:{
					source:false,
					poster:'',
					controls:true,
					loop:false,
					volume:1,
					width:970,
					height:550,
					language:'zh-CN',
					disablePictureInPicture:true,
					children:{
						"mediaLoader":true,
						"posterImage":true,
						"titleBar":true,
						"textTrackDisplay":true,
						"loadingSpinner":true,
						"bigPlayButton":true,
						"liveTracker":true,
						"controlBar":{
							progressControl:true
						},
						"errorDisplay":true,
						"textTrackSettings":true,
						"resizeManager":true
					}				
				}
			}
		},
		mounted(){
			this.init(this.settings);
		},
		beforeDestroy() {
			this.distory();
		},
		methods:{
			distory: function(){
				if (this.player && this.player.dispose) {
					this.player.dispose();
				}
			},
			init:function(setting){
				this.distory();

				let options = this.default;
				for(let x in setting)
				{
					options[x] = setting[x];
				}
				if(this.progress)options.children.controlBar.progressControl = this.progress.progressControl;

				let source = document.createElement('source');
				source.setAttribute('src',options.source);
				let video = document.createElement('video');
				video.setAttribute('class', "video-js vjs-default-skin");
				video.setAttribute('webkit-playsinline', 'true');
				video.setAttribute('playsinline', 'true');
				video.appendChild(source);
				this.$refs.video.appendChild(video);
				const player = videojs(video,options);
				if(this.progress && this.progress.currentTime)player.currentTime(this.progress.currentTime);
                this.time = 0;
				player.on('timeupdate',() => {
					if(this.action && this.action.record){
						const time = parseInt(player.currentTime());
	                    if(time !== this.time && time % 20 === 19)
						{
							this.time = time;
							this.action.record(time,player);
						}
					}
				})
				player.on('ended',() => {
					if(this.action && this.action.finish)this.action.finish();
				})
				this.player = player;
			}
		},
		watch:{
			settings:function(value){
				this.init(value);
			}
		}
	}
</script>
<style scoped></style>