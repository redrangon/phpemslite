import {layer} from "@layui/layui-vue";

export const withLayer = async(fn,msgOptions = null,final = null) => {
    if(msgOptions === null)
    {
        msgOptions = ["操作成功","操作失败"];
    }
    const id = layer.load(0);
    try{
        await fn();
        layer.close(id);
        if(msgOptions[0])layer.msg(msgOptions[0]);
    }catch (e) {
        layer.msg(e.message || msgOptions[1]);
    }finally {
        if(final)await final();
        layer.close(id);
    }
};
export const withConfirm = (msg = '确定要删除吗？',delFn,afterFn = null) => {
    layer.confirm(msg, {
        title: '操作确认',
        btn: [
            {
                text: '确定',
                callback: async (layerId) => {
                    layer.close(layerId);
                    await withLayer(delFn,null,afterFn??null);
                }
            },
            {
                text: '取消',
                callback: (layerId) => {
                    layer.close(layerId);
                }
            }
        ]
    })
};