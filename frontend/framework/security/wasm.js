import Config from "@/config";
import wasmCrypto from "@/framework/utils/wasmCrypto.js";

export const decrypt = function(data,keyId){
    if(Config.hashData) {
        try {
            const decryptedJson = wasmCrypto.decryptData(data, keyId);
            return JSON.parse(decryptedJson);
        } catch (e) {
            return {
                code: 300,
                msg: '数据解析失败'
            };
        }
    }else return data;
}

export const encrypt = function(data){
    if(Config.hashData)
    {
        const id = Math.floor(Math.random() * 100);
        try {
            const encryptedJson = wasmCrypto.encryptData(JSON.stringify(data),id);
            return { data: encryptedJson, keyId: id };
        } catch (e) {
            return '';
        }
    }
    else return JSON.stringify(data);
}