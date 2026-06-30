{extends file="Layout"}
{block name="masterContent"}
<!-- 内容主体区域 -->
<div style="padding: 15px;">
    <div class="layui-panel" style="padding: 15px;margin-bottom: 10px;">
        <span class="layui-breadcrumb">
            <a href="index.php?core-master">首页</a>
            <a href="index.php?content-master">内容管理</a>
            <a><cite>分类管理</cite></a>
        </span>
    </div>
    
    <div class="layui-card layui-panel">
        <div class="layui-card-header">
            <span>内容分类</span>
            <div style="float: right;">
                <button class="layui-btn layui-btn-sm" id="btnAdd">
                    <i class="layui-icon layui-icon-add-1"></i> 新增分类
                </button>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="categoryTable" lay-filter="categoryTable"></table>
        </div>
    </div>
</div>

<!-- 新增/编辑分类表单弹窗 -->
<div id="categoryForm" style="display: none; padding: 20px;">
    <form class="layui-form" lay-filter="categoryFormFilter">
        <input type="hidden" name="id" value="">
        <div class="layui-form-item">
            <label class="layui-form-label">上级分类</label>
            <div class="layui-input-block">
                <select name="parent_id" lay-verify="">
                    <option value="0">顶级分类</option>
                    {foreach $categories as $cat}
                    <option value="{$cat.id}">{$cat.name}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">分类名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" lay-verify="required" placeholder="请输入分类名称" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">排序</label>
            <div class="layui-input-block">
                <input type="number" name="sort" lay-verify="number" value="0" placeholder="请输入排序值" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-block">
                <input type="checkbox" name="status" lay-skin="switch" lay-text="启用|禁用" checked>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">描述</label>
            <div class="layui-input-block">
                <textarea name="description" placeholder="请输入分类描述" class="layui-textarea"></textarea>
            </div>
        </div>
    </form>
</div>

<!-- 操作列模板 -->
<script type="text/html" id="tableToolbar">
    <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>

<!-- 状态模板 -->
{literal}
<script type="text/html" id="statusTpl">
    <input type="checkbox" name="status" value="{{d.id}}" title="启用" lay-skin="switch" lay-filter="statusSwitch" {{ d.status == 1 ? 'checked' : '' }}>
</script>
{/literal}
{/block}

{block name="masterScript"}
<script>
layui.use(['table', 'layer', 'form', 'jquery'], function(){
    var table = layui.table;
    var layer = layui.layer;
    var form = layui.form;
    var $ = layui.$;
    
    {literal}
    // 渲染表格
    table.render({
        elem: '#categoryTable',
        url: 'index.php?content-master-category-getList', // 数据接口
        page: true,
        cols: [[
            {type: 'numbers', title: '序号', width: 80},
            {field: 'id', title: 'ID', width: 80, sort: true},
            {field: 'name', title: '分类名称', width: 200},
            {field: 'parent_name', title: '上级分类', width: 150},
            {field: 'sort', title: '排序', width: 100, sort: true},
            {field: 'status', title: '状态', width: 100, templet: '#statusTpl', unresize: true},
            {field: 'description', title: '描述', minWidth: 200},
            {field: 'create_time', title: '创建时间', width: 180},
            {title: '操作', width: 150, toolbar: '#tableToolbar', fixed: 'right'}
        ]],
        response: {
            statusCode: 200
        },
        parseData: function(res) {
            return {
                "code": res.code,
                "msg": res.msg,
                "count": res.count,
                "data": res.data
            };
        }
    });
    
    // 新增按钮点击事件
    $('#btnAdd').on('click', function(){
        form.val('categoryFormFilter', {
            id: '',
            parent_id: '0',
            name: '',
            sort: '0',
            status: true,
            description: ''
        });
        layer.open({
            type: 1,
            title: '新增分类',
            content: $('#categoryForm'),
            area: ['600px', '500px'],
            btn: ['确定', '取消'],
            yes: function(index, layero){
                $('#categoryForm form').find('button[lay-submit]').click();
            },
            btn2: function(index, layero){
                layer.close(index);
            }
        });
    });
    
    // 监听表格工具条
    table.on('tool(categoryTable)', function(obj){
        var data = obj.data;
        
        if(obj.event === 'edit'){
            // 编辑
            form.val('categoryFormFilter', {
                id: data.id,
                parent_id: data.parent_id,
                name: data.name,
                sort: data.sort,
                status: data.status == 1,
                description: data.description
            });
            layer.open({
                type: 1,
                title: '编辑分类',
                content: $('#categoryForm'),
                area: ['600px', '500px'],
                btn: ['确定', '取消'],
                yes: function(index, layero){
                    $('#categoryForm form').find('button[lay-submit]').click();
                },
                btn2: function(index, layero){
                    layer.close(index);
                }
            });
        } else if(obj.event === 'del'){
            // 删除
            layer.confirm('确定删除该分类吗？', function(index){
                $.ajax({
                    url: 'index.php?content-master-category-delete',
                    type: 'POST',
                    data: {id: data.id},
                    success: function(res){
                        if(res.code == 200){
                            layer.msg('删除成功', {icon: 1});
                            obj.del();
                        } else {
                            layer.msg(res.msg || '删除失败', {icon: 2});
                        }
                    },
                    error: function(){
                        layer.msg('网络错误', {icon: 2});
                    }
                });
                layer.close(index);
            });
        }
    });
    
    // 监听表单提交
    form.on('submit(categorySubmit)', function(data){
        var formData = data.field;
        formData.status = formData.status ? 1 : 0;
        
        $.ajax({
            url: 'index.php?content-master-category-save',
            type: 'POST',
            data: formData,
            success: function(res){
                if(res.code == 200){
                    layer.msg('保存成功', {icon: 1});
                    layer.closeAll('page');
                    table.reload('categoryTable');
                } else {
                    layer.msg(res.msg || '保存失败', {icon: 2});
                }
            },
            error: function(){
                layer.msg('网络错误', {icon: 2});
            }
        });
        return false;
    });
    
    // 监听状态开关
    form.on('switch(statusSwitch)', function(obj){
        var id = this.value;
        var status = obj.elem.checked ? 1 : 0;
        
        $.ajax({
            url: 'index.php?content-master-category-updateStatus',
            type: 'POST',
            data: {id: id, status: status},
            success: function(res){
                if(res.code == 200){
                    layer.msg('更新成功', {icon: 1});
                } else {
                    layer.msg(res.msg || '更新失败', {icon: 2});
                    // 恢复开关状态
                    $(obj.elem).prop('checked', !obj.elem.checked);
                    form.render('checkbox');
                }
            },
            error: function(){
                layer.msg('网络错误', {icon: 2});
                $(obj.elem).prop('checked', !obj.elem.checked);
                form.render('checkbox');
            }
        });
    });
    {/literal}
});
</script>
{/block}