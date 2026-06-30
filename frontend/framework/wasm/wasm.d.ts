/* tslint:disable */
/* eslint-disable */

/**
 * 解密函数
 * encrypted: IV(16字节) + 密文
 * key_id: 密钥序号（0-99）
 * 返回: 明文数据
 */
export function decrypt(encrypted: Uint8Array, key_id: number): Uint8Array;

/**
 * 加密函数
 * data: 明文数据
 * key_id: 密钥序号（0-99）
 * 返回: IV(16字节) + 密文
 */
export function encrypt(data: Uint8Array, key_id: number): Uint8Array;

/**
 * 获取密钥版本号（用于调试）
 */
export function get_key_count(): number;

/**
 * 设置当前域名（由 JS 传入）
 */
export function set_domain(domain: string): void;

/**
 * 验证 HMAC-SHA256 授权码
 */
export function verify_license(license: string): boolean;
