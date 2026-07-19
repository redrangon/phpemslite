# PHPEMS Lite
基于PHPEMS magnolia 框架的最新在线考试系统
## 项目概述

Magnolia (PHPEMS v12.0.0) 是一个基于 PHP 的考试管理系统。该项目采用前后端分离架构，后端使用自定义 MVC 框架，前端使用 Vue 3 + Vite 构建现代化用户界面。

关于Magnolia的命名，是继承自Ginkgo后的名称，我大学专业是森林资源保护与游憩，一个很小众的专业，我刚开始学习编程时，正是刚开始学林学类专业的时候，所以第一份框架，也就是PHPEMS1-11版本的框架，是以树木学的第一章银杏（ginkgo）命名的，现在到了第12个版本，原先的框架已经过于古老，所以我用了半年时间，重写了框架和主要功能，然后以第二章木兰（magnolia）命名。

### 主要特性
- **前后端分离架构**: 后端提供 RESTful API，前端使用 Vue 3 + Vite 构建
- **响应式设计**: 支持 PC 端（桌面）和移动端两种布局，自动适配设备类型
- **模块化设计**: 核心功能分为 Core、Course、Exam、User、Content、Attach、Cert 等模块
- **依赖注入系统**: 内置 DI 容器用于管理服务依赖
- **路由系统**: 后端基于 URL 路径的动态路由分发，前端使用 Vue Router
- **插件系统**: 支持通过 plugins 目录扩展功能
- **PSR-4 自动加载**: 使用 Composer 进行类自动加载
- **多数据库支持**: MySQL 和 Redis 支持
- **状态管理**: 前端使用 Pinia 进行状态管理
- **安全加密**: 前后端支持数据加密传输（AES-256-CBC），集成 WASM 加密模块
- **WebAssembly 支持**: 集成 WASM 模块用于高性能加密解密操作

### 技术栈

**后端技术栈：**
- **语言**: PHP >= 8.2
- **架构模式**: 自定义 MVC + 依赖注入 + 流程中间件
- **包管理**: Composer
- **核心组件**:
  - 路由器 (Router)
  - 依赖注入容器 (DI)
  - 请求处理 (Request Provider)
  - 数据库 (DB/Redis)
  - 认证系统 (Auth)
  - 会话管理 (Session)

**前端技术栈：**
- **框架**: Vue 3.4+
- **构建工具**: Vite 5.0+
- **状态管理**: Pinia 3.0+
- **路由**: Vue Router 4.6+
- **HTTP 客户端**: Axios 1.13+
- **UI 组件库**:
  - PC 端: LayUI Vue 2.23+
  - 移动端: Vant 4.9+
- **富文本编辑器**: 
  - Vditor 3.11+
  - WangEditor 5.1+
- **视频播放器**: Video.js 8.23+
- **加密库**: CryptoJS 4.2+
- **轮播组件**: Swiper 12.1+
- **拖拽排序**: SortableJS 1.15+
- **开发模式**: ES Modules

### 依赖要求

**PHP 扩展：**
- ext-json
- ext-pdo
- ext-redis
- ext-mbstring
- ext-fileinfo
- ext-gd
- ext-openssl
- ext-zip

**Composer 依赖：**
- guzzlehttp/guzzle ^7.10
- psr/log ^3.0

**开发依赖 (require-dev)：**
- phpunit/phpunit ^9.5
- mockery/mockery ^1.5
- phpstan/phpstan ^1.10

**Node.js 依赖：**
- @layui/layui-vue ^2.23.3
- @wangeditor/editor ^5.1.23
- @wangeditor/editor-for-vue ^5.1.12
- axios ^1.13.2
- crypto-js ^4.2.0
- pinia ^3.0.4
- swiper ^12.1.3
- sortablejs ^1.15.2
- vant ^4.9.22
- vditor ^3.11.2
- video.js ^8.23.4
- vue ^3.4.0
- vue-router ^4.6.4

## 项目结构

