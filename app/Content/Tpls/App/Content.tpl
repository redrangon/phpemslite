{extends file="Layout"}
{block name="contentContent"}
<!-- 面包屑导航 -->
<div class="breadcrumb-container">
    <div class="layui-container">
        <span class="breadcrumb-item"><a href="index.php">首页</a></span>
        <span class="breadcrumb-separator"><i class="layui-icon layui-icon-right"></i></span>
        <span class="breadcrumb-item"><a href="index.php?content">内容中心</a></span>
        <span class="breadcrumb-separator"><i class="layui-icon layui-icon-right"></i></span>
        <span class="breadcrumb-item"><a href="index.php?content-app-category">新闻资讯</a></span>
        <span class="breadcrumb-separator"><i class="layui-icon layui-icon-right"></i></span>
        <span class="breadcrumb-item current">画布平台荣获年度最具潜力在线教育平台奖</span>
    </div>
</div>

<!-- 内容详情 -->
<div class="content-container">
    <div class="content-detail">
        <h1>画布平台荣获年度最具潜力在线教育平台奖</h1>
        <div class="content-meta-bar">
            <div class="meta-item">
                <i class="layui-icon layui-icon-user"></i>
                <span><a href="#">平台编辑部</a></span>
            </div>
            <div class="meta-item">
                <i class="layui-icon layui-icon-date"></i>
                <span>2026-02-24</span>
            </div>
            <div class="meta-item">
                <i class="layui-icon layui-icon-eye"></i>
                <span>2,567 阅读</span>
            </div>
            <div class="meta-item">
                <i class="layui-icon layui-icon-tags"></i>
                <span><a href="#">新闻资讯</a></span>
            </div>
            <div class="meta-item">
                <i class="layui-icon layui-icon-share"></i>
                <span>分享</span>
            </div>
        </div>
        <div class="content-body">
            <p>在刚刚结束的2025年度在线教育行业峰会上，画布平台凭借其创新的教学模式和卓越的用户体验，荣获"年度最具潜力在线教育平台奖"。这一荣誉是对平台过去一年在在线教育领域所做努力的充分肯定，也是对未来发展的重要激励。</p>

            <h2>奖项背景</h2>
            <p>"年度最具潜力在线教育平台奖"是由中国在线教育协会主办的行业权威奖项，旨在表彰在过去一年中展现出卓越创新能力和发展潜力的在线教育平台。评选标准包括技术创新、用户增长、教学效果、社会影响力等多个维度。</p>

            <h2>获奖理由</h2>
            <p>画布平台在以下几个方面表现突出：</p>
            <ul>
                <li><strong>技术创新</strong>：引入AIGC辅助教学系统，实现个性化学习路径推荐</li>
                <li><strong>用户体验</strong>：全仿真考场模拟系统，提供沉浸式学习体验</li>
                <li><strong>内容建设</strong>：汇聚行业顶尖讲师，提供高质量的实战课程</li>
                <li><strong>社会责任</strong>：启动"偏远地区教育援建"计划，推动教育公平</li>
            </ul>

            <h2>未来规划</h2>
            <p>获奖不是终点，而是新的起点。2026年，画布平台将在以下几个方面继续发力：</p>
            <ol>
                <li>深化AI技术应用，提供更智能的学习辅助</li>
                <li>拓展课程体系，覆盖更多职业领域</li>
                <li>优化移动端体验，打造全平台学习环境</li>
                <li>加强校企合作，构建产学研一体化生态</li>
            </ol>

            <blockquote>
                感谢所有用户的支持与信任！我们将继续秉承"让学习更简单、让成长更高效"的使命，为每一位学习者提供更优质的服务。
            </blockquote>
        </div>

        <!-- 附件下载区 -->
        <div class="attachment-section">
            <h3><i class="layui-icon layui-icon-file"></i>相关附件</h3>
            <ul class="attachment-list">
                <li class="attachment-item">
                    <i class="layui-icon layui-icon-file"></i>
                    <div class="attachment-info">
                        <div class="attachment-name">获奖证书.jpg</div>
                        <div class="attachment-size">2.3 MB</div>
                    </div>
                    <div class="attachment-btn"><i class="layui-icon layui-icon-download-circle"></i> 下载</div>
                </li>
                <li class="attachment-item">
                    <i class="layui-icon layui-icon-file"></i>
                    <div class="attachment-info">
                        <div class="attachment-name">颁奖典礼视频.mp4</div>
                        <div class="attachment-size">156.8 MB</div>
                    </div>
                    <div class="attachment-btn"><i class="layui-icon layui-icon-download-circle"></i> 下载</div>
                </li>
            </ul>
        </div>
    </div>

    <!-- 相关推荐 -->
    <div class="related-section">
        <h3><i class="layui-icon layui-icon-tips"></i>相关推荐</h3>
        <ul class="related-list">
            <li class="related-item">
                <a href="#">新增《深度学习实战》系列免费公开课</a>
                <span>2026-02-21</span>
            </li>
            <li class="related-item">
                <a href="#">画布项目正式启动"偏远地区教育援建"计划</a>
                <span>2026-02-18</span>
            </li>
            <li class="related-item">
                <a href="#">2026年度发展规划正式发布</a>
                <span>2026-02-04</span>
            </li>
        </ul>
    </div>

    <!-- 评论区 -->
    <div class="comments-section">
        <div class="comments-header">
            评论区 <span>(3条评论)</span>
        </div>
        <div class="comment-form">
            <form class="layui-form">
                <div class="layui-form-item">
                    <textarea placeholder="发表你的评论..." class="layui-textarea"></textarea>
                </div>
                <div class="layui-form-item">
                    <button type="button" class="layui-btn layui-btn-normal">发表评论</button>
                </div>
            </form>
        </div>
        <ul class="comment-list">
            <li class="comment-item">
                <div class="comment-header">
                    <div class="comment-avatar">张</div>
                    <span class="comment-user">张同学</span>
                    <span class="comment-time">2026-02-24 18:30</span>
                </div>
                <div class="comment-content">恭喜画布平台获奖！作为平台的老用户，见证了平台一步步的成长，希望未来越来越好！</div>
                <div class="comment-actions">
                    <span><i class="layui-icon layui-icon-praise"></i> 32</span>
                    <span><i class="layui-icon layui-icon-dialogue"></i> 回复</span>
                </div>
            </li>
            <li class="comment-item">
                <div class="comment-header">
                    <div class="comment-avatar">李</div>
                    <span class="comment-user">李老师</span>
                    <span class="comment-time">2026-02-24 20:15</span>
                </div>
                <div class="comment-content">很荣幸能够成为画布平台的合作讲师，感谢平台提供这么好的教学环境和技术支持。</div>
                <div class="comment-actions">
                    <span><i class="layui-icon layui-icon-praise"></i> 28</span>
                    <span><i class="layui-icon layui-icon-dialogue"></i> 回复</span>
                </div>
            </li>
            <li class="comment-item">
                <div class="comment-header">
                    <div class="comment-avatar">王</div>
                    <span class="comment-user">王工</span>
                    <span class="comment-time">2026-02-25 09:20</span>
                </div>
                <div class="comment-content">通过画布平台的模拟考场，成功考取了架构师认证，这个奖项实至名归！</div>
                <div class="comment-actions">
                    <span><i class="layui-icon layui-icon-praise"></i> 45</span>
                    <span><i class="layui-icon layui-icon-dialogue"></i> 回复</span>
                </div>
            </li>
        </ul>
    </div>
</div>
{/block}
{block name="script"}
{literal}
    <script>
        layui.use(['form', 'layer'], function(){
            var form = layui.form;
            var layer = layui.layer;
        });
    </script>
{/literal}
{/block}