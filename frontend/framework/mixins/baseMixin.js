import { withLayer, withConfirm } from '@/framework/utils/decorator-adapter.js';

/**
 * 基础操作 Mixin
 * 提供常用的带 loading、确认提示的操作方法
 */
export default {
    methods: {
        /**
         * 执行带 loading 的操作
         * @param {Function} fn - 要执行的异步函数
         * @param {string} successMsg - 成功消息
         * @param {string} errorMsg - 失败消息
         * @param {Function} finalFn - 最终执行的函数（如刷新列表）
         */
        async execute(fn, successMsg = '操作成功', errorMsg = '操作失败', finalFn = null) {
            return withLayer(fn, [successMsg, errorMsg], finalFn);
        },

        /**
         * 带确认的删除操作
         * @param {Function} deleteFn - 删除函数
         * @param {Function} afterFn - 删除后的回调
         */
        confirmDelete(deleteFn, afterFn = null) {
            withConfirm('确定要删除吗？', deleteFn, afterFn);
        },

        confirmOperate(message,fn, afterFn = null) {
            withConfirm(message??'确定要删除吗？', fn, afterFn);
        },

        /**
         * 通用操作（自动调用 getData）
         * @param {Function} fn - 要执行的函数
         * @param {string} successMsg - 成功消息
         */
        async base(fn, successMsg = '操作成功') {
            // 假设组件有 getData 方法
            if (this.getData) {
                return this.execute(fn, successMsg, null, this.getData);
            }
            return this.execute(fn, successMsg);
        }
    }
};