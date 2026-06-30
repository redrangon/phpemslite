<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PHPEMS在线模拟考试系统</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="resources/styles/layui/css/layui.css" rel="stylesheet">
    <link href="resources/styles/phpems/desktop/css/master.css" rel="stylesheet">
    <link href="resources/styles/wangEditor/css/style.css" rel="stylesheet">
</head>
<body>
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo layui-bg-black">
            <img src="resources/styles/phpems/logo.png" style="max-width: 80%"/>
        </div>
        <!-- 头部区域（可配合layui 已有的水平导航） -->
        <ul class="layui-nav layui-layout-left">
            <!-- 移动端显示 -->
            <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-header-event="menuLeft">
                <i class="layui-icon layui-icon-spread-left"></i>
            </li>
            <li class="layui-nav-item layui-hide-xs">
                <a href="index.php?core-master"><i class="layui-icon layui-icon-app"></i> 首页</a>
            </li>
            <li class="layui-nav-item layui-hide-xs layui-this">
                <a href="index.php?content-master"><i class="layui-icon layui-icon-app"></i> 内容</a>
            </li>
            <li class="layui-nav-item layui-hide-xs">
                <a href="javascript:;"><i class="layui-icon layui-icon-app"></i> 课程</a>
            </li>
            <li class="layui-nav-item layui-hide-xs">
                <a href="javascript:;"><i class="layui-icon layui-icon-app"></i> 考试</a>
            </li>
            <li class="layui-nav-item layui-hide-xs">
                <a href="javascript:;"><i class="layui-icon layui-icon-app"></i> 附件</a>
            </li>
            <li class="layui-nav-item layui-hide-xs">
                <a href="javascript:;"><i class="layui-icon layui-icon-app"></i> 报名</a>
            </li>
        </ul>
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item layui-hide layui-show-sm-inline-block">
                <a href="javascript:;">
                    <img src="//unpkg.com/outeres@0.0.10/img/layui/icon-v2.png" class="layui-nav-img">
                    tester
                </a>
                <dl class="layui-nav-child">
                    <dd><a href="javascript:;">个人中心</a></dd>
                    <dd><a href="javascript:;">系统设置</a></dd>
                    <dd><a href="javascript:;">登出系统</a></dd>
                </dl>
            </li>
        </ul>
    </div>
    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree" lay-filter="test">
                <li class="layui-nav-item{if $controller == 'index'} layui-this{/if}"><a href="index.php?content-master">模块简介</a></li>
                <li class="layui-nav-item{if $controller == 'category'} layui-this{/if}"><a href="index.php?content-master-category">分类管理</a></li>
                <li class="layui-nav-item{if $controller == 'content'} layui-this{/if}"><a href="index.php?content-master-content">内容管理</a></li>
                <li class="layui-nav-item{if $controller == 'module'} layui-this{/if}"><a href="index.php?content-master-module">模型管理</a></li>
            </ul>
        </div>
    </div>
    <div class="layui-body">
        {block name="masterContent"}{/block}
    </div>
    <div class="layui-footer" style="text-align: center;background: #e5e5e5;">
        PHPEMS在线模拟考试系统 V12.0 © 2026
    </div>
</div>

<script src="resources/styles/layui/layui.js"></script>
<script src="resources/styles/wangEditor/index.js"></script>
<script src="resources/styles/phpems/js/layui.config.js"></script>
{block name="masterScript"}{/block}
</body>
</html>