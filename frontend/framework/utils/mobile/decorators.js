import {showFailToast,showSuccessToast,showConfirmDialog,showLoadingToast,closeToast } from 'vant'
import {layer} from "@layui/layui-vue";

export const withLayer = async (fn, msgOptions = null,final = null) => {
    if(msgOptions === null)
    {
        msgOptions = ["操作成功","操作失败"];
    }
    try{
        await fn();
        if(msgOptions[0])showSuccessToast(msgOptions[0]);
    }catch (e) {
        showFailToast(e.message || msgOptions[1]);
    }finally {
        if(final)await final();
    }
};
export const withConfirm = (msg = '确定要删除吗？',delFn,afterFn = null) => {
    showConfirmDialog({
        title:'操作确认',
        message:msg
    }).then(async() => {
        try{
            await delFn();
            showSuccessToast('操作成功')
        }
        catch (e) {
            showFailToast(e.message || '操作失败');
        }finally {
            if(afterFn)await afterFn();
        }
    });
};