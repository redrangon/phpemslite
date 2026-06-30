{extends file="Layout"}
{block name="masterContent"}
<!-- 内容主体区域 -->
<div style="padding: 15px;">
    <div class="layui-panel" style="padding: 15px;margin-bottom: 10px;">
        <span class="layui-breadcrumb">
            <a href="index.php?core-master">首页</a>
        </span>
    </div>
    <div class="layui-card layui-panel">
        <div class="layui-card-header">后台首页</div>
        <div class="layui-card-body">
            <p>PHPEMS在线模拟考试系统 V12.0</p>
        </div>
    </div>
</div>
{/block}
{block name="masterScript"}
<script>
    //JS
    layui.use(['element', 'layer', 'util', 'editor','form'], function(){
        var element = layui.element;
        var layer = layui.layer;
        var util = layui.util;
        var $ = layui.$;
        var form = layui.form;
        var wangEditor = layui.editor;

        {literal}
        wangEditor.render('.wangEditor');
        form.on('submit(loginSubmit)', function(data){
            // 这里可以添加AJAX提交逻辑
            console.log(data.field);
            return false; // 阻止表单默认提交
        });
        //头部事件
        util.event('lay-header-event', {
            menuLeft: function(othis){ // 左侧菜单事件
                layer.msg('展开左侧菜单的操作', {icon: 0});
            },
            menuRight: function(){  // 右侧菜单事件
                layer.open({
                    type: 1,
                    title: '更多',
                    content: '<div style="padding: 15px;">处理右侧面板的操作</div>',
                    area: ['260px', '100%'],
                    offset: 'rt', // 右上角
                    anim: 'slideLeft', // 从右侧抽屉滑出
                    shadeClose: true,
                    scrollbar: false
                });
            }
        });
        {/literal}
    });
</script>
{/block}