// cryptoUtils.js
import {set_domain, verify_license, encrypt, decrypt} from '@/framework/wasm/wasm.js';

class WasmCrypto {
    constructor() {
        this.initialized = false;
    }

    // 1. 纯同步初始化（只做业务层的授权验证）
    init(domain, license) {
        if (this.initialized) return;

        set_domain(domain);
        const isValid = verify_license(license);
        if (!isValid) {
            throw new Error('WASM 授权验证失败');
        }
        this.initialized = true;
    }

    // 2. 加密方法：JS 字符串 -> Uint8Array -> WASM 加密 -> Base64
    encryptData(plaintext, keyId) {
        if (!this.initialized) throw new Error('WASM 尚未初始化');

        const encoder = new TextEncoder();
        const dataBytes = encoder.encode(plaintext);
        const encryptedBytes = encrypt(dataBytes, keyId);

        return this.uint8ArrayToBase64(encryptedBytes);
    }

    // 3. 解密方法：Base64 -> Uint8Array -> WASM 解密 -> JS 字符串
    decryptData(base64Payload, keyId) {
        if (!this.initialized) throw new Error('WASM 尚未初始化');

        // 1. 将 Base64 字符串还原为 Uint8Array 二进制数据
        const encryptedBytes = this.base64ToUint8Array(base64Payload);

        // 2. 调用 WASM 解密，返回 Uint8Array
        const decryptedBytes = decrypt(encryptedBytes, keyId);

        // 3. 将解密后的 Uint8Array 转回人类可读的字符串
        const decoder = new TextDecoder();
        return decoder.decode(decryptedBytes);
    }

    // ========== 辅助转换方法 ==========

    // Uint8Array 转 Base64
    uint8ArrayToBase64(bytes) {
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary);
    }

    // Base64 转 Uint8Array
    base64ToUint8Array(base64) {
        const binaryString = atob(base64);
        const len = binaryString.length;
        const bytes = new Uint8Array(len);
        for (let i = 0; i < len; i++) {
            bytes[i] = binaryString.charCodeAt(i);
        }
        return bytes;
    }
}

export default new WasmCrypto();