<template>
    <div ref="videoContainer" class="video-container">
        <video
            ref="videoElement"
            class="video-js vjs-default-skin"
            webkit-playsinline="true"
            playsinline="true"
        ></video>
    </div>
</template>

<script>
import videoJs from "video.js";
import "video.js/dist/video-js.css";
import cn from 'video.js/dist/lang/zh-CN.json';

videoJs.addLanguage('zh-CN', cn);

export default {
    name: 'VideoPlayer',
    props: {
        settings: {
            type: Object,
            default: () => ({})
        },
        progress: {
            type: Object,
            default: () => ({
                progressControl: true,
                currentTime: 0
            })
        },
        action: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            time: 0,
            player: null,
            defaultConfig: {
                controls: true,
                loop: false,
                volume: 1,
                width: 760,
                height: 480,
                language: 'zh-CN',
                disablePictureInPicture: true,
                children: {
                    mediaLoader: true,
                    posterImage: true,
                    titleBar: true,
                    textTrackDisplay: true,
                    loadingSpinner: true,
                    bigPlayButton: true,
                    liveTracker: true,
                    controlBar: {
                        progressControl: true
                    },
                    errorDisplay: true,
                    textTrackSettings: true,
                    resizeManager: true
                }
            }
        }
    },
    mounted() {
        console.log('mounted');
        this.init(this.settings);
    },
    beforeUnmount() {
        console.log('destroy');
        this.destroy();
    },
    methods: {
        /**
         * 销毁播放器实例
         */
        destroy() {
            if (this.player) {
                this.player.dispose();
                this.player = null;
            }
        },

        /**
         * 控制进度条的可拖拽性（核心方法）
         * @param {boolean} enabled - true: 可拖动, false: 禁止拖动
         */
        setProgressControl(enabled) {
            if (!this.player) return;

            // 获取进度控制组件
            const controlBar = this.player.controlBar;
            if (!controlBar) return;

            const progressControl = controlBar.progressControl;
            if (!progressControl) return;

            const el = progressControl.el();
            if (!el) return;

            if (enabled) {
                // ✅ 启用进度条
                el.style.pointerEvents = 'auto';
                el.style.opacity = '1';
                el.style.cursor = 'pointer';
                progressControl.removeClass('vjs-disabled');

                // 移除阻止事件
                progressControl.off('mousedown', this.preventSeek);
                progressControl.off('touchstart', this.preventSeek);
            } else {
                // ❌ 禁用进度条
                el.style.pointerEvents = 'none';
                el.style.opacity = '0.6';
                el.style.cursor = 'not-allowed';
                progressControl.addClass('vjs-disabled');

                // 阻止拖拽事件（双重保障）
                progressControl.on('mousedown', this.preventSeek);
                progressControl.on('touchstart', this.preventSeek);
            }
        },

        /**
         * 阻止拖拽事件
         */
        preventSeek(e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        },

        /**
         * 初始化播放器
         */
        init(setting) {
            // 销毁旧实例
            this.destroy();

            // 合并配置
            const options = { ...this.defaultConfig };
            for (let x in setting) {
                options[x] = setting[x];
            }

            // 处理视频源
            if (options.source) {
                options.sources = [{
                    src: options.source,
                    type: 'video/mp4'
                }];
                delete options.source;
            }

            // 清空 video 元素
            const videoElement = this.$refs.videoElement;
            while (videoElement.firstChild) {
                videoElement.removeChild(videoElement.firstChild);
            }

            // 初始化播放器
            this.player = videoJs(videoElement, options);

            // 播放器就绪后的初始化
            this.player.ready(() => {
                // 1. 设置初始播放进度
                if (this.progress?.currentTime) {
                    this.player.currentTime(this.progress.currentTime);
                }

                // 2. 应用进度条控制状态
                if (this.progress) {
                    const enabled = this.progress.progressControl !== false;
                    this.setProgressControl(enabled);
                }

                // 3. 如果禁止拖动，额外禁用键盘快捷键
                if (this.progress?.progressControl === false) {
                    this.player.on('keydown', this.preventKeyboardSeek);
                }
            });

            // 时间更新事件
            this.time = 0;
            this.player.on('timeupdate', () => {
                const time = parseInt(this.player.currentTime());
                if (time > 0 && time !== this.time && time % 20 === 0) {
                    this.time = time;
                    if (this.action?.record) {
                        this.action.record(time, this.player);
                    }
                }
            });

            // 播放结束事件
            this.player.on('ended', () => {
                if (this.action?.finish) {
                    this.action.finish();
                }
            });

            // 错误监听（调试用）
            this.player.on('error', (e) => {
                console.warn('Video.js 错误:', this.player.error());
            });
        },

        /**
         * 阻止键盘快捷键（左右箭头快进快退）
         */
        preventKeyboardSeek(e) {
            if (e.key === 'ArrowLeft' || e.key === 'ArrowRight' ||
                e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        },

        /**
         * 更新视频源（不重新初始化）
         */
        updateSource(src) {
            if (this.player) {
                this.player.src({ src, type: 'video/mp4' });
                this.player.load();
            }
        },

        /**
         * 跳转到指定时间
         */
        seekTo(time) {
            if (this.player && time !== undefined) {
                this.player.currentTime(time);
            }
        },

        /**
         * 播放
         */
        play() {
            if (this.player) {
                this.player.play();
            }
        },

        /**
         * 暂停
         */
        pause() {
            if (this.player) {
                this.player.pause();
            }
        }
    },
    watch: {
        /**
         * 监听 settings 变化，重新初始化
         */
        settings: {
            handler(newVal) {
                this.init(newVal);
            },
            deep: true
        },

        /**
         * 监听 progress 变化，动态更新
         */
        progress: {
            handler(newVal, oldVal) {
                if (!this.player || !newVal) return;

                // 1. 处理进度条控制状态变化
                if (newVal.progressControl !== oldVal?.progressControl) {
                    const enabled = newVal.progressControl !== false;
                    this.setProgressControl(enabled);

                    // 处理键盘事件
                    if (!enabled) {
                        this.player.on('keydown', this.preventKeyboardSeek);
                    } else {
                        this.player.off('keydown', this.preventKeyboardSeek);
                    }
                }

                // 2. 处理进度跳转
                if (newVal.currentTime !== undefined &&
                    newVal.currentTime !== oldVal?.currentTime) {
                    this.seekTo(newVal.currentTime);
                }
            },
            deep: true,
            immediate: false
        }
    }
}
</script>

<style scoped>
.video-container {
    width: 100%;
    height: 100%;
    position: relative;
}

.video-container video {
    width: 100%;
    height: 100%;
    display: block;
}
</style>

<!-- 全局样式：禁用进度条的视觉反馈 -->
<style>
/* 禁用进度条时的样式 */
.vjs-disabled .vjs-progress-holder {
    cursor: not-allowed !important;
    opacity: 0.6;
    position: relative;
}

.vjs-disabled .vjs-slider {
    cursor: not-allowed !important;
}

/* 禁用进度条时显示锁定图标 */
.vjs-disabled .vjs-progress-holder::after {
    content: '🔒';
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    opacity: 0.6;
    pointer-events: none;
}

/* 禁用进度条时，禁止鼠标事件穿透到 slider */
.vjs-disabled .vjs-slider-handle {
    cursor: not-allowed !important;
}

/* 进度条禁用的提示文字（鼠标悬停时显示） */
.vjs-disabled .vjs-progress-holder:hover::before {
    content: '进度条已锁定';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: #fff;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    pointer-events: none;
    z-index: 999;
}
</style>