```
magnolia/
├── composer.json          # PHP 项目配置和自动加载定义
├── package.json           # Node.js 前端项目配置
├── vite.config.js         # Vite 构建配置
├── index.php              # PHP 应用入口文件
├── index.html             # 前端 HTML 入口
├── .htaccess              # Apache URL 重写配置
├── AGENTS.md              # 项目文档
├── phpemsvue.sql           # 数据库初始化脚本
├── boot/                  # PHP 启动文件
│   ├── boot.php           # 核心启动逻辑
│   ├── cli.php            # CLI 启动脚本
│   └── helpers.php        # 辅助函数
├── app/                   # PHP 应用模块
│   ├── Attach/            # 附件模块
│   │   └── Controller/Api/ # API 控制器
│   ├── Cert/              # 证书模块（新增）
│   │   ├── Controller/    # 控制器 (App/Master)
│   │   └── Service/       # 服务层和模型
│   ├── Content/           # 内容模块
│   │   ├── Controller/    # 控制器 (App/Master/Phone)
│   │   ├── Service/Model/ # 服务层和模型
│   │   └── Tpls/          # 模板文件
│   ├── Core/              # 核心模块
│   │   ├── Controller/    # 控制器 (App/Master/Phone/Utils)
│   │   ├── Service/Model/ # 服务层和模型
│   │   └── Tpls/          # 模板文件
│   ├── Course/            # 课程模块
│   │   ├── Controller/    # 控制器 (App/Master)
│   │   └── Service/Model/ # 服务层和模型
│   ├── Crypto/            # 加密模块（新增）
│   │   ├── Controller/    # 控制器 (Api/App)
│   │   ├── Service/       # 服务层
│   │   └── README.md      # 模块文档
│   ├── Exam/              # 考试模块
│   │   ├── Controller/    # 控制器 (App/Master)
│   │   └── Service/       # 服务层和模型
│   ├── User/              # 用户模块
│   │   ├── Controller/    # 控制器 (App/Master)
│   │   └── Service/       # 服务层和模型
│   └── Plugin/            # 插件模块
├── lib/                   # PHP 核心库
│   ├── AI/                # AI 客户端
│   │   └── AIClient.php   # AI 服务接口
│   ├── Auth/              # 认证系统
│   │   ├── Auth.php       # 认证核心
│   │   ├── DataProvider.php # 数据提供者
│   │   └── Drivers/       # 认证驱动
│   ├── Cache/             # 缓存组件 (预留)
│   ├── Config/            # 配置组件
│   │   ├── Config.php     # 配置管理器
│   │   ├── DataBase/      # 数据库配置
│   │   │   ├── MySql.php  # MySQL 配置
│   │   │   └── Redis.php  # Redis 配置
│   │   └── Site/          # 站点配置
│   ├── Core/              # 核心组件
│   │   ├── Router.php     # 路由分发器
│   │   ├── Flow/          # 流程中间件
│   │   │   ├── Init.php   # 初始化流程
│   │   │   ├── Auth.php   # 认证流程
│   │   │   ├── Json.php   # JSON 处理
│   │   │   └── Tpl.php    # 模板处理
│   │   └── Request/       # 请求处理
│   ├── DataBase/          # 数据库组件
│   │   ├── DB.php         # 数据库操作类
│   │   ├── DBProvider.php # 数据库提供者
│   │   ├── QueryBuilder.php # 查询构建器
│   │   └── RedisClient.php # Redis 客户端
│   ├── DI/                # 依赖注入
│   │   └── DI.php         # DI 容器实现
│   ├── Http/              # HTTP 组件
│   │   └── Cookie.php     # Cookie 管理
│   ├── Rules/             # 规则系统
│   │   ├── Controller.php # 控制器基类
│   │   ├── ControllerInterface.php # 控制器接口
│   │   ├── Error.php      # 错误处理
│   │   ├── Message.php    # 消息处理
│   │   └── Model.php      # 模型基类
│   ├── Session/           # 会话管理
│   │   ├── SessionDriverInterface.php # 会话驱动接口
│   │   ├── SessionProvider.php # 会话提供者
│   │   └── Drivers/       # 会话驱动实现
│   ├── Tpl/               # 模板引擎
│   │   ├── Tpl.php        # 模板引擎核心
│   │   ├── TplProvider.php # 模板提供者
│   │   └── readme.md      # 模板引擎文档
│   └── Utils/             # 工具类
│       ├── Captcha.php    # 验证码
│       ├── Env.php        # 环境变量
│       ├── FileLogger.php # 文件日志
│       ├── FileProvider.php # 文件提供者
│       ├── Style.php      # 样式管理
│       ├── Validator.php  # 数据验证器
│       ├── Crypto/        # 加密工具类（新增）
│       ├── FileService/   # 文件服务
│       └── Office/        # Office 文件处理
├── frontend/              # 前端源码
│   ├── App.vue            # 根组件
│   ├── main.js            # 前端入口文件
│   ├── env.d.ts           # TypeScript 类型定义
│   ├── config/            # 前端配置
│   │   └── index.js       # 配置文件
│   ├── assets/            # 静态资源
│   │   ├── css/           # 样式文件
│   │   │   ├── main.css   # 主样式
│   │   │   ├── desktop/   # PC 端样式
│   │   │   └── mobile/    # 移动端样式
│   │   └── images/        # 图片资源
│   ├── components/        # 组件库
│   │   ├── desktop/       # PC 端组件
│   │   ├── master/        # 管理端组件
│   │   └── mobile/        # 移动端组件
│   ├── framework/         # 前端框架层
│   │   ├── api/           # API 调用层
│   │   │   ├── admin/     # 管理端 API
│   │   │   ├── attach.js  # 附件 API
│   │   │   ├── auth.js    # 认证 API
│   │   │   ├── card.js    # 卡片 API
│   │   │   ├── content.js # 内容 API（新增）
│   │   │   ├── course.js  # 课程 API（新增）
│   │   │   ├── exam.js    # 考试 API（新增）
│   │   │   ├── index.js   # API 入口
│   │   │   ├── order.js   # 订单 API
│   │   │   ├── plan.js    # 计划 API（新增）
│   │   │   ├── project.js # 项目 API
│   │   │   ├── user.js    # 用户 API
│   │   │   ├── utils.js   # 工具 API
│   │   │   └── wechat.js  # 微信 API
│   │   ├── http/          # HTTP 请求层
│   │   │   ├── axios.js   # Axios 配置
│   │   │   └── index.js   # 请求封装
│   │   ├── security/      # 安全模块
│   │   │   └── index.js   # 加密解密
│   │   ├── ui/            # UI 框架加载
│   │   │   └── index.js   # UI 加载器
│   │   └── utils/         # 工具函数
│   │       ├── decorators.js # 装饰器
│   │       ├── device.js  # 设备检测
│   │       ├── extension.js # 扩展函数
│   │       ├── mobile/    # 移动端工具
│   │       └── tools.js   # 通用工具
│   ├── router/            # 路由配置
│   │   ├── index.js       # 路由主入口
│   │   ├── home.js        # 首页路由
│   │   ├── master.js      # 管理端路由
│   │   └── mobile.js      # 移动端路由
│   ├── stores/            # 状态管理
│   │   └── auth.js        # 认证状态
│   └── views/             # 页面视图
│       ├── Index.vue      # 首页
│       ├── NotFound.vue   # 404 页面
│       ├── desktop/       # PC 端页面
│       │   ├── home/      # 首页模块
│       │   └── master/    # 管理模块
│       └── mobile/        # 移动端页面
│           ├── auth/      # 认证模块
│           └── card/      # 卡片模块
├── plugins/               # 插件目录
│   └── Demo/              # 示例插件
│       └── Tpls/          # 插件模板
├── public/                # 公共资源目录
│   └── assets/            # 前端构建输出
├── resources/             # 资源文件
│   ├── styles/            # 样式资源
│   │   ├── layui/         # LayUI 框架
│   │   ├── phpems/        # 自定义样式
│   │   └── wangEditor/    # 富文本编辑器
│   └── wasm/              # WebAssembly 资源（新增）
│       ├── release.d.ts   # TypeScript 类型定义
│       ├── release.js     # WASM JavaScript 绑定
│       └── release.wasm   # WASM 二进制文件
├── storage/               # 存储目录
│   ├── attach/            # 附件存储
│   │   ├── private/       # 私有文件
│   │   └── public/        # 公开文件
│   └── logs/              # 日志文件
├── doc/                   # 项目文档
│   ├── controller.md      # 控制器文档
│   ├── model.md           # 模型文档
│   └── process_tasks_help.md # 流程任务帮助文档
└── vendor/                # Composer 依赖
```
## 后端路由机制

