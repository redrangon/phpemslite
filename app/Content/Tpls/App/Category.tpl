{extends file="Layout"}
{block name="contentContent"}
<!-- 面包屑导航 -->
<div class="breadcrumb-container">
    <div class="layui-container">
        <span class="breadcrumb-item"><a href="#">首页</a></span>
        <span class="breadcrumb-separator"><i class="layui-icon layui-icon-right"></i></span>
        <span class="breadcrumb-item"><a href="#">内容中心</a></span>
        <span class="breadcrumb-separator"><i class="layui-icon layui-icon-right"></i></span>
        <span class="breadcrumb-item current">新闻资讯</span>
    </div>
</div>
<!-- 分类头部 -->
<div class="category-header">
    <div class="layui-container category-info">
        <h1><i class="layui-icon layui-icon-read" style="vertical-align: middle; margin-right: 10px;"></i>新闻资讯</h1>
        <p>发布平台最新动态、行业新闻、政策解读等相关资讯信息</p>
        <div class="category-stats">
            <div class="stat-item">
                <i class="layui-icon layui-icon-file"></i>
                <span class="stat-value">45</span>
                <span class="stat-label">篇文章</span>
            </div>
            <div class="stat-item">
                <i class="layui-icon layui-icon-eye"></i>
                <span class="stat-value">12.5k</span>
                <span class="stat-label">总阅读</span>
            </div>
            <div class="stat-item">
                <i class="layui-icon layui-icon-date"></i>
                <span class="stat-value">2026-01</span>
                <span class="stat-label">创建时间</span>
            </div>
        </div>
    </div>
</div>
<!-- 内容列表 -->
<div class="content-container">
    <div class="layui-row layui-col-space20" id="content-container">
        <!-- JS填充内容卡片 -->
    </div>

    <!-- 分页 -->
    <div class="pagination-container">
        <div id="pagination"></div>
    </div>
</div>
{/block}
{block name="script"}
{literal}
    <script>
        layui.use(['laypage'], function(){
            var laypage = layui.laypage;

            // 模拟分类内容数据
            const contentData = [
                { title: '画布平台荣获年度最具潜力在线教育平台奖', summary: '在刚刚结束的2025年度在线教育行业峰会上，画布平台凭借其创新的教学模式和卓越的用户体验，荣获"年度最具潜力在线教育平台奖"。', tag: '获奖新闻', date: '2026-02-24', views: 2567 },
                { title: '新增《深度学习实战》系列免费公开课', summary: '平台新增AI技术系列课程，由行业顶尖专家倾情打造，零基础入门到实战项目全流程讲解，完全免费开放学习。', tag: '课程发布', date: '2026-02-21', views: 4521 },
                { title: '画布项目正式启动"偏远地区教育援建"计划', summary: '为响应国家教育扶贫号召，画布项目正式启动偏远地区教育援建计划，为欠发达地区提供免费的在线教育资源。', tag: '公益活动', date: '2026-02-18', views: 3245 },
                { title: '新版个人中心功能预览：图表化学习进度追踪', summary: '个人中心全新升级，新增可视化的学习进度图表、能力雷达图、学习时间统计等强大功能，助你更好地规划学习。', tag: '功能更新', date: '2026-02-16', views: 2876 },
                { title: '名师面对面：下周三晚8点直播答疑交流会', summary: '特邀行业资深讲师在线答疑，解答学员在学习过程中遇到的各种问题，机会难得，不要错过！', tag: '活动预告', date: '2026-02-15', views: 1956 },
                { title: '课程评价体系全新升级，更精准的学习反馈', summary: '平台课程评价系统全新升级，新增多维度评分机制和学习效果反馈，帮助学员选择更适合自己的课程。', tag: '功能更新', date: '2026-02-14', views: 2134 },
                { title: '2025年度优秀学员表彰名单公布', summary: '经过严格评选，2025年度优秀学员名单正式公布，这些学员在学习成果、贡献度等方面表现突出。', tag: '学员风采', date: '2026-02-12', views: 1890 },
                { title: '平台用户突破100万大里程碑', summary: '感谢所有用户的支持与信任！截至2026年2月，平台注册用户已突破100万，我们将继续提供更优质的服务。', tag: '里程碑', date: '2026-02-10', views: 5621 },
                { title: '春季招聘启动，诚邀行业精英加入', summary: '随着平台业务的快速发展，现面向社会招聘各类优秀人才，包括技术开发、产品设计、运营管理等岗位。', tag: '招聘信息', date: '2026-02-08', views: 1432 },
                { title: '春节期间课程优惠活动圆满结束', summary: '感谢广大用户的积极参与，春节期间优惠活动已圆满结束，累计服务用户超过50万人次，好评率达98%。', tag: '活动总结', date: '2026-02-06', views: 2345 },
                { title: '2026年度发展规划正式发布', summary: '在新的一年里，画布平台将在技术创新、内容建设、用户体验等方面进行全面升级，为用户提供更优质的学习体验。', tag: '发展规划', date: '2026-02-04', views: 3210 },
                { title: '合作伙伴招募计划启动', summary: '诚邀各行业优质机构、企业、个人讲师加入我们的合作伙伴计划，共同打造开放共赢的在线教育生态。', tag: '合作招募', date: '2026-02-02', views: 1789 }
            ];

            // 渲染内容卡片
            const contentContainer = document.getElementById('content-container');
            contentData.forEach((item, index) => {
                const col = document.createElement('div');
                col.className = 'layui-col-md3 layui-col-sm6';
                col.innerHTML = `
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
                `;
                contentContainer.appendChild(col);
            });

            // 渲染分页
            laypage.render({
                elem: 'pagination',
                count: 45,
                limit: 12,
                layout: ['count', 'prev', 'page', 'next', 'limit', 'skip'],
                jump: function(obj){
                    console.log(obj.curr);
                }
            });
        });
    </script>
{/literal}
{/block}