{extends file="Layout"}
{block name="masterContent"}
<!-- 内容主体区域 -->
<div style="padding: 15px;">
    <div class="layui-panel" style="padding: 15px;margin-bottom: 10px;">
        <span class="layui-breadcrumb">
            <a href="index.php?core-master">首页</a>
            <a href="index.php?content-master">内容管理</a>
            <a><cite>内容列表</cite></a>
        </span>
    </div>
    
    <div class="layui-card layui-panel">
        <div class="layui-card-header">
            <span>内容管理</span>
            <div style="float: right;">
                <button class="layui-btn layui-btn-sm" id="btnAdd">
                    <i class="layui-icon layui-icon-add-1"></i> 新增内容
                </button>
            </div>
        </div>
        <div class="layui-card-body">
            <!-- 搜索表单 -->
            <form class="layui-form" id="searchForm" style="margin-bottom: 15px;">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">标题</label>
                        <div class="layui-input-inline">
                            <input type="text" name="title" placeholder="请输入标题" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">分类</label>
                        <div class="layui-input-inline">
                            <select name="category_id" lay-verify="">
                                <option value="">全部分类</option>
                                {foreach $categories as $cat}
                                <option value="{$cat.id}">{$cat.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select name="status" lay-verify="">
                                <option value="">全部状态</option>
                                <option value="1">已发布</option>
                                <option value="0">草稿</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
            
            <table id="contentTable" lay-filter="contentTable"></table>
        </div>
    </div>
</div>

<!-- 新增/编辑内容表单弹窗 -->
<div id="contentForm" style="display: none; padding: 20px;">
    <form class="layui-form" lay-filter="contentFormFilter">
        <input type="hidden" name="id" value="">
        <div class="layui-form-item">
            <label class="layui-form-label">分类</label>
            <div class="layui-input-block">
                <select name="category_id" lay-verify="required">
                    <option value="">请选择分类</option>
                    {foreach $categories as $cat}
                    <option value="{$cat.id}">{$cat.name}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">标题</label>
            <div class="layui-input-block">
                <input type="text" name="title" lay-verify="required" placeholder="请输入标题" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">缩略图</label>
            <div class="layui-input-block">
                <button type="button" class="layui-btn layui-btn-sm" id="btnUploadThumb">
                    <i class="layui-icon layui-icon-upload"></i> 上传图片
                </button>
                <input type="hidden" name="thumb" id="thumbInput" value="">
                <div id="thumbPreview" style="margin-top: 10px;"></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">摘要</label>
            <div class="layui-input-block">
                <textarea name="summary" placeholder="请输入摘要" class="layui-textarea"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">内容</label>
            <div class="layui-input-block">
                <div name="content" id="contentEditor" placeholder="请输入内容" class="wangEditor"></div>
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
                <input type="radio" name="status" value="0" title="草稿">
                <input type="radio" name="status" value="1" title="已发布" checked>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">推荐</label>
            <div class="layui-input-block">
                <input type="checkbox" name="is_recommend" lay-skin="switch" lay-text="是|否">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">置顶</label>
            <div class="layui-input-block">
                <input type="checkbox" name="is_top" lay-skin="switch" lay-text="是|否">
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
<script type="text/html" id="statusTpl">
    {{#  if(d.status == 1){ }}
        <span class="layui-badge layui-bg-green">已发布</span>
    {{#  } else { }}
        <span class="layui-badge layui-bg-gray">草稿</span>
    {{#  } }}
</script>

<!-- 推荐模板 -->
<script type="text/html" id="recommendTpl">
    {{#  if(d.is_recommend == 1){ }}
        <i class="layui-icon layui-icon-ok" style="color: #009688;"></i>
    {{#  } else { }}
        <i class="layui-icon layui-icon-close" style="color: #c2c2c2;"></i>
    {{#  } }}
</script>

<!-- 置顶模板 -->
<script type="text/html" id="topTpl">
    {{#  if(d.is_top == 1){ }}
        <i class="layui-icon layui-icon-up" style="color: #FF5722;"></i>
    {{#  } else { }}
        <i class="layui-icon layui-icon-down" style="color: #c2c2c2;"></i>
    {{#  } }}
</script>
{/block}

{block name="masterScript"}
<script>
layui.use(['table', 'layer', 'form', 'jquery', 'upload', 'editor'], function(){
    var table = layui.table;
    var layer = layui.layer;
    var form = layui.form;
    var $ = layui.$;
    var upload = layui.upload;
    var wangEditor = layui.editor;
    
    {literal}
    // 初始化富文本编辑器
    wangEditor.render('.wangEditor');
    // 渲染表格
    table.render({
        elem: '#contentTable',
        url: 'index.php?content-master-content-getList', // 数据接口
        page: true,
        cols: [[
            {type: 'checkbox', fixed: 'left'},
            {type: 'numbers', title: '序号', width: 80, fixed: 'left'},
            {field: 'id', title: 'ID', width: 80, sort: true},
            {field: 'title', title: '标题', width: 200},
            {field: 'category_name', title: '分类', width: 120},
            {field: 'author', title: '作者', width: 100},
            {field: 'views', title: '浏览量', width: 100, sort: true},
            {field: 'status', title: '状态', width: 100, templet: '#statusTpl'},
            {field: 'is_recommend', title: '推荐', width: 80, templet: '#recommendTpl', align: 'center'},
            {field: 'is_top', title: '置顶', width: 80, templet: '#topTpl', align: 'center'},
            {field: 'sort', title: '排序', width: 80, sort: true},
            {field: 'create_time', title: '创建时间', width: 180},
            {field: 'update_time', title: '更新时间', width: 180},
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
    
    // 搜索按钮点击事件
    $('#btnSearch').on('click', function(){
        var formData = $('#searchForm').serialize();
        table.reload('contentTable', {
            url: 'index.php?content-master-content-getList?' + formData,
            page: {
                curr: 1
            }
        });
    });
    
    // 新增按钮点击事件
    $('#btnAdd').on('click', function(){
        form.val('contentFormFilter', {
            id: '',
            category_id: '',
            title: '',
            thumb: '',
            summary: '',
            content: '',
            sort: '0',
            status: '1',
            is_recommend: false,
            is_top: false
        });
        $('#thumbPreview').empty();
        
        layer.open({
            type: 1,
            title: '新增内容',
            content: $('#contentForm'),
            area: ['900px', '80vh'],
            btn: ['确定', '取消'],
            yes: function(index, layero){
                $('#contentForm form').find('button[lay-submit]').click();
            },
            btn2: function(index, layero){
                layer.close(index);
            },
            success: function(layero, index){
                // 初始化富文本编辑器

            }
        });
    });
    
    // 监听表格工具条
    table.on('tool(contentTable)', function(obj){
        var data = obj.data;
        
        if(obj.event === 'edit'){
            // 编辑
            form.val('contentFormFilter', {
                id: data.id,
                category_id: data.category_id,
                title: data.title,
                thumb: data.thumb || '',
                summary: data.summary || '',
                sort: data.sort,
                status: String(data.status),
                is_recommend: data.is_recommend == 1,
                is_top: data.is_top == 1
            });
            
            // 显示缩略图预览
            if(data.thumb){
                $('#thumbPreview').html('<img src="' + data.thumb + '" style="max-width: 200px; max-height: 200px;">');
            } else {
                $('#thumbPreview').empty();
            }
            
            layer.open({
                type: 1,
                title: '编辑内容',
                content: $('#contentForm'),
                area: ['900px', '80vh'],
                btn: ['确定', '取消'],
                yes: function(index, layero){
                    $('#contentForm form').find('button[lay-submit]').click();
                },
                btn2: function(index, layero){
                    layer.close(index);
                },
                success: function(layero, index){
                    // 初始化富文本编辑器
                    if(editor){
                        editor.destroy();
                    }
                    editor = wangEditor.create('#contentEditor');
                    editor.setHtml(data.content || '');
                }
            });
        } else if(obj.event === 'del'){
            // 删除
            layer.confirm('确定删除该内容吗？', function(index){
                $.ajax({
                    url: 'index.php?content-master-content-delete',
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
    form.on('submit(contentSubmit)', function(data){
        var formData = data.field;
        
        // 获取富文本内容
        if(editor){
            formData.content = editor.getHtml();
        }
        
        formData.is_recommend = formData.is_recommend ? 1 : 0;
        formData.is_top = formData.is_top ? 1 : 0;
        
        $.ajax({
            url: 'index.php?content-master-content-save',
            type: 'POST',
            data: formData,
            success: function(res){
                if(res.code == 200){
                    layer.msg('保存成功', {icon: 1});
                    layer.closeAll('page');
                    table.reload('contentTable');
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
    
    // 图片上传
    upload.render({
        elem: '#btnUploadThumb',
        url: 'index.php?attach-api-upload',
        accept: 'images',
        acceptMime: 'image/*',
        field: 'file',
        done: function(res){
            if(res.code == 200){
                $('#thumbInput').val(res.data.url);
                $('#thumbPreview').html('<img src="' + res.data.url + '" style="max-width: 200px; max-height: 200px;">');
                layer.msg('上传成功', {icon: 1});
            } else {
                layer.msg(res.msg || '上传失败', {icon: 2});
            }
        },
        error: function(){
            layer.msg('上传失败', {icon: 2});
        }
    });
    {/literal}
});
</script>
{/block}