系统使用基于 URL 路径的路由机制，支持流程中间件：

- URL 格式: `/module/controller/action/param1/param2/...`
- 默认路由: `/core/app/index`
- 模块路由:
  - 核心模块: `PHPEMS\App\{Module}\Controller\{Controller}\{Action}`
  - 插件模块: `PHPEMS\Plugins\{Plugin}\Controller\{Controller}\{Action}`

### 路由示例
- `/core/app/index` → `PHPEMS\App\Core\Controller\App\Index::index()`
- `/exam/test/start` → `PHPEMS\App\Exam\Controller\Test\Start::index()`
- `/content/article/list` → `PHPEMS\App\Content\Controller\Article\List::index()`
- `/plan/app/member/data` → `PHPEMS\App\Plan\Controller\App\Member::data()`
- `/crypto/api/encrypt/encrypt` → `PHPEMS\App\Crypto\Controller\Api\Encrypt::encrypt()`
- `/plugins/myplugin/admin/dashboard` → `PHPEMS\Plugins\Myplugin\Controller\Admin\Dashboard::index()`

### 流程中间件

系统支持流程中间件，可在请求处理前/后执行逻辑：

- **Init**: 初始化流程（默认注册）
- **Auth**: 认证流程
- **Json**: JSON 响应处理
- **Tpl**: 模板渲染

控制器可通过以下方法控制流程：
- `withFlows($action)`: 为特定动作添加流程
- `withOutFlows($action)`: 为特定动作排除流程

