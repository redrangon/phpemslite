<html lang="zh-CN"><head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>内容中心 - PHPEMS在线模拟考试系统</title>
    {$style.css|nofilter}
    <!-- 引入内容中心CSS -->
    <link href="resources/styles/phpems/desktop/css/content.css" rel="stylesheet">
    {block name="css"}{/block}
</head>
<body>
<!-- 头部导航 -->
<header class="canvas-header">
    <div class="layui-container">
        <div class="layui-row">
            <div class="layui-col-md2 layui-col-xs6">
                <a class="canvas-logo" href="#">
                    <img src="resources/styles/phpems/logo.png" style="width:200px;margin-top:15px;"/>
                </a>
            </div>
            <div class="layui-col-md8 layui-hide-xs">
                <ul class="layui-nav">
                    <li class="layui-nav-item"><a href="index.php">网站首页</a></li>
                    <li class="layui-nav-item"><a href="index.php?course">课程中心</a></li>
                    <li class="layui-nav-item"><a href="index.php?exam">考场模拟</a></li>
                    <li class="layui-nav-item layui-this"><a href="index.php?content">内容中心</a></li>
                    <li class="layui-nav-item"><a href="index.php">关于我们</a></li>
                </ul>
            </div>
            <div class="layui-col-md2 layui-col-xs6" style="text-align: right;">
                <div class="header-btns">
                    <button class="layui-btn layui-btn-primary layui-border-blue">登录</button>
                    <button class="layui-btn layui-btn-normal">注册</button>
                </div>
            </div>
        </div>
    </div>
</header>
{if $controller != 'category'}
<!-- 页面头部 -->
<div class="page-header">
    <h1><i class="layui-icon layui-icon-read" style="vertical-align: middle; margin-right: 10px;"></i>内容中心</h1>
    <p>汇聚最新资讯、公告通知、教程指南与帮助文档</p>
</div>
{/if}
{block name="contentContent"}{/block}
<!-- 页脚 -->
<footer class="canvas-footer">
    <div class="layui-container">
        <div class="layui-row layui-col-space30">
            <div class="layui-col-md4">
                <div class="footer-title">关于PHPEMS</div>
                <p style="line-height: 1.8;">PHPEMS是一款中文在线模拟考试系统，包含内容管理，在线课程，模拟考试，用户管理等主要功能，提供在线培训服务。</p>
            </div>
            <div class="layui-col-md2 layui-col-xs6">
                <div class="footer-title">快速链接</div>
                <ul class="footer-link-list">
                    <li><a href="#">首页</a></li>
                    <li><a href="#">课程列表</a></li>
                    <li><a href="#">模拟考场</a></li>
                    <li><a href="#">讲师入驻</a></li>
                </ul>
            </div>
            <div class="layui-col-md2 layui-col-xs6">
                <div class="footer-title">帮助中心</div>
                <ul class="footer-link-list">
                    <li><a href="#">常见问题</a></li>
                    <li><a href="#">用户协议</a></li>
                    <li><a href="#">隐私政策</a></li>
                    <li><a href="#">支付方式</a></li>
                </ul>
            </div>
            <div class="layui-col-md4">
                <div class="footer-title">联系我们</div>
                <ul class="footer-link-list">
                    <li><i class="layui-icon layui-icon-cellphone" style="vertical-align: middle; margin-right: 8px;"></i> 客服热线：400-888-2026</li>
                    <li><i class="layui-icon layui-icon-cellphone" style="vertical-align: middle; margin-right: 8px;"></i> 邮箱：contact@canvas2026.com</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 新乡市落笔千言网络技术有限公司 版权所有 | 豫ICP备13016752号</p>
        </div>
    </div>
</footer>

<!-- 引入 Layui JS -->
{$style.js|nofilter}
{block name="script"}{/block}
</body>
</html>