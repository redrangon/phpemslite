import http from "@/framework/http";
import config from "@/config";
import router from '@/router';
import {showFailToast,showSuccessToast,closeToast } from 'vant'
const wechat = {
    isWechatEnv:() => {
        // 获取 User Agent
        let userAgent = navigator.userAgent.toLowerCase();

        // 判断是否在微信中打开
        if (userAgent.indexOf('micromessenger') !== -1) {
            return true;
        } else {
            return false;
        }
    },
    getWechatAuthUrl:() => {
        let url = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
        let fromUrl = window.location.href;
        url += 'appid='+config.wechatAppid;
        url += '&redirect_uri='+encodeURIComponent(fromUrl);
        url += '&response_type=code';
        url += '&scope=snsapi_base';
        url += '&state=#wechat_redirect';
        window.location.replace(url);
    },
    loginByCode:async (code) => {
        const res = await ajax({
            url:'index.php',
            data:{
                api:'wxlogin',
                code,
                fail:function(res){
                    closeToast();
                    showFailToast({
                        message:res.message,
                        onClose:function(){
                            window.location.replace(window.location.origin+window.location.pathname+window.location.hash);
                        }
                    });
                }
            }
        });
        if(res.statusCode == 200)
        {
            router.replace('/');
            return true;
        }
        else return false;
    },
    unBindWechat:async () => {
        const res = await ajax({
            url:'user.php',
            data:{
                api:'unbindwechat'
            } 
        }) 
        if(res.statusCode === 200) return true;
        else return false;
    },
    bindWechat:async (code) => {
        const res = await ajax({
            url:'user.php',
            data:{
                api:'bindwechat',
                code,
                success:(res) => {
                    if(code)window.location.replace(window.location.origin+window.location.pathname+window.location.hash);
                },
                fail:(res) => {
                    showFailToast({
                        message:res.message,
                        onClose:() => {
                            if(code)window.location.replace(window.location.origin+window.location.pathname+window.location.hash);
                        }
                    });  
                }
            }
        })
        if(res.statusCode === 200)
        {
            return true;
        }
        else if(res.statusCode === 402)
        {
            wechat.getWechatAuthUrl();
        }
        else return false;
    }
};
export default wechat;