## 前端路由系统

前端使用 Vue Router 实现客户端路由，支持 Hash 模式和自动设备适配。

### 路由结构

```
/                          → 根路径（自动重定向到设备首页）
├── /desktop               → PC 端布局
│   ├── /home              → 首页模块
│   │   ├── /auth          → 认证页面
│   │   │   ├── /login     → 登录
│   │   │   ├── /register  → 注册
│   │   │   └── /forget    → 忘记密码
│   │   └── /card          → 卡片模块
│   └── /master            → 管理模块
│       ├── /dashboard     → 仪表盘
│       ├── /exam          → 考试管理
│       └── /user          → 用户管理
└── /mobile                → 移动端布局
    ├── /auth              → 认证页面

### PC 端 UI (LayUI Vue)

- 组件库: @layui/layui-vue ^2.23.3
- 样式文件: assets/css/desktop/style.css
- 使用场景: 管理后台、桌面端页面

### 移动端 UI (Vant)

- 组件库: Vant ^4.9.22
- 样式文件: assets/css/mobile/style.css
- 使用场景: 移动端页面、H5 页面

### 富文本编辑器

系统集成了两种富文本编辑器：

**Vditor:**
- 版本: ^3.11.2
- 功能: Markdown 编辑、所见即所得
- 使用场景: 内容编辑、试题编辑

**WangEditor:**
- 版本: ^5.1.23
- 功能: 富文本编辑、所见即所得
- 使用场景: 文章内容编辑、文档编辑

### 视频播放器

- 播放器: Video.js ^8.23.4
- 功能: 视频播放、课程视频
- 使用场景: 课程学习、视频教程
### 数据库配置

**MySQL 配置:** `lib/Config/DataBase/MySql.php`
```php
'default' => [
    'type' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'user' => 'root',
    'password' => '密码',
    'database' => 'phpemsvue',
    'charset' => 'utf8',
    'tablePrefix' => 'x2_'
]
```
## 构建和运行

### 环境要求

**后端环境：**
- PHP >= 8.2
- JSON、PDO、Redis、Mbstring、Fileinfo、GD、OpenSSL、Zip 扩展
- MySQL 5.7+ 或 MariaDB 10.2+
- Redis 5.0+（可选）

**前端环境：**
- Node.js >= 16.0
- npm >= 8.0

### 安装步骤

1. **克隆或下载项目**
   ```bash
   git clone <repository-url>
   cd phpemslite
   ```

2. **安装后端依赖**
   ```bash
   composer install
   ```

3. **安装前端依赖**
   ```bash
   npm install
   ```

4. **配置数据库**
   - 修改 `lib/Config/DataBase/MySql.php` 配置文件
   - 导入 `phpemsvue.sql` 数据库脚本

5. **配置站点**
   - 修改 `lib/Config/Site/` 下的配置文件

6. **配置前端**
   - 修改 `frontend/config/index.js` 中的 API 地址

7. **确保 Web 服务器配置**
   - Web 服务器根目录指向项目根目录
   - 配置 URL 重写规则（.htaccess）
### 版本兼容性
- PHP >= 8.2 是最低要求
- 数据库版本要求: MySQL 5.7.7+ 或 MariaDB 10.2+
- Redis 5.0+ 用于会话和缓存（推荐）
- Node.js >= 16.0 用于前端开发
- OpenSSL 扩展用于加密功能（必需）

## 快速开始

### 开发环境快速启动

1. **安装依赖**
   ```bash
   composer install
   npm install
   ```

2. **配置**
   - 复制并编辑配置文件
   - 配置数据库连接
   - 配置前端 API 地址
   - 导入 `magnolia.sql` 数据库脚本

3. **启动开发服务器**
   ```bash
   # 终端 1
   npm run dev

   # 终端 2（如果需要）
   php -S localhost:8000
   ```

4. **访问应用**
   - 前端开发服务器: http://localhost:5173
   - 后端 API: http://localhost:8000 或配置的域名

### 生产环境部署

1. **构建前端**
   ```bash
   npm run build
   ```

2. **配置 Web 服务器**
   - 指向项目根目录
   - 配置 URL 重写规则
   - 配置 HTTPS（推荐）

3. **设置文件权限**
   - storage/ 目录可写
   - logs/ 目录可写

4. **配置缓存**
   - 启用 Redis
   - 配置 Redis 连接

5. **监控日志**
   - 定期检查 `storage/logs/error.log`

## 联系与支持

如有问题或建议，请通过以下方式联系：
- 提交 Issue
- 官网 https://phpems.net
- 演示 https://lite.phpems.net
- 演示站账号密码均为 peadmin
- 查看文档

---

**最后更新**: 2026-07-18
