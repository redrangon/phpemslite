import CryptoJS from 'crypto-js'
import Config from "@/config";
export const decrypt = (data,force = false) => {
    try{
        if(Config.hashData || force)return CryptoJS.AES.decrypt(decodeURIComponent(data),CryptoJS.enc.Utf8.parse(Config.hashKey),{
            iv:CryptoJS.enc.Utf8.parse(Config.hashIv),
            mode:CryptoJS.mode.CBC,
            padding:CryptoJS.pad.Pkcs7
        }).toString(CryptoJS.enc.Utf8).toJson();
        else return data.toJson();
    }catch (e) {
        return {
            code: 300,
            msg: '数据解析失败'
        };
    }
}
export const encrypt = (data,force = false) => {
    try{
        if(Config.hashData || force) return encodeURIComponent(CryptoJS.AES.encrypt(JSON.stringify(data),CryptoJS.enc.Utf8.parse(Config.hashKey),{
            iv:CryptoJS.enc.Utf8.parse(Config.hashIv),
            mode:CryptoJS.mode.CBC,
            padding:CryptoJS.pad.Pkcs7
        }).ciphertext.toString(CryptoJS.enc.Base64));
        else return JSON.stringify(data);
    }catch (e) {
        return '';
    }

}