{extends file="Layout"}
{block name="contentContent"}
<!-- 内容主体 -->
<div class="content-container">
    <div class="layui-row">
        <!-- 左侧分类导航 -->
        <div class="layui-col-md3 layui-col-sm4">
            <div class="category-sidebar">
                <div class="category-title">内容分类</div>
                <ul class="category-list">
                    <li class="active">
                        <i class="layui-icon layui-icon-app"></i>
                        全部内容
                        <span class="category-count">128</span>
                    </li>
                    <li>
                        <i class="layui-icon layui-icon-notice"></i>
                        新闻资讯
                        <span class="category-count">45</span>
                    </li>
                    <li>
                        <i class="layui-icon layui-icon-speaker"></i>
                        公告通知
                        <span class="category-count">23</span>
                    </li>
                    <li>
                        <i class="layui-icon layui-icon-read"></i>
                        教程指南
                        <span class="category-count">38</span>
                    </li>
                    <li>
                        <i class="layui-icon layui-icon-help"></i>
                        帮助文档
                        <span class="category-count">22</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 右侧内容区 -->
        <div class="layui-col-md9 layui-col-sm8 content-main">
            <!-- 分类标签 -->
            <div class="category-tabs">
                <div class="layui-tab layui-tab-brief">
                    <ul class="layui-tab-title">
                        <li class="layui-this">全部</li>
                        <li>最新</li>
                        <li>最热</li>
                        <li>推荐</li>
                    </ul>
                </div>
            </div>

            <!-- 内容列表 -->
            <div class="layui-row layui-col-space20" id="content-container">
                <!-- JS填充内容卡片 -->
            </div>

            <!-- 分页 -->
            <div class="pagination-container">
                <div id="pagination"></div>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
{literal}
    <script>
        layui.use(['element', 'laypage'], function(){
            var element = layui.element;
            var laypage = layui.laypage;

            // 模拟内容数据
            const contentData = [
                { title: '2026年首季国家专业职称考试报名通知', summary: '现将2026年第一季度国家专业职称考试报名事宜通知如下，请各位考生及时关注报名时间节点...', tag: '公告通知', date: '2026-02-25', views: 1234 },
                { title: '画布平台荣获年度最具潜力在线教育平台奖', summary: '在刚刚结束的2025年度在线教育行业峰会上，画布平台凭借其创新的教学模式和卓越的用户体验，荣获"年度最具潜力在线教育平台奖"。', tag: '新闻资讯', date: '2026-02-24', views: 2567 },
                { title: '如何利用模拟考场提高过考率？官方攻略来了', summary: '本文将详细介绍如何有效使用平台的模拟考场功能，通过科学的备考方法和反复练习，大幅提高考试通过率。', tag: '教程指南', date: '2026-02-23', views: 3421 },
                { title: '系统维护公告：本周六凌晨2点进行数据库升级', summary: '为提供更优质的服务，平台将于本周六（2月29日）凌晨2:00-6:00进行数据库升级维护，期间将暂停服务。', tag: '公告通知', date: '2026-02-22', views: 892 },
                { title: '新增《深度学习实战》系列免费公开课', summary: '平台新增AI技术系列课程，由行业顶尖专家倾情打造，零基础入门到实战项目全流程讲解，完全免费开放学习。', tag: '新闻资讯', date: '2026-02-21', views: 4521 },
                { title: '用户常见问题解答汇总', summary: '整理了用户在使用平台过程中最常遇到的20个问题及解决方案，包括账户注册、课程购买、考试流程等各个方面。', tag: '帮助文档', date: '2026-02-20', views: 2156 },
                { title: '开发者日志：我们如何优化移动端的渲染速度', summary: '本文将深入剖析团队在移动端性能优化方面的技术实践，分享从架构设计到代码层面的优化经验。', tag: '教程指南', date: '2026-02-19', views: 1678 },
                { title: '画布项目正式启动"偏远地区教育援建"计划', summary: '为响应国家教育扶贫号召，画布项目正式启动偏远地区教育援建计划，为欠发达地区提供免费的在线教育资源。', tag: '新闻资讯', date: '2026-02-18', views: 3245 },
                { title: '关于打击非法盗录课程行为的严正声明', summary: '近期发现有不法分子盗录平台付费课程进行非法传播，平台将采取法律手段维护讲师和平台的合法权益。', tag: '公告通知', date: '2026-02-17', views: 1890 },
                { title: '新版个人中心功能预览：图表化学习进度追踪', summary: '个人中心全新升级，新增可视化的学习进度图表、能力雷达图、学习时间统计等强大功能，助你更好地规划学习。', tag: '新闻资讯', date: '2026-02-16', views: 2876 },
                { title: '名师面对面：下周三晚8点直播答疑交流会', summary: '特邀行业资深讲师在线答疑，解答学员在学习过程中遇到的各种问题，机会难得，不要错过！', tag: '新闻资讯', date: '2026-02-15', views: 1956 },
                { title: '课程评价体系全新升级，更精准的学习反馈', summary: '平台课程评价系统全新升级，新增多维度评分机制和学习效果反馈，帮助学员选择更适合自己的课程。', tag: '新闻资讯', date: '2026-02-14', views: 2134 }
            ];

            // 渲染内容卡片
            const contentContainer = document.getElementById('content-container');
            contentData.forEach((item, index) => {
                const col = document.createElement('div');
                col.className = 'layui-col-md4 layui-col-sm6';
                col.innerHTML = `
                <a href="index.php?content-app-content">
                    <div class="content-card">
                        <div class="content-thumb">
                            <span class="content-tag">${item.tag}</span>
                            <img src="https://modao.cc/agent-py/media/generated_images/2026-02-25/4434b8cc421c4d9d94e757d787d9b609.jpg" alt="Content Image">
                        </div>
                        <div class="content-body">
                            <h3 class="content-title">${item.title}</h3>
                            <p class="content-summary">${item.summary}</p>
                            <div class="content-meta">
                                <span><i class="layui-icon layui-icon-date"></i>${item.date}</span>
                                <span><i class="layui-icon layui-icon-eye"></i>${item.views}</span>
                            </div>
                        </div>
                    </div>
                </a>
                `;
                contentContainer.appendChild(col);
            });

            // 渲染分页
            laypage.render({
                elem: 'pagination',
                count: 128,
                limit: 12,
                layout: ['count', 'prev', 'page', 'next', 'limit', 'skip'],
                jump: function(obj){
                    console.log(obj.curr);
                }
            });

            // 分类点击事件
            document.querySelectorAll('.category-list li').forEach(item => {
                item.addEventListener('click', function() {
                    document.querySelectorAll('.category-list li').forEach(li => li.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
{/literal}
{/block}