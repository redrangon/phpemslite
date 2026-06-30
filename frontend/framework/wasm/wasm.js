/* @ts-self-types="./wasm.d.ts" */
import * as wasm from "./wasm_bg.wasm";
import { __wbg_set_wasm } from "./wasm_bg.js";

__wbg_set_wasm(wasm);
wasm.__wbindgen_start();
export {
    decrypt, encrypt, get_key_count, set_domain, verify_license
} from "./wasm_bg.js";
