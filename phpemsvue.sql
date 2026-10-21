/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 80029 (8.0.29)
 Source Host           : localhost:3306
 Source Schema         : phpemsvue

 Target Server Type    : MySQL
 Target Server Version : 80029 (8.0.29)
 File Encoding         : 65001

 Date: 20/10/2026 11:24:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for x2_ask
-- ----------------------------
DROP TABLE IF EXISTS `x2_ask`;
CREATE TABLE `x2_ask`  (
  `askid` int NOT NULL AUTO_INCREMENT,
  `askuserid` int NULL DEFAULT NULL,
  `asktitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `asktime` int NULL DEFAULT NULL,
  `askcoin` int NULL DEFAULT NULL,
  `askcontent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `askisshow` int NULL DEFAULT NULL,
  `askstatus` int NULL DEFAULT NULL,
  `askorder` int NULL DEFAULT NULL,
  PRIMARY KEY (`askid`) USING BTREE,
  INDEX `askuserid`(`askuserid` ASC) USING BTREE,
  INDEX `askstatus`(`askstatus` ASC) USING BTREE,
  INDEX `askisshow`(`askisshow` ASC) USING BTREE,
  INDEX `askorder`(`askorder` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_ask
-- ----------------------------

-- ----------------------------
-- Table structure for x2_ask_answer
-- ----------------------------
DROP TABLE IF EXISTS `x2_ask_answer`;
CREATE TABLE `x2_ask_answer`  (
  `asrid` int NOT NULL AUTO_INCREMENT,
  `asruserid` int NULL DEFAULT NULL,
  `asraskid` int NULL DEFAULT NULL,
  `asrcontent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `asrtime` int NULL DEFAULT NULL,
  `asrstatus` int NULL DEFAULT NULL,
  PRIMARY KEY (`asrid`) USING BTREE,
  INDEX `asruserid`(`asruserid` ASC) USING BTREE,
  INDEX `asraskid`(`asraskid` ASC) USING BTREE,
  INDEX `asrstatus`(`asrstatus` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_ask_answer
-- ----------------------------

-- ----------------------------
-- Table structure for x2_cert
-- ----------------------------
DROP TABLE IF EXISTS `x2_cert`;
CREATE TABLE `x2_cert`  (
  `ceid` int NOT NULL AUTO_INCREMENT,
  `cetitle` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `ceicon` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `cethumb` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `cedays` int NULL DEFAULT NULL,
  `cetime` int NULL DEFAULT NULL,
  `cenumber` int NULL DEFAULT NULL,
  `cetpl` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `cetags` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `cedescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `cetext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  PRIMARY KEY (`ceid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_cert
-- ----------------------------
INSERT INTO `x2_cert` VALUES (1, '测试凭证', '', 'storage/attach/public/20260728/7e4c63d85e4d92041c626974b6b1e137.jpg', 365, 0, NULL, 'storage/attach/public/20260728/41e40e1abcf03c20399a82f8b708d875.jpg', '111', '111', '<p>1111</p>');

-- ----------------------------
-- Table structure for x2_cert_member
-- ----------------------------
DROP TABLE IF EXISTS `x2_cert_member`;
CREATE TABLE `x2_cert_member`  (
  `cemid` int NOT NULL AUTO_INCREMENT,
  `cemceid` int NOT NULL,
  `cempassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `cemtime` int NOT NULL,
  `cemstatus` tinyint(1) NOT NULL,
  `cemsn` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `cemexpirytime` int NULL DEFAULT NULL,
  PRIMARY KEY (`cemid`) USING BTREE,
  INDEX `cempassport`(`cempassport` ASC) USING BTREE,
  INDEX `cemtime`(`cemtime` ASC) USING BTREE,
  INDEX `cemstatus`(`cemstatus` ASC) USING BTREE,
  INDEX `cemsn`(`cemsn` ASC) USING BTREE,
  INDEX `cemceid`(`cemceid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_cert_member
-- ----------------------------

-- ----------------------------
-- Table structure for x2_content
-- ----------------------------
DROP TABLE IF EXISTS `x2_content`;
CREATE TABLE `x2_content`  (
  `contentid` int NOT NULL AUTO_INCREMENT,
  `contentcatid` int NOT NULL DEFAULT 0,
  `contentmoduleid` int NOT NULL DEFAULT 0,
  `contentuserid` int NOT NULL DEFAULT 0,
  `contentusername` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `contentmodifier` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `contenttitle` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `contenttags` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `contentkeywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `contentthumb` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `contentlink` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `contentinputtime` int NOT NULL DEFAULT 0,
  `contentmodifytime` int NOT NULL DEFAULT 0,
  `contentsequence` int NOT NULL DEFAULT 0,
  `contentdescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `contentinfo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `contentstatus` int NOT NULL DEFAULT 0,
  `contenttemplate` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `contenttext` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `contentview` int NULL DEFAULT NULL,
  PRIMARY KEY (`contentid`) USING BTREE,
  INDEX `contentuserid`(`contentuserid` ASC, `contentinputtime` ASC, `contentmodifytime` ASC, `contentsequence` ASC) USING BTREE,
  INDEX `contentmoduleid`(`contentmoduleid` ASC) USING BTREE,
  INDEX `contentcatid`(`contentcatid` ASC) USING BTREE,
  INDEX `contentstatus`(`contentstatus` ASC) USING BTREE,
  INDEX `contenttags`(`contenttags` ASC) USING BTREE,
  FULLTEXT INDEX `contentkeywords`(`contentkeywords`)
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_content
-- ----------------------------

-- ----------------------------
-- Table structure for x2_content_category
-- ----------------------------
DROP TABLE IF EXISTS `x2_content_category`;
CREATE TABLE `x2_content_category`  (
  `catid` int NOT NULL AUTO_INCREMENT,
  `catlite` int NOT NULL DEFAULT 0,
  `catname` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `catthumb` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `caturl` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `catuseurl` int NOT NULL DEFAULT 0,
  `catparent` int NULL DEFAULT 0,
  `catdes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`catid`) USING BTREE,
  INDEX `catlite`(`catlite` ASC, `catparent` ASC) USING BTREE,
  INDEX `catuseurl`(`catuseurl` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_content_category
-- ----------------------------
INSERT INTO `x2_content_category` VALUES (1, 0, 'PHPEMS', '', '', 0, 0, '');

-- ----------------------------
-- Table structure for x2_course
-- ----------------------------
DROP TABLE IF EXISTS `x2_course`;
CREATE TABLE `x2_course`  (
  `courseid` int NOT NULL AUTO_INCREMENT,
  `coursetitle` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `coursemodule` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `coursecsid` int NULL DEFAULT NULL,
  `coursedirid` int NULL DEFAULT NULL,
  `coursethumb` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `courseuserid` int NULL DEFAULT NULL,
  `courseinputtime` int NULL DEFAULT NULL,
  `coursemodifytime` int NULL DEFAULT NULL,
  `coursesequence` int NULL DEFAULT NULL,
  `coursedescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `coursepath` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `coursepasstime` int NOT NULL,
  PRIMARY KEY (`courseid`) USING BTREE,
  INDEX `coursemodule`(`coursemodule` ASC) USING BTREE,
  INDEX `coursecsid`(`coursecsid` ASC) USING BTREE,
  INDEX `coursedirid`(`coursedirid` ASC) USING BTREE,
  INDEX `coursesequence`(`coursesequence` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course
-- ----------------------------
INSERT INTO `x2_course` VALUES (1, '安装使用', 'dir', 1, 0, 'storage/attach/public/20260721/1c7a5edebd6d22ed741b75a463bd788b.png', 1, 1784601138, 1784601138, 0, '<p>介绍 PHPEMS LITE 的安装和使用</p>', '', 0);
INSERT INTO `x2_course` VALUES (2, '运行环境', 'md', 1, 1, 'storage/attach/public/20260721/c10ad228e076e598f885ca8c5aefae75.png', 1, 1784601569, 1784601579, 0, '# 服务器环境\n\nPHP >= 8.2\n\nMySQL >= 5.7.7\n\nRedis >= 5.0 （可选）\n\n# 浏览器\n\n谷歌浏览器\n\nEdge浏览器\n', '', 0);
INSERT INTO `x2_course` VALUES (3, '详细信息', 'md', 1, 1, 'storage/attach/public/20260721/1dd1c5cea4c0213ecec6678d4c5ee3d7.png', 1, 1784601771, 1784601777, 0, '# PHPEMS Lite  项目\n\n## 项目概述\n\nPHPEMS LITE (PHPEMS v12.0.0) 是一个基于 PHP 的考试管理系统。该项目采用前后端分离架构，后端使用自定义 MVC 框架（Magnolia），前端使用 Vue 3 + Vite 构建现代化用户界面。\n\n### 主要特性\n\n- **前后端分离架构**: 后端提供 RESTful API，前端使用 Vue 3 + Vite 构建\n- **响应式设计**: 支持 PC 端（桌面）和移动端两种布局，自动适配设备类型\n- **模块化设计**: 核心功能分为 Core、Course、Exam、User、Content、Attach、Cert、Crypto、Plan 等模块\n- **依赖注入系统**: 内置 DI 容器用于管理服务依赖\n- **路由系统**: 后端基于 URL 路径的动态路由分发，前端使用 Vue Router\n- **插件系统**: 支持通过 plugins 目录扩展功能\n- **PSR-4 自动加载**: 使用 Composer 进行类自动加载\n- **多数据库支持**: MySQL 和 Redis （可选）支持\n- **状态管理**: 前端使用 Pinia 进行状态管理\n- **安全加密**: 前后端支持数据加密传输（AES-256-CBC），集成 WASM 加密模块（可选）\n- **WebAssembly 支持**: 集成 WASM 模块用于高性能加密解密操作（可选）\n\n### 技术栈\n\n**后端技术栈：**\n\n- **语言**: PHP >= 8.2\n- **架构模式**: 自定义 MVC + 依赖注入 + 流程中间件\n- **包管理**: Composer\n- **核心组件**:\n  - 路由器 (Router)\n  - 依赖注入容器 (DI)\n  - 请求处理 (Request Provider)\n  - 数据库 (DB/Redis)\n  - 认证系统 (Auth)\n  - 会话管理 (Session)\n\n**前端技术栈：**\n\n- **框架**: Vue 3.4+\n- **构建工具**: Vite 5.0+\n- **状态管理**: Pinia 3.0+\n- **路由**: Vue Router 4.6+\n- **HTTP 客户端**: Axios 1.13+\n- **UI 组件库**:\n  - PC 端: LayUI Vue 2.23+\n  - 移动端: Vant 4.9+\n- **富文本编辑器**:\n  - Vditor 3.11+\n  - WangEditor 5.1+\n- **视频播放器**: Video.js 8.23+\n- **加密库**: CryptoJS 4.2+\n- **轮播组件**: Swiper 12.1+\n- **拖拽排序**: SortableJS 1.15+\n- **开发模式**: ES Modules\n\n### 依赖要求\n\n**PHP 扩展：**\n\n- ext-json\n- ext-pdo\n- ext-redis\n- ext-mbstring\n- ext-fileinfo\n- ext-gd\n- ext-openssl\n- ext-zip\n\n**Composer 依赖：**\n\n- guzzlehttp/guzzle ^7.10\n- psr/log ^3.0\n\n**开发依赖 (require-dev)：**\n\n- phpunit/phpunit ^9.5\n- mockery/mockery ^1.5\n- phpstan/phpstan ^1.10\n\n**Node.js 依赖：**\n\n- @layui/layui-vue ^2.23.3\n- @wangeditor/editor ^5.1.23\n- @wangeditor/editor-for-vue ^5.1.12\n- axios ^1.13.2\n- crypto-js ^4.2.0\n- pinia ^3.0.4\n- swiper ^12.1.3\n- sortablejs ^1.15.2\n- vant ^4.9.22\n- vditor ^3.11.2\n- video.js ^8.23.4\n- vue ^3.4.0\n- vue-router ^4.6.4\n\n## 项目结构\n\n```\nmagnolia/\n├── composer.json          # PHP 项目配置和自动加载定义\n├── package.json           # Node.js 前端项目配置\n├── vite.config.js         # Vite 构建配置\n├── index.php              # PHP 应用入口文件\n├── index.html             # 前端 HTML 入口\n├── .htaccess              # Apache URL 重写配置\n├── AGENTS.md              # 项目文档\n├── phpemsvue.sql           # 数据库初始化脚本\n├── boot/                  # PHP 启动文件\n│   ├── boot.php           # 核心启动逻辑\n│   ├── cli.php            # CLI 启动脚本\n│   └── helpers.php        # 辅助函数\n├── app/                   # PHP 应用模块\n│   ├── Attach/            # 附件模块\n│   │   └── Controller/Api/ # API 控制器\n│   ├── Cert/              # 证书模块（新增）\n│   │   ├── Controller/    # 控制器 (App/Master)\n│   │   └── Service/       # 服务层和模型\n│   ├── Content/           # 内容模块\n│   │   ├── Controller/    # 控制器 (App/Master/Phone)\n│   │   ├── Service/Model/ # 服务层和模型\n│   │   └── Tpls/          # 模板文件\n│   ├── Core/              # 核心模块\n│   │   ├── Controller/    # 控制器 (App/Master/Phone/Utils)\n│   │   ├── Service/Model/ # 服务层和模型\n│   │   └── Tpls/          # 模板文件\n│   ├── Course/            # 课程模块\n│   │   ├── Controller/    # 控制器 (App/Master)\n│   │   └── Service/Model/ # 服务层和模型\n│   ├── Crypto/            # 加密模块（新增）\n│   │   ├── Controller/    # 控制器 (Api/App)\n│   │   ├── Service/       # 服务层\n│   │   └── README.md      # 模块文档\n│   ├── Exam/              # 考试模块\n│   │   ├── Controller/    # 控制器 (App/Master)\n│   │   └── Service/       # 服务层和模型\n│   ├── Plan/              # 计划模块（新增）\n│   │   ├── Controller/    # 控制器 (App/Master)\n│   │   └── Service/       # 服务层和模型\n│   ├── User/              # 用户模块\n│   │   ├── Controller/    # 控制器 (App/Master)\n│   │   └── Service/       # 服务层和模型\n│   └── Plugin/            # 插件模块\n├── cli/                   # CLI 脚本工具\n│   ├── generate_controller.php  # 控制器生成脚本\n│   ├── generate_model.php       # 模型生成脚本\n│   └── generate_questions.php   # 试题生成脚本（新增）\n├── lib/                   # PHP 核心库\n│   ├── AI/                # AI 客户端\n│   │   └── AIClient.php   # AI 服务接口\n│   ├── Auth/              # 认证系统\n│   │   ├── Auth.php       # 认证核心\n│   │   ├── DataProvider.php # 数据提供者\n│   │   └── Drivers/       # 认证驱动\n│   ├── Cache/             # 缓存组件 (预留)\n│   ├── Config/            # 配置组件\n│   │   ├── Config.php     # 配置管理器\n│   │   ├── DataBase/      # 数据库配置\n│   │   │   ├── MySql.php  # MySQL 配置\n│   │   │   └── Redis.php  # Redis 配置\n│   │   └── Site/          # 站点配置\n│   ├── Core/              # 核心组件\n│   │   ├── Router.php     # 路由分发器\n│   │   ├── Flow/          # 流程中间件\n│   │   │   ├── Init.php   # 初始化流程\n│   │   │   ├── Auth.php   # 认证流程\n│   │   │   ├── Json.php   # JSON 处理\n│   │   │   └── Tpl.php    # 模板处理\n│   │   └── Request/       # 请求处理\n│   ├── DataBase/          # 数据库组件\n│   │   ├── DB.php         # 数据库操作类\n│   │   ├── DBProvider.php # 数据库提供者\n│   │   ├── QueryBuilder.php # 查询构建器\n│   │   └── RedisClient.php # Redis 客户端\n│   ├── DI/                # 依赖注入\n│   │   └── DI.php         # DI 容器实现\n│   ├── Http/              # HTTP 组件\n│   │   └── Cookie.php     # Cookie 管理\n│   ├── Rules/             # 规则系统\n│   │   ├── Controller.php # 控制器基类\n│   │   ├── ControllerInterface.php # 控制器接口\n│   │   ├── Error.php      # 错误处理\n│   │   ├── Message.php    # 消息处理\n│   │   └── Model.php      # 模型基类\n│   ├── Session/           # 会话管理\n│   │   ├── SessionDriverInterface.php # 会话驱动接口\n│   │   ├── SessionProvider.php # 会话提供者\n│   │   └── Drivers/       # 会话驱动实现\n│   ├── Tpl/               # 模板引擎\n│   │   ├── Tpl.php        # 模板引擎核心\n│   │   ├── TplProvider.php # 模板提供者\n│   │   └── readme.md      # 模板引擎文档\n│   └── Utils/             # 工具类\n│       ├── Captcha.php    # 验证码\n│       ├── Env.php        # 环境变量\n│       ├── FileLogger.php # 文件日志\n│       ├── FileProvider.php # 文件提供者\n│       ├── Style.php      # 样式管理\n│       ├── Validator.php  # 数据验证器\n│       ├── Crypto/        # 加密工具类（新增）\n│       ├── FileService/   # 文件服务\n│       └── Office/        # Office 文件处理\n├── frontend/              # 前端源码\n│   ├── App.vue            # 根组件\n│   ├── main.js            # 前端入口文件\n│   ├── env.d.ts           # TypeScript 类型定义\n│   ├── config/            # 前端配置\n│   │   └── index.js       # 配置文件\n│   ├── assets/            # 静态资源\n│   │   ├── css/           # 样式文件\n│   │   │   ├── main.css   # 主样式\n│   │   │   ├── desktop/   # PC 端样式\n│   │   │   └── mobile/    # 移动端样式\n│   │   └── images/        # 图片资源\n│   ├── components/        # 组件库\n│   │   ├── desktop/       # PC 端组件\n│   │   ├── master/        # 管理端组件\n│   │   └── mobile/        # 移动端组件\n│   ├── framework/         # 前端框架层\n│   │   ├── api/           # API 调用层\n│   │   │   ├── admin/     # 管理端 API\n│   │   │   ├── attach.js  # 附件 API\n│   │   │   ├── auth.js    # 认证 API\n│   │   │   ├── card.js    # 卡片 API\n│   │   │   ├── content.js # 内容 API（新增）\n│   │   │   ├── course.js  # 课程 API（新增）\n│   │   │   ├── exam.js    # 考试 API（新增）\n│   │   │   ├── index.js   # API 入口\n│   │   │   ├── order.js   # 订单 API\n│   │   │   ├── plan.js    # 计划 API（新增）\n│   │   │   ├── project.js # 项目 API\n│   │   │   ├── user.js    # 用户 API\n│   │   │   ├── utils.js   # 工具 API\n│   │   │   └── wechat.js  # 微信 API\n│   │   ├── http/          # HTTP 请求层\n│   │   │   ├── axios.js   # Axios 配置\n│   │   │   └── index.js   # 请求封装\n│   │   ├── security/      # 安全模块\n│   │   │   └── index.js   # 加密解密\n│   │   ├── ui/            # UI 框架加载\n│   │   │   └── index.js   # UI 加载器\n│   │   └── utils/         # 工具函数\n│   │       ├── decorators.js # 装饰器\n│   │       ├── device.js  # 设备检测\n│   │       ├── extension.js # 扩展函数\n│   │       ├── mobile/    # 移动端工具\n│   │       └── tools.js   # 通用工具\n│   ├── router/            # 路由配置\n│   │   ├── index.js       # 路由主入口\n│   │   ├── home.js        # 首页路由\n│   │   ├── master.js      # 管理端路由\n│   │   └── mobile.js      # 移动端路由\n│   ├── stores/            # 状态管理\n│   │   └── auth.js        # 认证状态\n│   └── views/             # 页面视图\n│       ├── Index.vue      # 首页\n│       ├── NotFound.vue   # 404 页面\n│       ├── desktop/       # PC 端页面\n│       │   ├── home/      # 首页模块\n│       │   └── master/    # 管理模块\n│       └── mobile/        # 移动端页面\n│           ├── auth/      # 认证模块\n│           └── card/      # 卡片模块\n├── plugins/               # 插件目录\n│   └── Demo/              # 示例插件\n│       └── Tpls/          # 插件模板\n├── public/                # 公共资源目录\n│   └── assets/            # 前端构建输出\n├── resources/             # 资源文件\n│   ├── styles/            # 样式资源\n│   │   ├── layui/         # LayUI 框架\n│   │   ├── phpems/        # 自定义样式\n│   │   └── wangEditor/    # 富文本编辑器\n│   └── wasm/              # WebAssembly 资源（新增）\n│       ├── release.d.ts   # TypeScript 类型定义\n│       ├── release.js     # WASM JavaScript 绑定\n│       └── release.wasm   # WASM 二进制文件\n├── storage/               # 存储目录\n│   ├── attach/            # 附件存储\n│   │   ├── private/       # 私有文件\n│   │   └── public/        # 公开文件\n│   └── logs/              # 日志文件\n├── doc/                   # 项目文档\n│   ├── controller.md      # 控制器文档\n│   ├── model.md           # 模型文档\n│   └── process_tasks_help.md # 流程任务帮助文档\n└── vendor/                # Composer 依赖\n```\n\n## 后端路由机制\n\n系统使用基于 URL 路径的路由机制，支持流程中间件：\n\n- URL 格式: `/module/controller/action/param1/param2/...`\n- 默认路由: `/core/app/index`\n- 模块路由:\n  - 核心模块: `PHPEMS\\App\\{Module}\\Controller\\{Controller}\\{Action}`\n  - 插件模块: `PHPEMS\\Plugins\\{Plugin}\\Controller\\{Controller}\\{Action}`\n\n### 路由示例\n\n- `/core/app/index` → `PHPEMS\\App\\Core\\Controller\\App\\Index::index()`\n- `/exam/test/start` → `PHPEMS\\App\\Exam\\Controller\\Test\\Start::index()`\n- `/content/article/list` → `PHPEMS\\App\\Content\\Controller\\Article\\List::index()`\n- `/plan/app/member/data` → `PHPEMS\\App\\Plan\\Controller\\App\\Member::data()`\n- `/crypto/api/encrypt/encrypt` → `PHPEMS\\App\\Crypto\\Controller\\Api\\Encrypt::encrypt()`\n- `/plugins/myplugin/admin/dashboard` → `PHPEMS\\Plugins\\Myplugin\\Controller\\Admin\\Dashboard::index()`\n\n### 流程中间件\n\n系统支持流程中间件，可在请求处理前/后执行逻辑：\n\n- **Init**: 初始化流程（默认注册）\n- **Auth**: 认证流程\n- **Json**: JSON 响应处理\n- **Tpl**: 模板渲染\n\n控制器可通过以下方法控制流程：\n\n- `withFlows($action)`: 为特定动作添加流程\n- `withOutFlows($action)`: 为特定动作排除流程\n\n## 前端路由系统\n\n前端使用 Vue Router 实现客户端路由，支持 Hash 模式和自动设备适配。\n\n### 路由结构\n\n```\n/                          → 根路径（自动重定向到设备首页）\n├── /desktop               → PC 端布局\n│   ├── /home              → 首页模块\n│   │   ├── /auth          → 认证页面\n│   │   │   ├── /login     → 登录\n│   │   │   ├── /register  → 注册\n│   │   │   └── /forget    → 忘记密码\n│   │   └── /card          → 卡片模块\n│   └── /master            → 管理模块\n│       ├── /dashboard     → 仪表盘\n│       ├── /exam          → 考试管理\n│       └── /user          → 用户管理\n└── /mobile                → 移动端布局\n    ├── /auth              → 认证页面\n    └── /card              → 卡片模块\n```\n\n### 设备适配\n\n- 根路径 `/` 自动根据设备类型重定向\n  - PC 设备 → `/desktop/home`\n  - 移动设备 → `/mobile`\n- 防止设备类型误入错误布局\n  - PC 用户访问移动端路由 → 重定向到 PC 首页\n  - 移动用户访问 PC 端路由 → 重定向到移动端首页\n\n### 路由守卫\n\n路由守卫在 `router/index.js` 中配置，处理：\n\n1. 设备类型自动重定向\n2. 防止跨设备访问\n3. 认证状态检查（通过 meta.requiresAuth）\n\n## 前端状态管理\n\n使用 Pinia 进行状态管理，支持持久化存储。\n\n### Auth Store\n\n位置: `frontend/stores/auth.js`\n\n```javascript\nimport { useAuthStore } from \'@/stores/auth\';\n\n// 获取状态\nconst authStore = useAuthStore();\nconst isLoggedIn = authStore.isLoggedIn;\nconst userInfo = authStore.userInfo;\nconst isMaster = authStore.isMaster;\n\n// 方法\nawait authStore.getCurrentUser(); // 获取当前用户\nauthStore.clearUser(); // 清除用户信息\n```\n\n**状态字段：**\n\n- `user`: 用户信息（加密存储在 localStorage）\n- `isLoggedIn`: 是否已登录\n- `userInfo`: 用户信息 getter\n- `isMaster`: 是否为管理员\n\n**持久化：**\n\n- 用户信息自动加密存储在 localStorage\n- 使用 AES-256-CBC 加密算法\n\n## 前端 API 调用\n\n### API 架构\n\n使用代理模式实现动态 API 模块加载，支持按需加载和缓存。\n\n### 使用示例\n\n```javascript\nimport api from \'@/framework/api\';\n\n// 调用 API\nconst user = await api.userApi.getCurrentUser();\nconst data = await api.authApi.login(username, password);\n```\n\n### API 模块\n\n位置: `frontend/framework/api/`\n\n- `auth.js`: 认证相关 API\n- `user.js`: 用户相关 API\n- `project.js`: 项目相关 API\n- `card.js`: 卡片相关 API\n- `content.js`: 内容相关 API（新增）\n- `course.js`: 课程相关 API（新增）\n- `exam.js`: 考试相关 API（新增）\n- `plan.js`: 计划相关 API（新增）\n- `utils.js`: 工具 API\n- `attach.js`: 附件 API\n- `order.js`: 订单 API\n- `wechat.js`: 微信 API\n- `admin/`: 管理端 API\n  - `task.js`: 任务 API\n  - `user.js`: 用户管理 API\n  - `card.js`: 卡片管理 API\n  - `order.js`: 订单管理 API\n\n### HTTP 请求\n\n统一封装在 `frontend/framework/http/index.js`：\n\n```javascript\nimport { get, post } from \'@/framework/http\';\n\n// GET 请求\nconst data = await get(\'/api/user/profile\', { id: 1 });\n\n// POST 请求\nconst result = await post(\'/api/user/login\', { username, password });\n```\n\n### 响应处理\n\n统一响应格式：\n\n```javascript\n{\n  code: 200,    // 200: 成功, 310/311: 未认证, 其他: 错误\n  data: {},     // 业务数据\n  msg: \'\'       // 消息\n}\n```\n\n错误处理：\n\n- 代码 310/311: 自动跳转到登录页\n- 其他错误: 显示错误消息\n- 网络错误: 统一错误提示\n\n## 前端安全加密\n\n### 加密模块\n\n位置: `frontend/framework/security/index.js`\n\n### 使用方法\n\n```javascript\nimport { encrypt, decrypt } from \'@/framework/security\';\n\n// 加密数据\nconst encrypted = encrypt({ userId: 1, name: \'张三\' }, true);\n\n// 解密数据\nconst decrypted = decrypt(encrypted, true);\n```\n\n### 配置\n\n位置: `frontend/config/index.js`\n\n```javascript\nconst Config = {\n    hashData: false,      // 是否启用加密\n    hashKey: \'1234567812345678\',  // 加密密钥（16 字节）\n    hashIv: \'1234567812345678\',   // 初始化向量（16 字节）\n    devUrl: \"/api/magnolia/index.php/\",  // 开发环境 API 地址\n    buildUrl: \"/magnolia/index.php/\",    // 生产环境 API 地址\n    loginPage: \'/login\',              // 登录页面\n    wechatAppid: \'\'                   // 微信 AppID\n};\n\nConfig.url = import.meta.env.DEV ? Config.devUrl : Config.buildUrl;\n```\n\n### 加密算法\n\n- 算法: AES-256-CBC\n- 填充: PKCS7\n- 编码: Base64\n- 支持: UTF-8 中文\n\n### WASM 加密模块（新增）\n\n位置: `resources/wasm/`\n\n系统集成了 WebAssembly 加密模块，提供高性能加密解密操作：\n\n```javascript\nimport {\n    verifyLicense,\n    getLicense,\n    getCurrentDomain,\n    isLicenseValid,\n    getLicenseVersion,\n    encrypt,\n    decrypt,\n    generateKey,\n    generateIV\n} from \"/resources/wasm/release.js\";\n\n// 检查授权状态\nconst currentLicense = getLicense();\nconst licenseValid = isLicenseValid();\n\n// 生成密钥并加密\nconst key = generateKey();\nconst iv = generateIV();\nconst ciphertext = encrypt(\"敏感数据\", key, iv);\n```\n\n**WASM 模块特性：**\n\n- AES-256-CBC 加密算法\n- PKCS7 填充\n- UTF-8 字符编码支持（包括中文）\n- Base64 编码\n- ECDH 密钥交换\n- 会话密钥派生\n- 前后端完全兼容\n- 黑盒保护\n\n## 前端 UI 框架\n\n### 动态 UI 加载\n\n系统根据设备类型动态加载对应的 UI 框架：\n\n位置: `frontend/framework/ui/index.js`\n\n```javascript\nimport loadUI from \'@/framework/ui\';\n\n// PC 端: LayUI Vue\n// 移动端: Vant\n```\n\n### PC 端 UI (LayUI Vue)\n\n- 组件库: @layui/layui-vue ^2.23.3\n- 样式文件: assets/css/desktop/style.css\n- 使用场景: 管理后台、桌面端页面\n\n### 移动端 UI (Vant)\n\n- 组件库: Vant ^4.9.22\n- 样式文件: assets/css/mobile/style.css\n- 使用场景: 移动端页面、H5 页面\n\n### 富文本编辑器\n\n系统集成了两种富文本编辑器：\n\n**Vditor:**\n\n- 版本: ^3.11.2\n- 功能: Markdown 编辑、所见即所得\n- 使用场景: 内容编辑、试题编辑\n\n**WangEditor:**\n\n- 版本: ^5.1.23\n- 功能: 富文本编辑、所见即所得\n- 使用场景: 文章内容编辑、文档编辑\n\n### 视频播放器\n\n- 播放器: Video.js ^8.23.4\n- 功能: 视频播放、课程视频\n- 使用场景: 课程学习、视频教程\n\n### 其他 UI 组件\n\n**Swiper:**\n\n- 版本: ^12.1.3\n- 功能: 轮播图、滑动组件\n- 使用场景: 首页轮播、图片展示\n\n**SortableJS:**\n\n- 版本: ^1.15.2\n- 功能: 拖拽排序\n- 使用场景: 列表排序、卡片拖拽\n\n## 后端控制器规范\n\n所有控制器必须:\n\n1. 继承 `PHPEMS\\Lib\\Rules\\Controller`\n2. 实现 `PHPEMS\\Lib\\Rules\\ControllerInterface` 接口\n3. 提供 `getRoutes()` 方法返回路由映射\n4. 提供 `index()` 方法作为默认动作\n\n### 控制器接口\n\n```php\ninterface ControllerInterface\n{\n    public function getRoutes();  // 返回路由映射数组\n    public function index();      // 默认动作方法\n    public function withFlows($action);   // 为动作添加流程（可选）\n    public function withOutFlows($action); // 为动作排除流程（可选）\n}\n```\n\n### 控制器基类特性\n\n继承自 `PHPEMS\\Lib\\Rules\\Controller` 的控制器可访问：\n\n- `$this->request`: 请求对象\n- `$this->db`: 数据库对象\n- `$this->redis`: Redis 对象\n- `$this->tpl`: 模板对象\n- `$this->style`: 样式对象\n- `$this->user`: 当前用户对象（如果已认证）\n\n## 依赖注入系统\n\n项目内置简单的依赖注入容器 (`PHPEMS\\Lib\\DI\\DI`)，支持:\n\n- 单例模式 (`getInstance()`)\n- 服务绑定 (`bind()`)\n- 自动解析构造函数依赖\n- 闭包工厂函数\n\n### 预绑定服务\n\n在 `boot/boot.php` 中预绑定了以下服务：\n\n- `request`: 请求对象\n- `mysql.default`: MySQL 数据库连接（默认数据库）\n- `mysql.phpems`: MySQL 数据库连接（phpems 数据库）\n- `redis.default`: Redis 连接\n- `tpl.default`: 模板引擎\n- `style.default`: 样式管理器\n\n### 使用示例\n\n```php\n// 获取服务\n$request = DI::getInstance()->get(\'request\');\n$db = DI::getInstance()->get(\'mysql.default\');\n$phpemsDb = DI::getInstance()->get(\'mysql.phpems\');\n\n// 在控制器中直接访问\n$this->db->query(\"SELECT * FROM users\");\n```\n\n## 数据库系统\n\n### MySQL 支持\n\n- **DBProvider**: 数据库连接提供者\n- **DB**: 数据库操作类\n- **QueryBuilder**: 查询构建器\n\n### Redis 支持\n\n- **RedisClient**: Redis 客户端封装\n\n### 数据库配置\n\n**MySQL 配置:** `lib/Config/DataBase/MySql.php`\n\n```php\n\'default\' => [\n    \'type\' => \'mysql\',\n    \'host\' => \'127.0.0.1\',\n    \'port\' => \'3306\',\n    \'user\' => \'root\',\n    \'password\' => \'密码\',\n    \'database\' => \'magnolia\',\n    \'charset\' => \'utf8\',\n    \'tablePrefix\' => \'x2_\'\n]\n```\n\n**Redis 配置:** `lib/Config/DataBase/Redis.php`\n\n## 模板系统\n\n项目使用自定义模板引擎 (`PHPEMS\\Lib\\Tpl\\Tpl`)：\n\n- 模板文件位于各模块的 `Tpls/` 目录\n- 支持变量输出、循环、条件判断\n- 支持模板继承和包含\n- 集成 LayUI 框架和 WangEditor 富文本编辑器\n\n**注意**: 新版本主要使用 Vue 前端，模板引擎主要用于生成页面框架或特定场景。\n\n## 认证和会话系统\n\n### 认证系统\n\n- 支持多种认证驱动\n- 数据提供者抽象\n- 集成到 Auth 流程中间件\n- 前端使用 Pinia 管理认证状态\n\n### 会话管理\n\n- 支持多种会话驱动\n- 会话驱动接口定义\n- 会话提供者管理\n- 前端使用 localStorage 持久化用户信息（加密存储）\n\n## 新增模块详解\n\n### Cert 模块（证书）\n\n位置: `app/Cert/`\n\n证书管理模块，支持证书的生成、管理和验证。\n\n**控制器:**\n\n- `App/`: 用户端证书控制器\n- `Master/`: 管理端证书控制器\n\n**服务层:**\n\n- `Service/Model/`: 证书相关模型\n\n### Crypto 模块（加密）\n\n位置: `app/Crypto/`\n\n提供完整的 AES-256-CBC 加密解密功能，支持前端 WASM 和后端 PHP 的数据安全交换。\n\n**核心组件:**\n\n- `lib/Utils/Crypto/AESCipher.php`: AES-256-CBC 加密解密类\n- `lib/Utils/Crypto/SessionManager.php`: ECDH 密钥交换和会话管理\n- `app/Crypto/Service/CryptoService.php`: 统一的加密服务接口\n\n**API 控制器:**\n\n- `Api/Encrypt.php`: 加密解密 API\n  - `POST /crypto/api/encrypt/encrypt` - 加密数据\n  - `POST /crypto/api/encrypt/decrypt` - 解密数据\n- `Api/Key.php`: 密钥管理 API\n  - `GET /crypto/api/key/get` - 获取当前密钥\n  - `POST /crypto/api/key/update` - 更新密钥\n- `Api/Session.php`: 会话管理 API\n  - `POST /crypto/api/session/init` - 初始化 ECDH 密钥交换\n  - `GET /crypto/api/session/keys` - 获取会话密钥\n  - `POST /crypto/api/session/delete` - 删除会话\n  - `POST /crypto/api/session/clean` - 清理过期会话\n\n**详细文档:** 参见 `app/Crypto/README.md`\n\n### Plan 模块（计划）\n\n位置: `app/Plan/`\n\n学习计划管理模块，支持用户学习计划的创建、跟踪和管理。\n\n**控制器:**\n\n- `App/`: 用户端计划控制器\n- `Master/`: 管理端计划控制器\n\n**服务层:**\n\n- `PlanService.php`: 计划服务\n- `Service/Model/`: 计划相关模型\n\n**前端 API:**\n\n```javascript\n// 获取用户计划统计\nawait api.planApi.getUserPlanStats();\n\n// 获取用户最近计划\nawait api.planApi.getUserRecentPlan();\n\n// 获取我的计划列表\nawait api.planApi.getMyPlans(search);\n```\n\n## 构建和运行\n\n### 环境要求\n\n**后端环境：**\n\n- PHP >= 8.0\n- JSON、PDO、Redis、Mbstring、Fileinfo、GD、OpenSSL、Zip 扩展\n- MySQL 5.7+ 或 MariaDB 10.2+\n- Redis 5.0+（可选）\n\n**前端环境：**\n\n- Node.js >= 16.0\n- npm >= 8.0\n\n### 安装步骤\n\n1. **克隆或下载项目**\n\n   ```bash\n   git clone <repository-url>\n   cd magnolia\n   ```\n2. **安装后端依赖**\n\n   ```bash\n   composer install\n   ```\n3. **安装前端依赖**\n\n   ```bash\n   npm install\n   ```\n4. **配置数据库**\n\n   - 修改 `lib/Config/DataBase/MySql.php` 配置文件\n   - 导入 `magnolia.sql` 数据库脚本\n5. **配置站点**\n\n   - 修改 `lib/Config/Site/` 下的配置文件\n6. **配置前端**\n\n   - 修改 `frontend/config/index.js` 中的 API 地址\n7. **确保 Web 服务器配置**\n\n   - Web 服务器根目录指向项目根目录\n   - 配置 URL 重写规则（.htaccess）\n\n### 开发模式\n\n**启动前端开发服务器：**\n\n```bash\nnpm run dev\n```\n\n开发服务器默认运行在 `http://localhost:5173`，并代理 API 请求到后端。\n\n**后端开发：**\n\n- 配置 Web 服务器（Apache/Nginx）指向项目根目录\n- 确保 PHP 扩展已启用\n- 确保数据库和 Redis 服务正在运行\n\n### 生产构建\n\n**构建前端：**\n\n```bash\nnpm run build\n```\n\n构建输出到 `public/assets/` 目录。\n\n**预览构建结果：**\n\n```bash\nnpm run preview\n```\n\n### PHP 脚本\n\n项目提供了以下 Composer 脚本：\n\n```bash\n# 运行测试\ncomposer test\n\n# 运行测试并生成覆盖率报告\ncomposer test:coverage\n\n# 运行单元测试\ncomposer test:unit\n\n# 运行集成测试\ncomposer test:integration\n\n# 运行静态分析\ncomposer analyze\n```\n\n### CLI 脚本工具\n\n项目提供了以下 CLI 脚本用于代码生成：\n\n```bash\n# 生成控制器\nphp cli/generate_controller.php --module=模块名 --type=类型(Master/App) --controller=控制器名\n\n# 生成模型\nphp cli/generate_model.php --module=模块名 --table=表名\n\n# 生成试题\nphp cli/generate_questions.php\n```\n\n### 开发工作流\n\n1. **启动开发环境**\n\n   ```bash\n   # 终端 1: 启动前端开发服务器\n   npm run dev\n\n   # 终端 2: 启动后端服务器（如果需要）\n   php -S localhost:8000\n   ```\n2. **开发新功能**\n\n   - 前端: 在 `frontend/` 目录下开发 Vue 组件和页面\n   - 后端: 在 `app/` 目录下开发控制器和服务\n3. **调试**\n\n   - 前端: 使用浏览器 DevTools\n   - 后端: 查看 `storage/logs/error.log`\n4. **提交代码**\n\n   ```bash\n   git add .\n   git commit -m \"描述变更\"\n   git push\n   ```\n\n## 插件系统\n\n项目支持插件扩展：\n\n- 插件放置在 `plugins/` 目录\n- 插件路由以 `/plugins/` 开头\n- 插件命名空间: `PHPEMS\\Plugins\\{PluginName}`\n- 插件遵循与核心模块相同的目录结构\n\n### 示例插件\n\n`plugins/Demo/` 提供了一个示例插件，可供参考。\n\n## 开发约定\n\n### 命名规范\n\n**PHP 端：**\n\n- **类名**: PascalCase (首字母大写驼峰式)\n- **方法名**: camelCase (首字母小写驼峰式)\n- **模块名**: 首字母大写\n- **控制器名**: 首字母大写\n- **动作名**: 首字母小写\n- **常量**: 全大写，下划线分隔\n\n**前端 JavaScript/Vue：**\n\n- **组件名**: PascalCase\n- **文件名**: PascalCase (组件), camelCase (工具/模块)\n- **变量/函数**: camelCase\n- **常量**: UPPER_SNAKE_CASE\n\n### 目录结构约定\n\n**PHP 端：**\n\n- 每个模块在 `app/` 下有独立目录\n- 控制器必须放在 `Controller/` 子目录\n- 服务类放在 `Service/` 子目录\n- 模型类放在 `Service/Model/` 子目录\n- 模板文件放在 `Tpls/` 子目录\n- 插件必须遵循相同的目录结构\n\n**前端：**\n\n- 页面组件放在 `views/` 目录\n- 可复用组件放在 `components/` 目录\n- API 调用放在 `framework/api/` 目录\n- 工具函数放在 `framework/utils/` 目录\n- 状态管理放在 `stores/` 目录\n- 路由配置放在 `router/` 目录\n\n### HTTP 请求规范\n\n**后端 API：**\n\n- **敏感操作**: 必须使用 POST 请求\n- **数据修改**: 使用 POST、PUT 或 DELETE\n- **数据查询**: 可以使用 GET（但不涉及敏感数据）\n\n**前端 API 调用：**\n\n- 统一使用 `frontend/framework/api/` 中的 API 模块\n- 使用代理模式按需加载\n- 统一错误处理\n\n### 错误处理\n\n**后端：**\n\n- 404 错误: 当控制器类不存在时返回\"页面未找到\"\n- 依赖解析错误: 当无法解析构造函数参数时抛出异常\n- 使用 `PHPEMS\\Lib\\Rules\\Error` 统一错误处理\n- 使用 `PHPEMS\\Lib\\Rules\\Message` 统一消息处理\n\n**前端：**\n\n- 统一 HTTP 错误处理（`frontend/framework/http/index.js`）\n- 业务错误: 显示错误消息\n- 网络错误: 显示网络异常提示\n- 认证错误: 自动跳转到登录页\n\n## 扩展点\n\n### 可扩展组件\n\n**后端：**\n\n1. **Flow 组件**: 可扩展 Init、Auth、Json、Tpl 等流程处理\n2. **Auth 驱动**: 在 `lib/Auth/Drivers/` 下添加新的认证驱动\n3. **Session 驱动**: 在 `lib/Session/Drivers/` 下添加新的会话驱动\n4. **Cache 组件**: 在 `lib/Cache/` 下实现具体的缓存驱动\n5. **Utils 工具类**: 在 `lib/Utils/` 下添加通用工具类\n6. **插件系统**: 通过 plugins 目录添加新功能\n7. **AI 客户端**: 扩展 `lib/AI/AIClient.php` 支持更多 AI 服务\n8. **Crypto 模块**: 扩展 `lib/Utils/Crypto/` 添加新的加密算法\n\n**前端：**\n\n1. **API 模块**: 在 `framework/api/` 下添加新的 API 模块\n2. **组件**: 在 `components/` 下添加可复用组件\n3. **工具函数**: 在 `framework/utils/` 下添加工具函数\n4. **UI 框架**: 在 `framework/ui/` 下配置 UI 框架加载\n5. **状态管理**: 在 `stores/` 下添加新的 store\n\n### 模型扩展\n\n- 使用 `generate_model.php` 脚本生成数据表对应的模型类\n- 模型继承 `PHPEMS\\Lib\\Rules\\Model`\n- 在 `app/{Module}/Service/Model/` 下组织模型类\n\n### 控制器扩展\n\n- 使用 `generate_controller.php` 脚本生成控制器类\n- 控制器必须继承 `PHPEMS\\Lib\\Rules\\Controller`\n- 在 `app/{Module}/Controller/{Type}/` 下组织控制器\n\n## 性能优化\n\n**后端：**\n\n- **依赖注入**: 使用单例模式减少重复实例化\n- **自动加载**: 利用 Composer 的 PSR-4 自动加载优化类加载\n- **数据库**: 使用查询构建器优化 SQL，使用 Redis 缓存热点数据\n- **会话**: 使用 Redis 存储会话提高性能\n\n**前端：**\n\n- **按需加载**: API 模块和 UI 框架按需加载\n- **代码分割**: Vite 自动进行代码分割\n- **静态资源**: 使用 Vite 优化静态资源\n- **缓存**: 使用 localStorage 持久化数据\n- **路由懒加载**: 页面组件懒加载\n- **WASM 加速**: 使用 WebAssembly 提升加密性能\n\n## 安全注意事项\n\n**后端：**\n\n- **输入验证**: 使用 `PHPEMS\\Lib\\Utils\\Validator` 验证所有用户输入\n- **XSS 防护**: 模板引擎自动转义输出内容\n- **CSRF 防护**: 在表单提交中使用 CSRF 令牌\n- **SQL 注入**: 使用预处理语句或查询构建器\n- **文件上传**: 限制上传文件类型和大小\n- **认证授权**: 使用完整的认证和会话系统\n- **敏感数据**: 不要在日志中记录敏感信息\n\n**前端：**\n\n- **数据加密**: 敏感数据使用 AES 加密存储\n- **XSS 防护**: Vue 自动转义 HTML\n- **CSRF 防护**: 使用 Cookie 的 SameSite 属性\n- **API 安全**: 敏感操作使用 POST 请求\n- **环境隔离**: 开发环境和生产环境分离\n- **WASM 安全**: 使用 WASM 模块进行黑盒加密保护\n\n## 维护指南\n\n### 添加新功能\n\n**后端：**\n\n1. 确定功能所属模块\n2. 使用 CLI 脚本生成控制器和模型\n3. 实现业务逻辑\n4. 更新路由配置（如果需要自定义路由）\n5. 更新文档\n\n**前端：**\n\n1. 确定功能所属模块（PC/移动）\n2. 创建 API 调用模块（如果需要）\n3. 创建页面组件和视图\n4. 更新路由配置\n5. 更新状态管理（如果需要）\n6. 更新文档\n\n### 调试技巧\n\n**后端：**\n\n- 检查 PHP 错误日志: `storage/logs/error.log`\n- 使用 `PHPEMS\\Lib\\Utils\\FileLogger` 记录调试信息\n- 验证路由是否正确解析到控制器\n- 检查依赖注入是否正确配置\n\n**前端：**\n\n- 使用浏览器 DevTools 调试\n- 检查网络请求和响应\n- 查看 Vue DevTools\n- 检查 localStorage 中的数据\n- 使用 console.log 调试\n\n### 常见问题\n\n**后端：**\n\n1. **404 错误**: 检查控制器类路径和命名是否正确\n2. **依赖解析失败**: 检查构造函数参数是否有默认值或可为空\n3. **自动加载失败**: 检查命名空间和目录结构是否匹配\n4. **插件不工作**: 检查插件目录结构和命名空间是否正确\n5. **数据库连接失败**: 检查数据库配置文件和网络连接\n6. **Redis 连接失败**: 检查 Redis 服务是否启动和配置是否正确\n\n**前端：**\n\n1. **API 调用失败**: 检查 API 地址配置和网络连接\n2. **路由错误**: 检查路由配置和组件路径\n3. **组件不加载**: 检查组件导入路径和命名\n4. **状态丢失**: 检查 localStorage 和加密配置\n5. **UI 样式错误**: 检查 UI 框架加载和样式文件\n6. **WASM 加载失败**: 确保通过 HTTP 服务器访问（不能使用 file:// 协议）\n\n### 代码质量\n\n**后端：**\n\n- 保持代码风格一致\n- 编写清晰的注释和文档\n- 遵循 SOLID 原则\n- 使用 PSR 规范\n- 运行静态分析: `composer analyze`\n- 运行测试: `composer test`\n\n**前端：**\n\n- 使用 ESLint 检查代码规范（如果配置）\n- 使用 TypeScript 类型检查（如果配置）\n- 组件化开发\n- 遵循 Vue 3 最佳实践\n\n## 升级指南\n\n### 从早期版本升级\n\n1. 备份现有数据和代码\n2. 更新 `composer.json` 依赖\n3. 运行 `composer update`\n4. 更新 `package.json` 依赖\n5. 运行 `npm install`\n6. 检查配置文件变更\n7. 运行数据库迁移（如果有）\n8. 测试所有功能\n9. 重新构建前端: `npm run build`\n\n### 版本兼容性\n\n- PHP >= 8.0 是最低要求\n- 数据库版本要求: MySQL 5.7+ 或 MariaDB 10.2+\n- Redis 5.0+ 用于会话和缓存（推荐）\n- Node.js >= 16.0 用于前端开发\n- OpenSSL 扩展用于加密功能（必需）\n\n## 快速开始\n\n### 开发环境快速启动\n\n1. **安装依赖**\n\n   ```bash\n   composer install\n   npm install\n   ```\n2. **配置**\n\n   - 复制并编辑配置文件\n   - 配置数据库连接\n   - 配置前端 API 地址\n   - 导入 `magnolia.sql` 数据库脚本\n3. **启动开发服务器**\n\n   ```bash\n   # 终端 1\n   npm run dev\n\n   # 终端 2（如果需要）\n   php -S localhost:8000\n   ```\n4. **访问应用**\n\n   - 前端开发服务器: http://localhost:5173\n   - 后端 API: http://localhost:8000 或配置的域名\n\n### 生产环境部署\n\n1. **构建前端**\n\n   ```bash\n   npm run build\n   ```\n2. **配置 Web 服务器**\n\n   - 指向项目根目录\n   - 配置 URL 重写规则\n   - 配置 HTTPS（推荐）\n3. **设置文件权限**\n\n   - storage/ 目录可写\n   - logs/ 目录可写\n4. **配置缓存**\n\n   - 启用 Redis\n   - 配置 Redis 连接\n5. **监控日志**\n\n   - 定期检查 `storage/logs/error.log`\n\n## 联系与支持\n\n如有问题或建议，请通过以下方式联系：\n\n- 提交 Issue\n- 发送邮件\n- 查看文档\n\n---\n\n**最后更新**: 2026-07-18\n', '', 0);
INSERT INTO `x2_course` VALUES (4, '视频测试', 'video', 1, 0, 'storage/attach/public/20260724/f2abe7e26013c9c618c5a4817816b924.png', 1, 1784853947, 1784853947, 0, '<p>视频测试</p>', 'http://114.139.215.21:12371/@/kodbox/?explorer/share/file&hash=b91f74IZO2tycbctZpjp7GRO5uy28X8nmQ8A44fOqE4b4m3EVCT5fKU8pMuCwrFz5Xda&name=/12333.mp4', 0);

-- ----------------------------
-- Table structure for x2_course_category
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_category`;
CREATE TABLE `x2_course_category`  (
  `catid` int NOT NULL AUTO_INCREMENT,
  `catname` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `catthumb` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `catparent` int NULL DEFAULT 0,
  `catlite` int NOT NULL DEFAULT 0,
  `caturl` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `catuseurl` int NOT NULL DEFAULT 0,
  `catdes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`catid`) USING BTREE,
  INDEX `catlite`(`catlite` ASC, `catparent` ASC) USING BTREE,
  INDEX `catuseurl`(`catuseurl` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course_category
-- ----------------------------
INSERT INTO `x2_course_category` VALUES (1, 'phpems', 'storage/attach/public/20260719/83d751018930302939ccb39e9c2f57fe.png', 0, 1, '', 0, '');
INSERT INTO `x2_course_category` VALUES (2, '人工智能', 'storage/attach/public/20260723/4ddceac741ab881783b20f0d543dea99.png', 3, 0, '', 0, '人工智能');
INSERT INTO `x2_course_category` VALUES (3, '父级分类', 'storage/attach/public/20260723/e3599910120ce2d59dbf3866f86ccf8e.png', 0, 1, '', 0, '');
INSERT INTO `x2_course_category` VALUES (4, '缩略图测试', '', 0, 2, '', 0, '');

-- ----------------------------
-- Table structure for x2_course_log
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_log`;
CREATE TABLE `x2_course_log`  (
  `logid` int NOT NULL AUTO_INCREMENT,
  `logpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT '0',
  `logplanid` int NOT NULL,
  `logcourseid` int NULL DEFAULT 0,
  `logtime` int NULL DEFAULT 0,
  `logstatus` int NULL DEFAULT NULL,
  `logendtime` int NULL DEFAULT NULL,
  `logprogress` int NULL DEFAULT NULL,
  `logfaces` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  PRIMARY KEY (`logid`) USING BTREE,
  INDEX `loguserid`(`logpassport` ASC, `logcourseid` ASC) USING BTREE,
  INDEX `logtime`(`logtime` ASC) USING BTREE,
  INDEX `logplanid`(`logplanid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course_log
-- ----------------------------
INSERT INTO `x2_course_log` VALUES (1, '0025896gd42safx0001', 0, 2, 1784601813, 1, 0, 0, NULL);
INSERT INTO `x2_course_log` VALUES (2, '0025896gd42safx0001', 0, 3, 1784601815, 1, 0, 0, NULL);
INSERT INTO `x2_course_log` VALUES (3, '0025896gd42safx0001', 0, 4, 1784853956, 0, 0, 720, NULL);

-- ----------------------------
-- Table structure for x2_course_member
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_member`;
CREATE TABLE `x2_course_member`  (
  `cmid` int NOT NULL AUTO_INCREMENT,
  `cmpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `cmcsid` int NULL DEFAULT NULL,
  `cmstarttime` int NULL DEFAULT NULL,
  `cmendtime` int NULL DEFAULT NULL,
  PRIMARY KEY (`cmid`) USING BTREE,
  INDEX `cmpassport`(`cmpassport` ASC) USING BTREE,
  INDEX `cmcsid`(`cmcsid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course_member
-- ----------------------------
INSERT INTO `x2_course_member` VALUES (1, '0025896gd42safx0001', 1, 1784601804, 1793696505);

-- ----------------------------
-- Table structure for x2_course_price
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_price`;
CREATE TABLE `x2_course_price`  (
  `cpid` int NOT NULL AUTO_INCREMENT,
  `cpcsid` int NULL DEFAULT NULL,
  `cpdays` int NULL DEFAULT NULL,
  `cpamount` int NULL DEFAULT NULL,
  PRIMARY KEY (`cpid`) USING BTREE,
  INDEX `cpcsid`(`cpcsid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course_price
-- ----------------------------
INSERT INTO `x2_course_price` VALUES (1, 1, 30, 0);

-- ----------------------------
-- Table structure for x2_course_progress
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_progress`;
CREATE TABLE `x2_course_progress`  (
  `cpid` int NOT NULL AUTO_INCREMENT,
  `cppassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `cpcsid` int NULL DEFAULT NULL,
  `cpstatus` int NULL DEFAULT NULL,
  PRIMARY KEY (`cpid`) USING BTREE,
  INDEX `cppassport`(`cppassport` ASC) USING BTREE,
  INDEX `cpcsid`(`cpcsid` ASC) USING BTREE,
  INDEX `cpstatus`(`cpstatus` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course_progress
-- ----------------------------

-- ----------------------------
-- Table structure for x2_course_session
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_session`;
CREATE TABLE `x2_course_session`  (
  `csnid` int NOT NULL AUTO_INCREMENT,
  `csnpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `csncsid` int NULL DEFAULT NULL,
  `csnfadtime` int NULL DEFAULT NULL,
  PRIMARY KEY (`csnid`) USING BTREE,
  UNIQUE INDEX `csnpassport`(`csnpassport` ASC) USING BTREE,
  INDEX `csncsid`(`csncsid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course_session
-- ----------------------------
INSERT INTO `x2_course_session` VALUES (1, '0025896gd42safx0001', 1, 0);

-- ----------------------------
-- Table structure for x2_course_subject
-- ----------------------------
DROP TABLE IF EXISTS `x2_course_subject`;
CREATE TABLE `x2_course_subject`  (
  `csid` int NOT NULL AUTO_INCREMENT,
  `cstitle` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT '',
  `cscatid` int NULL DEFAULT NULL,
  `csuserid` int NULL DEFAULT 0,
  `cstime` int NULL DEFAULT 0,
  `csthumb` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT '',
  `cssequence` int NULL DEFAULT NULL,
  `csnumber` tinyint(1) NULL DEFAULT NULL,
  `csprogress` tinyint(1) NULL DEFAULT NULL,
  `csfacetime` int NULL DEFAULT NULL,
  `csdescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `cstext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  PRIMARY KEY (`csid`) USING BTREE,
  INDEX `csbasicid`(`cstime` ASC) USING BTREE,
  INDEX `cscatid`(`cscatid` ASC) USING BTREE,
  INDEX `cuserid`(`csuserid` ASC) USING BTREE,
  INDEX `cssequence`(`cssequence` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_course_subject
-- ----------------------------
INSERT INTO `x2_course_subject` VALUES (1, 'PHPEMS LITE', 1, 1, 1784461088, 'storage/attach/public/20260719/eee63a96d3e50acbb5685a443411456c.png', 0, 1, 1, 0, 'PHPEMS LITE', '<p>PHPEMS LITE</p>');

-- ----------------------------
-- Table structure for x2_exam_basic
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_basic`;
CREATE TABLE `x2_exam_basic`  (
  `basicid` int NOT NULL AUTO_INCREMENT,
  `basic` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `basicsubjectid` int NOT NULL DEFAULT 0,
  `basicnumber` int NULL DEFAULT NULL,
  `basicfacetime` int NULL DEFAULT NULL,
  `basicpoint` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `basicexam` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `basicthumb` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `basicdescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `basictext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  PRIMARY KEY (`basicid`) USING BTREE,
  INDEX `basicsubjectid`(`basicsubjectid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_basic
-- ----------------------------
INSERT INTO `x2_exam_basic` VALUES (1, '植物学', 1, 1, 0, '{\"1\":[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\"],\"2\":[\"8\",\"9\",\"10\",\"11\",\"12\",\"13\",\"14\"],\"3\":[\"15\",\"16\",\"17\",\"18\",\"19\",\"20\",\"21\"],\"4\":[\"22\",\"23\",\"24\",\"25\",\"26\",\"27\"],\"5\":[\"28\",\"29\",\"30\",\"31\",\"32\",\"33\"],\"6\":[\"34\",\"35\",\"36\",\"37\",\"38\",\"39\"]}', '{\"rulemodel\":0,\"model\":0,\"changesequence\":1,\"auto\":[\"1\"],\"autotemplate\":\"1\",\"self\":[\"1\"],\"selftemplate\":\"0\",\"selectrule\":0,\"fullsubmit\":0,\"unjointime\":\"0\",\"examnumber\":\"0\",\"screennumber\":\"0\",\"notviewscore\":0}', 'storage/attach/public/20260720/6f1fa7eca89ecdc9e24b01186f1a11bb.png', '植物学', '');

-- ----------------------------
-- Table structure for x2_exam_exercise
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_exercise`;
CREATE TABLE `x2_exam_exercise`  (
  `exerid` int NOT NULL AUTO_INCREMENT,
  `exeruserid` int NOT NULL,
  `exerplanid` int NULL DEFAULT NULL,
  `exerbasicid` int NOT NULL,
  `exerpointid` int NOT NULL,
  `exernumber` int NOT NULL,
  `exerqutype` int NOT NULL,
  PRIMARY KEY (`exerid`) USING BTREE,
  INDEX `exeruserid`(`exeruserid` ASC) USING BTREE,
  INDEX `exerbasicid`(`exerbasicid` ASC) USING BTREE,
  INDEX `exerknowsid`(`exerpointid` ASC) USING BTREE,
  INDEX `exerplanid`(`exerplanid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_exercise
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_favor
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_favor`;
CREATE TABLE `x2_exam_favor`  (
  `favorid` int NOT NULL AUTO_INCREMENT,
  `favorpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '0',
  `favorsubjectid` int NOT NULL DEFAULT 0,
  `favorquestionid` int NOT NULL DEFAULT 0,
  `favortime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`favorid`) USING BTREE,
  INDEX `favoruserid`(`favorpassport` ASC, `favorquestionid` ASC, `favortime` ASC) USING BTREE,
  INDEX `favorsubjectid`(`favorsubjectid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_favor
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_feedback
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_feedback`;
CREATE TABLE `x2_exam_feedback`  (
  `fbid` int NOT NULL AUTO_INCREMENT,
  `fbquestionid` int NOT NULL,
  `fbtype` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `fbcontent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `fbuserid` int NOT NULL,
  `fbtime` int NOT NULL,
  `fbstatus` tinyint NOT NULL,
  `fbdoneuserid` int NOT NULL,
  `fbdonetime` int NOT NULL,
  PRIMARY KEY (`fbid`) USING BTREE,
  INDEX `fbquestionid`(`fbquestionid` ASC, `fbuserid` ASC) USING BTREE,
  INDEX `fbtype`(`fbtype` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_feedback
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_history
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_history`;
CREATE TABLE `x2_exam_history`  (
  `ehid` int NOT NULL AUTO_INCREMENT,
  `ehplanid` int NOT NULL,
  `ehpaperid` int NOT NULL DEFAULT 0,
  `ehexam` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `ehtype` int NOT NULL DEFAULT 0,
  `ehbasicid` int NOT NULL DEFAULT 0,
  `ehtime` int NOT NULL DEFAULT 0,
  `ehscore` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `ehpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `ehstarttime` int NOT NULL DEFAULT 0,
  `ehendtime` int NOT NULL,
  `ehstatus` int NOT NULL DEFAULT 1,
  `ehdecide` int NOT NULL DEFAULT 0,
  `ehneedresit` tinyint NOT NULL,
  `ehispass` tinyint NULL DEFAULT NULL,
  `ehteacher` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ehdecidetime` int NULL DEFAULT NULL,
  `ehstats` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `ehstartip` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ehendip` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ehstartclient` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ehendclient` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ehid`) USING BTREE,
  INDEX `ehtype`(`ehtype` ASC, `ehbasicid` ASC, `ehtime` ASC) USING BTREE,
  INDEX `ehdecide`(`ehdecide` ASC) USING BTREE,
  INDEX `ehexamid`(`ehpaperid` ASC) USING BTREE,
  INDEX `ehneedresit`(`ehneedresit` ASC) USING BTREE,
  INDEX `ehispass`(`ehispass` ASC) USING BTREE,
  INDEX `ehplanid`(`ehplanid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_history
-- ----------------------------
INSERT INTO `x2_exam_history` VALUES (10, 0, 1, '植物学', 1, 1, 5, 0.00, '0025896gd42safx0001', 1791010785, 1791010790, 1, 0, 0, 0, '', 0, '{\"setting\":{\"totalScore\":100,\"totalTime\":60,\"passMark\":\"60.00\"},\"pointAnalysis\":{\"totalPointScore\":{\"1\":4,\"9\":2,\"11\":4,\"12\":6,\"15\":2,\"27\":2,\"33\":2,\"37\":2,\"7\":2,\"22\":4,\"19\":4,\"20\":4,\"25\":4,\"31\":2,\"8\":2,\"21\":10,\"26\":2,\"14\":6,\"34\":2,\"5\":2,\"2\":2,\"13\":2,\"23\":4,\"29\":2,\"4\":2,\"38\":2,\"39\":2,\"35\":2,\"36\":2,\"6\":4,\"10\":4},\"rightPointScore\":{\"1\":0,\"9\":0,\"11\":0,\"12\":0,\"15\":0,\"27\":0,\"33\":0,\"37\":0,\"7\":0,\"22\":0,\"19\":0,\"20\":0,\"25\":0,\"31\":0,\"8\":0,\"21\":0,\"26\":0,\"14\":0,\"34\":0,\"5\":0,\"2\":0,\"13\":0,\"23\":0,\"29\":0,\"4\":0,\"38\":0,\"39\":0,\"35\":0,\"36\":0,\"6\":0,\"10\":0},\"totalPointNumber\":{\"1\":2,\"9\":1,\"11\":2,\"12\":3,\"15\":1,\"27\":1,\"33\":1,\"37\":1,\"7\":1,\"22\":2,\"19\":2,\"20\":2,\"25\":2,\"31\":1,\"8\":1,\"21\":5,\"26\":1,\"14\":3,\"34\":1,\"5\":1,\"2\":1,\"13\":1,\"23\":2,\"29\":1,\"4\":1,\"38\":1,\"39\":1,\"35\":1,\"36\":1,\"6\":2,\"10\":2},\"rightPointNumber\":{\"1\":0,\"9\":0,\"11\":0,\"12\":0,\"15\":0,\"27\":0,\"33\":0,\"37\":0,\"7\":0,\"22\":0,\"19\":0,\"20\":0,\"25\":0,\"31\":0,\"8\":0,\"21\":0,\"26\":0,\"14\":0,\"34\":0,\"5\":0,\"2\":0,\"13\":0,\"23\":0,\"29\":0,\"4\":0,\"38\":0,\"39\":0,\"35\":0,\"36\":0,\"6\":0,\"10\":0}},\"questionTypeAnalysis\":{\"totalTypeScore\":{\"1\":80,\"2\":20},\"rightTypeScore\":{\"1\":0,\"2\":0},\"totalTypeNumber\":{\"1\":40,\"2\":10},\"rightTypeNumber\":{\"1\":0,\"2\":0}}}', '127.0.0.1', '127.0.0.1', '电脑:Edge', '电脑:Edge');

-- ----------------------------
-- Table structure for x2_exam_history_detail
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_history_detail`;
CREATE TABLE `x2_exam_history_detail`  (
  `ehdid` int NOT NULL AUTO_INCREMENT,
  `ehdehid` int NULL DEFAULT NULL,
  `ehdscores` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `ehdsetting` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `ehdquestion` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `ehdanswer` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `ehdfacelist` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `ehdtimes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`ehdid`) USING BTREE,
  UNIQUE INDEX `ehdehid`(`ehdehid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_history_detail
-- ----------------------------
INSERT INTO `x2_exam_history_detail` VALUES (10, 10, '{\"3\":0,\"26\":0,\"33\":0,\"35\":0,\"45\":0,\"70\":0,\"83\":0,\"92\":0,\"105\":0,\"109\":0,\"132\":0,\"153\":0,\"190\":0,\"202\":0,\"204\":0,\"216\":0,\"224\":0,\"244\":0,\"257\":0,\"262\":0,\"41\":0,\"42\":0,\"85\":0,\"103\":0,\"117\":0,\"122\":0,\"131\":0,\"144\":0,\"152\":0,\"154\":0,\"160\":0,\"174\":0,\"194\":0,\"210\":0,\"212\":0,\"233\":0,\"236\":0,\"254\":0,\"269\":0,\"270\":0}', '{\"totalScore\":100,\"totalTime\":60,\"passMark\":\"60.00\",\"questionTypeSort\":[1,2,3,4],\"scaleModel\":0,\"type\":1,\"questionTypes\":{\"1\":{\"number\":\"40\",\"score\":\"2\",\"describe\":\"\",\"easyNumber\":\"20\",\"middleNumber\":\"20\",\"hardNumber\":0},\"2\":{\"number\":\"10\",\"score\":\"2\",\"describe\":\"\",\"easyNumber\":\"5\",\"middleNumber\":\"5\",\"hardNumber\":0},\"3\":{\"number\":0,\"score\":0,\"describe\":\"\",\"easyNumber\":0,\"middleNumber\":0,\"hardNumber\":0},\"4\":{\"number\":0,\"score\":0,\"describe\":\"\",\"easyNumber\":0,\"middleNumber\":0,\"hardNumber\":0}},\"questionScore\":{\"3\":\"2\",\"26\":\"2\",\"33\":\"2\",\"35\":\"2\",\"45\":\"2\",\"70\":\"2\",\"83\":\"2\",\"92\":\"2\",\"105\":\"2\",\"109\":\"2\",\"132\":\"2\",\"153\":\"2\",\"190\":\"2\",\"202\":\"2\",\"204\":\"2\",\"216\":\"2\",\"224\":\"2\",\"244\":\"2\",\"257\":\"2\",\"262\":\"2\",\"41\":\"2\",\"42\":\"2\",\"85\":\"2\",\"103\":\"2\",\"117\":\"2\",\"122\":\"2\",\"131\":\"2\",\"144\":\"2\",\"152\":\"2\",\"154\":\"2\",\"160\":\"2\",\"174\":\"2\",\"194\":\"2\",\"210\":\"2\",\"212\":\"2\",\"233\":\"2\",\"236\":\"2\",\"254\":\"2\",\"269\":\"2\",\"270\":\"2\"}}', '{\"1\":{\"questions\":[{\"questionid\":3,\"questiontype\":1,\"question\":\"下列哪种质体在植物细胞中负责合成和积累淀粉？\",\"questionhtml\":[\"A. 叶绿体\",\"B. 有色体\",\"C. 白色体（淀粉体）\",\"D. 前质体\"],\"questionisparent\":0,\"questionanswer\":\"C\",\"questionselecttype\":0,\"points\":[1]},{\"questionid\":26,\"questiontype\":1,\"question\":\"植物吸收矿质离子的主要方式为主动运输，该过程需要能量和？\",\"questionhtml\":[\"A. 载体蛋白\",\"B. 通道蛋白\",\"C. 胞饮作用\",\"D. 离子交换\"],\"questionisparent\":0,\"questionanswer\":\"A\",\"questionselecttype\":0,\"points\":[9]},{\"questionid\":33,\"questiontype\":1,\"question\":\"无氧呼吸中，植物细胞将丙酮酸转化为乙醇和CO₂，该过程属于？\",\"questionhtml\":[\"A. 糖酵解\",\"B. 发酵\",\"C. 氧化磷酸化\",\"D. 光呼吸\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[11]},{\"questionid\":35,\"questiontype\":1,\"question\":\"促进果实成熟、促进器官脱落和衰老的植物激素是？\",\"questionhtml\":[\"A. 乙烯\",\"B. 生长素\",\"C. 赤霉素\",\"D. 细胞分裂素\"],\"questionisparent\":0,\"questionanswer\":\"A\",\"questionselecttype\":0,\"points\":[12]},{\"questionid\":45,\"questiontype\":1,\"question\":\"在植物分类等级序列（界、门、纲、目、科、属、种）中，科与属之间的等级是？\",\"questionhtml\":[\"A. 亚科\",\"B. 族\",\"C. 目\",\"D. 亚属\"],\"questionisparent\":0,\"questionanswer\":\"C\",\"questionselecttype\":0,\"points\":[15]},{\"questionid\":70,\"questiontype\":1,\"question\":\"热带雨林的主要特征包括物种丰富度高、层次结构复杂以及？\",\"questionhtml\":[\"A. 有大量藤本和附生植物\",\"B. 植物多具旱生结构\",\"C. 优势种明显\",\"D. 林冠稀疏\"],\"questionisparent\":0,\"questionanswer\":\"A\",\"questionselecttype\":0,\"points\":[27]},{\"questionid\":83,\"questiontype\":1,\"question\":\"物种形成中，地理隔离是最常见的机制。当两个种群被山脉、河流等地理障碍隔开，各自演化后可能形成新物种，这种方式称为？\",\"questionhtml\":[\"A. 异域物种形成\",\"B. 同域物种形成\",\"C. 邻域物种形成\",\"D. 瞬时物种形成\"],\"questionisparent\":0,\"questionanswer\":\"A\",\"questionselecttype\":0,\"points\":[33]},{\"questionid\":92,\"questiontype\":1,\"question\":\"植物转基因技术中，最常用的载体是？\",\"questionhtml\":[\"A. 质粒\",\"B. Ti质粒（农杆菌转化）\",\"C. 病毒载体\",\"D. 脂质体\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[37]},{\"questionid\":105,\"questiontype\":1,\"question\":\"果实由子房和花的其他部分（如花托、花萼）共同发育而成，称为假果。以下属于假果的是？\",\"questionhtml\":[\"A. 桃\",\"B. 葡萄\",\"C. 梨\",\"D. 大豆\"],\"questionisparent\":0,\"questionanswer\":\"C\",\"questionselecttype\":0,\"points\":[7]},{\"questionid\":109,\"questiontype\":1,\"question\":\"呼吸作用中，电子传递链位于线粒体内膜上，最终电子受体是？\",\"questionhtml\":[\"A. NAD⁺\",\"B. 氧气（O₂）\",\"C. 水\",\"D. 二氧化碳\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[11]},{\"questionid\":132,\"questiontype\":1,\"question\":\"植物细胞中，细胞壁分为初生壁和次生壁。次生壁通常在细胞停止生长后形成，其特点包括？\",\"questionhtml\":[\"A. 薄而弹性大\",\"B. 富含纤维素，常有木质素沉积\",\"C. 没有纤维素\",\"D. 仅存在于薄壁组织中\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[1]},{\"questionid\":153,\"questiontype\":1,\"question\":\"种群增长模型中，当种群密度接近环境容纳量（K）时，种群增长率（r）会？\",\"questionhtml\":[\"A. 增大\",\"B. 减小\",\"C. 不变\",\"D. 先减后增\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[22]},{\"questionid\":190,\"questiontype\":1,\"question\":\"植物激素中，细胞分裂素（CTK）主要在根尖合成，通过木质部向上运输。细胞分裂素的生理效应不包括？\",\"questionhtml\":[\"A. 促进细胞分裂\",\"B. 延缓叶片衰老\",\"C. 促进侧芽生长\",\"D. 促进果实成熟\"],\"questionisparent\":0,\"questionanswer\":\"D\",\"questionselecttype\":0,\"points\":[12]},{\"questionid\":202,\"questiontype\":1,\"question\":\"裸子植物中，松科（如马尾松）的花粉具有两个气囊，这有助于风媒传粉。松树的雌球果成熟时，珠鳞张开释放种子。松树的种子具有翅，有利于？\",\"questionhtml\":[\"A. 动物传播\",\"B. 风力传播\",\"C. 水力传播\",\"D. 自体传播\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[19]},{\"questionid\":204,\"questiontype\":1,\"question\":\"APG分类系统中，被子植物的科和属的界定多采用分子数据。APG系统与传统的恩格勒系统（Engler系统）在分类依据上有何不同？\",\"questionhtml\":[\"A. 恩格勒系统基于形态，APG基于分子和形态\",\"B. 两者完全一致\",\"C. APG不承认单子叶植物\",\"D. APG基于地理分布\"],\"questionisparent\":0,\"questionanswer\":\"A\",\"questionselecttype\":0,\"points\":[20]},{\"questionid\":216,\"questiontype\":1,\"question\":\"地衣是藻类和真菌的共生体，真菌提供保护结构和水分，藻类提供光合产物。这种共生关系属于？\",\"questionhtml\":[\"A. 寄生\",\"B. 互利共生\",\"C. 偏利共生\",\"D. 化感作用\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[25]},{\"questionid\":224,\"questiontype\":1,\"question\":\"被子植物中，花的演化趋势之一是从两性花到单性花，从虫媒到风媒。风媒花通常具有哪项特征？\",\"questionhtml\":[\"A. 花色鲜艳\",\"B. 花蜜丰富\",\"C. 花小、无香气\",\"D. 花大而美丽\"],\"questionisparent\":0,\"questionanswer\":\"C\",\"questionselecttype\":0,\"points\":[31]},{\"questionid\":244,\"questiontype\":1,\"question\":\"植物对水分的吸收和运输中，水通道蛋白（aquaporin）在细胞膜上形成选择性通道，促进水分快速跨膜运输。水通道蛋白对水分的运输属于？\",\"questionhtml\":[\"A. 主动运输\",\"B. 协助扩散\",\"C. 自由扩散\",\"D. 胞吞作用\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[8]},{\"questionid\":257,\"questiontype\":1,\"question\":\"豆科的蝶形花亚科的花冠由旗瓣、翼瓣和龙骨瓣组成，龙骨瓣包裹雄蕊和雌蕊。蝶形花的雄蕊常为二体雄蕊（9+1）或单体雄蕊。二体雄蕊的特征是？\",\"questionhtml\":[\"A. 全部雄蕊分离\",\"B. 9枚合生，1枚分离\",\"C. 全部合生成管\",\"D. 雄蕊数目不定\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[21]},{\"questionid\":262,\"questiontype\":1,\"question\":\"泛热带分布类型是指广泛分布于全球热带地区的类群。在中国南部，泛热带分布的科有棕榈科、天南星科等。这些科的植物在温带地区少见，原因是？\",\"questionhtml\":[\"A. 不耐低温\",\"B. 不耐干旱\",\"C. 需弱光\",\"D. 需酸性土壤\"],\"questionisparent\":0,\"questionanswer\":\"A\",\"questionselecttype\":0,\"points\":[26]},{\"questionid\":41,\"questiontype\":1,\"question\":\"植物遭受干旱胁迫时，常通过积累哪种物质来调节渗透势，减少水分损失？\",\"questionhtml\":[\"A. 淀粉\",\"B. 脯氨酸\",\"C. 纤维素\",\"D. 木质素\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[14]},{\"questionid\":42,\"questiontype\":1,\"question\":\"盐碱胁迫对植物的伤害主要包括离子毒害和渗透胁迫。植物通过将Na⁺区隔化在液泡中，依靠的转运蛋白主要是？\",\"questionhtml\":[\"A. H⁺-ATP酶\",\"B. Na⁺\\/H⁺反向转运体\",\"C. 水通道蛋白\",\"D. 钾离子通道\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[14]},{\"questionid\":85,\"questiontype\":1,\"question\":\"植物基因组中，重复序列所占比例较高，其中转座子（转座元件）属于？\",\"questionhtml\":[\"A. 单拷贝序列\",\"B. 中度重复序列\",\"C. 高度重复序列\",\"D. 编码序列\"],\"questionisparent\":0,\"questionanswer\":\"C\",\"questionselecttype\":0,\"points\":[34]},{\"questionid\":103,\"questiontype\":1,\"question\":\"叶片中，气孔的开闭受保卫细胞膨压调节。保卫细胞具有哪种特殊结构，有助于气孔的快速开闭？\",\"questionhtml\":[\"A. 角质层厚\",\"B. 细胞壁不均匀加厚\",\"C. 含大量叶绿体\",\"D. 没有液泡\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[5]},{\"questionid\":117,\"questiontype\":1,\"question\":\"APG分类系统中，被子植物分为基部类群、核心双子叶植物和单子叶植物等主要支系。其中，核心双子叶植物包括蔷薇类、菊类等。蔷薇类中有一个大科，其果实为核果、梨果或聚合果，该科是？\",\"questionhtml\":[\"A. 蔷薇科\",\"B. 豆科\",\"C. 菊科\",\"D. 禾本科\"],\"questionisparent\":0,\"questionanswer\":\"A\",\"questionselecttype\":0,\"points\":[20]},{\"questionid\":122,\"questiontype\":1,\"question\":\"植物与微生物之间的共生关系包括菌根共生。菌根中，丛枝菌根（AM）对植物磷营养吸收有重要促进作用。AM菌根真菌属于？\",\"questionhtml\":[\"A. 子囊菌\",\"B. 担子菌\",\"C. 球囊菌门\",\"D. 半知菌\"],\"questionisparent\":0,\"questionanswer\":\"C\",\"questionselecttype\":0,\"points\":[25]},{\"questionid\":131,\"questiontype\":1,\"question\":\"植物组织培养中，通过调整激素比例可以诱导器官发生或体细胞胚胎发生。高浓度细胞分裂素与低浓度生长素通常有利于诱导？\",\"questionhtml\":[\"A. 根形成\",\"B. 芽形成\",\"C. 愈伤组织生长\",\"D. 体细胞胚成熟\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[2]},{\"questionid\":144,\"questiontype\":1,\"question\":\"光周期现象中，有些植物的开花由夜间长度（暗期）决定，而非日长。对于短日照植物，夜长中断（用红光处理）会？\",\"questionhtml\":[\"A. 促进开花\",\"B. 抑制开花\",\"C. 无影响\",\"D. 改变花型\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[13]},{\"questionid\":152,\"questiontype\":1,\"question\":\"蔷薇科分为四个亚科：绣线菊亚科、蔷薇亚科、李亚科（桃亚科）和苹果亚科。其中，子房上位、花托凹陷形成杯状花筒、果实为核果的亚科是？\",\"questionhtml\":[\"A. 绣线菊亚科\",\"B. 蔷薇亚科\",\"C. 李亚科\",\"D. 苹果亚科\"],\"questionisparent\":0,\"questionanswer\":\"C\",\"questionselecttype\":0,\"points\":[21]},{\"questionid\":154,\"questiontype\":1,\"question\":\"群落生态学中，生态位（niche）是指物种在群落中的地位和资源利用方式。两个物种的生态位重叠可能导致？\",\"questionhtml\":[\"A. 协同进化\",\"B. 竞争排斥\",\"C. 互利共生\",\"D. 寄生\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[23]},{\"questionid\":160,\"questiontype\":1,\"question\":\"从水生到陆生的演化过程中，植物逐步形成了角质层和气孔。角质层的作用是减少水分蒸发，但同时也妨碍气体交换，因此气孔的出现是重要适应。最早出现角质层和气孔的植物类群可能是？\",\"questionhtml\":[\"A. 绿藻\",\"B. 苔藓植物\",\"C. 蕨类植物\",\"D. 种子植物\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[29]},{\"questionid\":174,\"questiontype\":1,\"question\":\"茎的初生结构中，维管束包括初生木质部和初生韧皮部。在双子叶植物中，初生木质部的分化方向是？\",\"questionhtml\":[\"A. 从外向内（外始式）\",\"B. 从内向外（内始式）\",\"C. 从中心向四周\",\"D. 没有方向\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[4]},{\"questionid\":194,\"questiontype\":1,\"question\":\"植物对高温的适应机制包括合成热激蛋白（HSP）、改变膜脂成分等。HSP的作用是帮助变性蛋白质重新折叠，这一过程需要？\",\"questionhtml\":[\"A. 水解ATP\",\"B. 合成ATP\",\"C. 氧化还原\",\"D. 光能\"],\"questionisparent\":0,\"questionanswer\":\"A\",\"questionselecttype\":0,\"points\":[14]},{\"questionid\":210,\"questiontype\":1,\"question\":\"在生态学中，阿尔利效应（Allee effect）是指种群密度过低时，个体适合度下降的现象，这可能导致种群灭绝。Allee效应的可能原因是？\",\"questionhtml\":[\"A. 捕食压力减小\",\"B. 交配困难或合作行为减少\",\"C. 资源更充足\",\"D. 竞争减弱\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[22]},{\"questionid\":212,\"questiontype\":1,\"question\":\"群落演替的顶极概念中，单元顶极理论认为一个气候区内只有一个顶级群落（气候顶极），而多元顶极理论认为存在多个顶极。单元顶极理论的主要支持者是？\",\"questionhtml\":[\"A. 达尔文\",\"B. 林德曼\",\"C. 克莱门茨\",\"D. 坦斯利\"],\"questionisparent\":0,\"questionanswer\":\"C\",\"questionselecttype\":0,\"points\":[23]},{\"questionid\":233,\"questiontype\":1,\"question\":\"杂交育种中，杂种优势（heterosis）是指杂交后代在生长势、产量等方面优于亲本的现象。杂种优势的遗传机制包括显性假说和超显性假说。显性假说认为杂种优势来自？\",\"questionhtml\":[\"A. 杂合位点产生的超亲效应\",\"B. 有利显性基因的掩盖\",\"C. 基因互作\",\"D. 染色体加倍\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[38]},{\"questionid\":236,\"questiontype\":1,\"question\":\"种质资源在育种中利用时，常通过核心种质（core collection）的概念提高效率。核心种质是？\",\"questionhtml\":[\"A. 所有种质材料\",\"B. 具有代表性的遗传多样性亚集\",\"C. 仅地方品种\",\"D. 仅野生近缘种\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[39]},{\"questionid\":254,\"questiontype\":1,\"question\":\"松柏类是裸子植物中最大和分布最广的类群。松柏类植物多为常绿乔木，叶针形或鳞片形，适应寒冷或干旱环境。松柏类的输导组织仅具管胞，无导管，这属于？\",\"questionhtml\":[\"A. 原始特征\",\"B. 适应特征\",\"C. 进化优势\",\"D. 退化特征\"],\"questionisparent\":0,\"questionanswer\":\"A\",\"questionselecttype\":0,\"points\":[19]},{\"questionid\":269,\"questiontype\":1,\"question\":\"细胞质雄性不育（CMS）在杂交种子生产中具有重要应用。CMS的育性恢复通常由核基因控制，称为恢复基因（Rf）。Rf基因通过什么机制恢复育性？\",\"questionhtml\":[\"A. 敲除不育基因\",\"B. 编辑线粒体转录本\",\"C. 增加花粉数量\",\"D. 促进叶绿体发育\"],\"questionisparent\":0,\"questionanswer\":\"B\",\"questionselecttype\":0,\"points\":[35]},{\"questionid\":270,\"questiontype\":1,\"question\":\"数量性状的遗传分析中，遗传力（heritability）是衡量遗传因素对表型变异的贡献程度。广义遗传力包括所有遗传效应，狭义遗传力仅考虑加性遗传效应。狭义遗传力较高的性状在育种中较易选择，因为？\",\"questionhtml\":[\"A. 加性效应可稳定遗传\",\"B. 显性效应占主导\",\"C. 环境变异小\",\"D. 非加性效应可传递\"],\"questionisparent\":0,\"questionanswer\":\"A\",\"questionselecttype\":0,\"points\":[36]}],\"rowsQuestions\":[]},\"2\":{\"questions\":[],\"rowsQuestions\":[{\"questionid\":4,\"questiontype\":2,\"question\":\"植物组织根据发育阶段和功能可分为多种类型。请阅读以下关于组织类型的描述，回答后续小题。\",\"questionhtml\":\"[]\",\"questionisparent\":1,\"questionanswer\":\"\",\"questionselecttype\":0,\"points\":[],\"children\":[{\"questionid\":5,\"questiontype\":1,\"question\":\"具有持续分裂能力，位于根尖和茎尖的组织是？\",\"questionhtml\":[\"A. 薄壁组织\",\"B. 分生组织\",\"C. 保护组织\",\"D. 机械组织\"],\"questionlevel\":1,\"questionparent\":4,\"questionanswer\":\"B\",\"questionselecttype\":0},{\"questionid\":6,\"questiontype\":1,\"question\":\"下列哪项不属于输导组织的功能？\",\"questionhtml\":[\"A. 运输水分\",\"B. 运输无机盐\",\"C. 运输有机物\",\"D. 支撑植物体\"],\"questionlevel\":1,\"questionparent\":4,\"questionanswer\":\"D\",\"questionselecttype\":0}]},{\"questionid\":16,\"questiontype\":2,\"question\":\"花是被子植物特有的生殖器官，其形态和结构多样性丰富。请回答以下关于花结构的问题。\",\"questionhtml\":\"[]\",\"questionisparent\":1,\"questionanswer\":\"\",\"questionselecttype\":0,\"points\":[6],\"children\":[{\"questionid\":17,\"questiontype\":1,\"question\":\"花萼和花冠合称为？\",\"questionhtml\":[\"A. 花被\",\"B. 花盘\",\"C. 花梗\",\"D. 花托\"],\"questionlevel\":1,\"questionparent\":16,\"questionanswer\":\"A\",\"questionselecttype\":0},{\"questionid\":18,\"questiontype\":1,\"question\":\"下列哪种演化趋势在花的演化中较为常见？\",\"questionhtml\":[\"A. 花部数由少变多\",\"B. 花由单性花演化为两性花\",\"C. 花由虫媒花演化为风媒花\",\"D. 花托由凸起变为凹陷\"],\"questionlevel\":2,\"questionparent\":16,\"questionanswer\":\"D\",\"questionselecttype\":0}]},{\"questionid\":36,\"questiontype\":2,\"question\":\"植物激素在生长发育中相互调控。请回答以下关于激素的问题。\",\"questionhtml\":\"[]\",\"questionisparent\":1,\"questionanswer\":\"\",\"questionselecttype\":0,\"points\":[12],\"children\":[{\"questionid\":37,\"questiontype\":1,\"question\":\"在种子休眠解除过程中，起拮抗作用的两种激素通常为？\",\"questionhtml\":[\"A. 生长素和乙烯\",\"B. 赤霉素和脱落酸\",\"C. 细胞分裂素和赤霉素\",\"D. 生长素和脱落酸\"],\"questionlevel\":2,\"questionparent\":36,\"questionanswer\":\"B\",\"questionselecttype\":0}]},{\"questionid\":28,\"questiontype\":2,\"question\":\"光合作用碳反应（暗反应）存在多种途径。请根据以下描述回答问题。\",\"questionhtml\":\"[]\",\"questionisparent\":1,\"questionanswer\":\"\",\"questionselecttype\":0,\"points\":[10],\"children\":[{\"questionid\":29,\"questiontype\":1,\"question\":\"C3途径中，CO₂被固定的第一个稳定产物是？\",\"questionhtml\":[\"A. 草酰乙酸（OAA）\",\"B. 3-磷酸甘油酸（PGA）\",\"C. 苹果酸\",\"D. 磷酸烯醇式丙酮酸（PEP）\"],\"questionlevel\":1,\"questionparent\":28,\"questionanswer\":\"B\",\"questionselecttype\":0},{\"questionid\":30,\"questiontype\":1,\"question\":\"CAM途径与C4途径的主要区别在于？\",\"questionhtml\":[\"A. 固定CO₂的酶不同\",\"B. 是否具有维管束鞘细胞\",\"C. CO₂固定和同化在时间上分离\",\"D. 最终产物不同\"],\"questionlevel\":2,\"questionparent\":28,\"questionanswer\":\"C\",\"questionselecttype\":0}]},{\"questionid\":56,\"questiontype\":2,\"question\":\"识别重要植物科的特征是分类学的基本技能。请回答以下问题。\",\"questionhtml\":\"[]\",\"questionisparent\":1,\"questionanswer\":\"\",\"questionselecttype\":0,\"points\":[21],\"children\":[{\"questionid\":57,\"questiontype\":1,\"question\":\"豆科（Leguminosae）的典型果实类型是？\",\"questionhtml\":[\"A. 角果\",\"B. 荚果\",\"C. 颖果\",\"D. 蒴果\"],\"questionlevel\":1,\"questionparent\":56,\"questionanswer\":\"B\",\"questionselecttype\":0},{\"questionid\":58,\"questiontype\":1,\"question\":\"菊科（Asteraceae）的头状花序中，边缘花通常为舌状花，中央花为管状花。菊科植物的果实类型为？\",\"questionhtml\":[\"A. 瘦果\",\"B. 坚果\",\"C. 核果\",\"D. 浆果\"],\"questionlevel\":1,\"questionparent\":56,\"questionanswer\":\"A\",\"questionselecttype\":0},{\"questionid\":59,\"questiontype\":1,\"question\":\"禾本科（Poaceae）植物的茎通常中空，具有明显的节和节间，这种茎称为？\",\"questionhtml\":[\"A. 草质茎\",\"B. 木质茎\",\"C. 秆\",\"D. 藤\"],\"questionlevel\":1,\"questionparent\":56,\"questionanswer\":\"C\",\"questionselecttype\":0}]}]}}', '{\"70\":\"B\"}', '[]', '[]');

-- ----------------------------
-- Table structure for x2_exam_history_log
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_history_log`;
CREATE TABLE `x2_exam_history_log`  (
  `ehlid` int NOT NULL AUTO_INCREMENT,
  `ehlehid` int NULL DEFAULT NULL,
  `ehlusername` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ehltype` int NULL DEFAULT NULL,
  `ehlinfo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `ehltime` int NULL DEFAULT NULL,
  PRIMARY KEY (`ehlid`) USING BTREE,
  INDEX `ehlusername`(`ehlusername` ASC) USING BTREE,
  INDEX `ehltype`(`ehltype` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_history_log
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_member
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_member`;
CREATE TABLE `x2_exam_member`  (
  `emid` int NOT NULL AUTO_INCREMENT,
  `empassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `embasicid` int NULL DEFAULT NULL,
  `emstarttime` int NULL DEFAULT NULL,
  `emendtime` int NULL DEFAULT NULL,
  PRIMARY KEY (`emid`) USING BTREE,
  INDEX `empassport`(`empassport` ASC) USING BTREE,
  INDEX `embasicid`(`embasicid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_member
-- ----------------------------
INSERT INTO `x2_exam_member` VALUES (1, '0025896gd42safx0001', 1, 1784558436, 1816094436);

-- ----------------------------
-- Table structure for x2_exam_paper
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_paper`;
CREATE TABLE `x2_exam_paper`  (
  `examid` int NOT NULL AUTO_INCREMENT,
  `examsubject` int NOT NULL DEFAULT 0,
  `exam` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `examtotalscore` int NULL DEFAULT NULL,
  `examtotaltime` int NULL DEFAULT NULL,
  `exampassmark` decimal(10, 2) NULL DEFAULT NULL,
  `examscalemodel` tinyint NULL DEFAULT NULL,
  `examsetting` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examquestions` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examscore` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examstatus` int NOT NULL DEFAULT 0,
  `examtype` int NOT NULL DEFAULT 0,
  `examauthorid` int NOT NULL DEFAULT 0,
  `examauthor` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `examtime` int NOT NULL DEFAULT 0,
  `examkeyword` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `examdecide` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`examid`) USING BTREE,
  INDEX `examstatus`(`examstatus` ASC) USING BTREE,
  INDEX `examtype`(`examtype` ASC, `examauthorid` ASC) USING BTREE,
  INDEX `examtime`(`examtime` ASC) USING BTREE,
  INDEX `examsubject`(`examsubject` ASC) USING BTREE,
  INDEX `examdecide`(`examdecide` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_paper
-- ----------------------------
INSERT INTO `x2_exam_paper` VALUES (1, 1, '植物学', 100, 60, 60.00, 0, '{\"lite\":[1,2,3,4],\"questionTypes\":{\"1\":{\"number\":\"40\",\"score\":\"2\",\"describe\":\"\",\"easyNumber\":\"20\",\"middleNumber\":\"20\",\"hardNumber\":0},\"2\":{\"number\":\"10\",\"score\":\"2\",\"describe\":\"\",\"easyNumber\":\"5\",\"middleNumber\":\"5\",\"hardNumber\":0},\"3\":{\"number\":0,\"score\":0,\"describe\":\"\",\"easyNumber\":0,\"middleNumber\":0,\"hardNumber\":0},\"4\":{\"number\":0,\"score\":0,\"describe\":\"\",\"easyNumber\":0,\"middleNumber\":0,\"hardNumber\":0}},\"examScale\":[]}', '{\"1\":{\"questions\":[],\"rowsquestions\":[]},\"2\":{\"questions\":[],\"rowsquestions\":[]},\"3\":{\"questions\":[],\"rowsquestions\":[]},\"4\":{\"questions\":[],\"rowsquestions\":[]}}', '', 0, 1, 0, 'peadmin', 1784558388, '', 0);

-- ----------------------------
-- Table structure for x2_exam_paper_session
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_paper_session`;
CREATE TABLE `x2_exam_paper_session`  (
  `esid` int NOT NULL AUTO_INCREMENT,
  `examsessionid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examsessionplanid` int NOT NULL,
  `examsessionpassport` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '0',
  `examsession` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `examsessionsetting` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examsessionsign` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examsessionbasicid` int NOT NULL DEFAULT 0,
  `examsessiontype` int NOT NULL,
  `examsessionpaperid` int UNSIGNED NOT NULL,
  `examsessionquestion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examsessionuseranswer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examsessionstarttime` int NOT NULL DEFAULT 0,
  `examsessiontime` int NOT NULL DEFAULT 0,
  `examsessiontimelist` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `examsessiontoken` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `examsessionfacelist` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL,
  `examsessionip` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `examsessionclient` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  PRIMARY KEY (`esid`) USING BTREE,
  UNIQUE INDEX `examsessionhandle`(`examsessionplanid` ASC, `examsessionpassport` ASC, `examsessionbasicid` ASC, `examsessiontype` ASC) USING BTREE,
  UNIQUE INDEX `examsessionid`(`examsessionid` ASC) USING BTREE,
  INDEX `examsessionstarttime`(`examsessionstarttime` ASC) USING BTREE,
  INDEX `examsessiontype`(`examsessiontype` ASC) USING BTREE,
  INDEX `examsessionkey`(`examsessionpaperid` ASC) USING BTREE,
  INDEX `examsessionbasic`(`examsessionbasicid` ASC) USING BTREE,
  INDEX `examsessionpassport`(`examsessionpassport` ASC) USING BTREE,
  INDEX `examsessiontoken`(`examsessiontoken` ASC) USING BTREE,
  INDEX `examsessionplanid`(`examsessionplanid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_paper_session
-- ----------------------------

-- ----------------------------
-- Table structure for x2_exam_point
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_point`;
CREATE TABLE `x2_exam_point`  (
  `pointid` int NOT NULL AUTO_INCREMENT,
  `point` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `pointsectionid` int NOT NULL DEFAULT 0,
  `pointdescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `pointstatus` int NOT NULL DEFAULT 1,
  `pointsequence` int NOT NULL,
  `pointnumber` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `pointquestions` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`pointid`) USING BTREE,
  INDEX `pointstatus`(`pointstatus` ASC) USING BTREE,
  INDEX `pointsequence`(`pointsequence` ASC) USING BTREE,
  INDEX `pointsectionid`(`pointsectionid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 41 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_point
-- ----------------------------
INSERT INTO `x2_exam_point` VALUES (1, '植物细胞的结构与功能（细胞壁、质体、液泡等）', 1, '', 1, 0, '{\"1\":8}', '{\"1\":{\"1\":[1,2,3,99,132,168,169,237]}}');
INSERT INTO `x2_exam_point` VALUES (2, '植物组织类型（分生组织、薄壁组织、输导组织、机械组织、保护组织）', 1, '', 1, 0, '{\"2\":2,\"1\":6}', '{\"2\":{\"1\":[4]},\"1\":{\"1\":[100,170,171,238],\"2\":[131,133]}}');
INSERT INTO `x2_exam_point` VALUES (3, '根的形态与初生结构（根尖分区、中柱鞘、内皮层）', 1, '', 1, 0, '{\"1\":8}', '{\"1\":{\"1\":[7,9,101,172],\"2\":[8,134,173,239]}}');
INSERT INTO `x2_exam_point` VALUES (4, '茎的初生与次生生长（维管束排列、形成层活动、年轮形成）', 1, '', 1, 0, '{\"1\":8}', '{\"1\":{\"1\":[10,11,135,240],\"2\":[12,102,174,175]}}');
INSERT INTO `x2_exam_point` VALUES (5, '叶的形态解剖（表皮、叶肉、叶脉、气孔结构）', 1, '', 1, 0, '{\"1\":7}', '{\"1\":{\"1\":[13],\"2\":[14,103,136,176,177,241]}}');
INSERT INTO `x2_exam_point` VALUES (6, '花的结构与发育（花萼、花冠、雄蕊、雌蕊的形态与演化）', 1, '', 1, 0, '{\"1\":6,\"2\":2}', '{\"1\":{\"1\":[15,178,179],\"2\":[104,137,242]},\"2\":{\"1\":[16]}}');
INSERT INTO `x2_exam_point` VALUES (7, '果实与种子的形成和类型（真果、假果、单种皮、双种皮）', 1, '', 1, 0, '{\"1\":7}', '{\"1\":{\"1\":[19,20,105,138,181,243],\"2\":[180]}}');
INSERT INTO `x2_exam_point` VALUES (8, '水分代谢（水势、根系吸水、蒸腾作用）', 2, '', 1, 0, '{\"1\":8}', '{\"1\":{\"1\":[21,22,23,139,183,244],\"2\":[106,182]}}');
INSERT INTO `x2_exam_point` VALUES (9, '矿质营养（必需元素、缺素症、离子吸收与转运）', 2, '', 1, 0, '{\"1\":8}', '{\"1\":{\"1\":[24,26,185],\"2\":[25,107,140,184,245]}}');
INSERT INTO `x2_exam_point` VALUES (10, '光合作用（光反应、暗反应、C3/C4/CAM途径）', 2, '', 1, 0, '{\"1\":6,\"2\":2}', '{\"1\":{\"1\":[27,186,187,246],\"2\":[108,141]},\"2\":{\"2\":[28]}}');
INSERT INTO `x2_exam_point` VALUES (11, '呼吸作用（糖酵解、三羧酸循环、电子传递链、发酵）', 2, '', 1, 0, '{\"1\":8}', '{\"1\":{\"1\":[31,32,33,109,188,247],\"2\":[142,189]}}');
INSERT INTO `x2_exam_point` VALUES (12, '植物激素与生长调节（生长素、赤霉素、细胞分裂素、脱落酸、乙烯）', 2, '', 1, 0, '{\"1\":7,\"2\":2}', '{\"1\":{\"1\":[34,35,190,191],\"2\":[110,143,248]},\"2\":{\"1\":[36]}}');
INSERT INTO `x2_exam_point` VALUES (13, '光形态建成与光周期现象（光受体、光周期类型）', 2, '', 1, 0, '{\"1\":6}', '{\"1\":{\"1\":[39],\"2\":[40,111,144,192,249]}}');
INSERT INTO `x2_exam_point` VALUES (14, '植物逆境生理（干旱、盐碱、低温、高温适应机制）', 2, '', 1, 0, '{\"1\":8}', '{\"1\":{\"2\":[41,42,43,112,145,193,194,250]}}');
INSERT INTO `x2_exam_point` VALUES (15, '分类等级与命名法（界门纲目科属种、双名法）', 3, '', 1, 0, '{\"1\":4}', '{\"1\":{\"1\":[44,45,146,195]}}');
INSERT INTO `x2_exam_point` VALUES (16, '主要藻类植物分类（绿藻、红藻、褐藻）', 3, '', 1, 0, '{\"1\":8}', '{\"1\":{\"1\":[46,113,147,196,251],\"2\":[47,197,252]}}');
INSERT INTO `x2_exam_point` VALUES (17, '苔藓植物（苔纲、藓纲、角苔纲）', 3, '', 1, 0, '{\"1\":7}', '{\"1\":{\"1\":[48,49,148,198,253],\"2\":[114,199]}}');
INSERT INTO `x2_exam_point` VALUES (18, '蕨类植物（石松类、木贼类、真蕨类）', 3, '', 1, 0, '{\"1\":6}', '{\"1\":{\"2\":[50,149,200,201],\"1\":[51,115]}}');
INSERT INTO `x2_exam_point` VALUES (19, '裸子植物（苏铁科、松柏科、银杏科、麻黄科）', 3, '', 1, 0, '{\"1\":8}', '{\"1\":{\"1\":[52,53,116,202],\"2\":[150,203,254,255]}}');
INSERT INTO `x2_exam_point` VALUES (20, '被子植物APG分类系统（基部类群、核心双子叶、单子叶）', 3, '', 1, 0, '{\"1\":6}', '{\"1\":{\"2\":[54,117],\"1\":[55,151,204,205]}}');
INSERT INTO `x2_exam_point` VALUES (21, '重要科属识别特征（豆科、菊科、禾本科、蔷薇科、十字花科等）', 3, '', 1, 0, '{\"2\":3,\"1\":7}', '{\"2\":{\"2\":[56]},\"1\":{\"1\":[118,206,207,208,256,257],\"2\":[152]}}');
INSERT INTO `x2_exam_point` VALUES (22, '种群生态（密度、分布格局、种群增长模型）', 4, '', 1, 0, '{\"1\":6}', '{\"1\":{\"1\":[60,61,153],\"2\":[119,209,210]}}');
INSERT INTO `x2_exam_point` VALUES (23, '群落生态（物种多样性、优势种、演替）', 4, '', 1, 0, '{\"1\":7}', '{\"1\":{\"1\":[62,63,120,211],\"2\":[154,212,258]}}');
INSERT INTO `x2_exam_point` VALUES (24, '生态系统（能量流动、物质循环、生产力）', 4, '', 1, 0, '{\"1\":8}', '{\"1\":{\"1\":[64,65,155,213,214],\"2\":[121,259,260]}}');
INSERT INTO `x2_exam_point` VALUES (25, '植物与环境的相互作用（竞争、共生、化感作用）', 4, '', 1, 0, '{\"1\":7}', '{\"1\":{\"1\":[66,67,216,261],\"2\":[122,156,215]}}');
INSERT INTO `x2_exam_point` VALUES (26, '植物区系与分布区类型（热带、亚热带、温带、高山）', 4, '', 1, 0, '{\"1\":6}', '{\"1\":{\"2\":[68,69,123,217],\"1\":[157,262]}}');
INSERT INTO `x2_exam_point` VALUES (27, '植被型与地带性植被（热带雨林、落叶阔叶林、草原、荒漠、苔原）', 4, '', 1, 0, '{\"1\":8}', '{\"1\":{\"1\":[70,71,72,124,218,219],\"2\":[158,263]}}');
INSERT INTO `x2_exam_point` VALUES (28, '植物起源与演化证据（化石、分子系统学）', 5, '', 1, 0, '{\"1\":5}', '{\"1\":{\"1\":[73],\"2\":[74,159,220,221]}}');
INSERT INTO `x2_exam_point` VALUES (29, '从水生到陆生的适应演化（胚胎保护、维管系统）', 5, '', 1, 0, '{\"1\":5}', '{\"1\":{\"2\":[75,160,222],\"1\":[76,264]}}');
INSERT INTO `x2_exam_point` VALUES (30, '种子植物与裸子植物的演化优势', 5, '', 1, 0, '{\"1\":5}', '{\"1\":{\"2\":[77,161],\"1\":[78,125,223]}}');
INSERT INTO `x2_exam_point` VALUES (31, '被子植物的起源与辐射演化', 5, '', 1, 0, '{\"1\":8}', '{\"1\":{\"2\":[79,80,126,225,265,266],\"1\":[162,224]}}');
INSERT INTO `x2_exam_point` VALUES (32, '共衍征与系统发育树构建（形态与分子数据）', 5, '', 1, 0, '{\"1\":6}', '{\"1\":{\"2\":[81,82,127,163,226,227]}}');
INSERT INTO `x2_exam_point` VALUES (33, '物种形成机制（地理隔离、多倍体化、杂交）', 5, '', 1, 0, '{\"1\":5}', '{\"1\":{\"1\":[83],\"2\":[84,128,164,228]}}');
INSERT INTO `x2_exam_point` VALUES (34, '植物基因组结构与重复序列', 6, '', 1, 0, '{\"1\":5}', '{\"1\":{\"2\":[85,86,129,267,268]}}');
INSERT INTO `x2_exam_point` VALUES (35, '细胞质遗传与母系遗传（叶绿体、线粒体DNA）', 6, '', 1, 0, '{\"1\":4}', '{\"1\":{\"2\":[87,88,269],\"1\":[229]}}');
INSERT INTO `x2_exam_point` VALUES (36, '数量性状与QTL定位', 6, '', 1, 0, '{\"1\":5}', '{\"1\":{\"2\":[89,165,230,270],\"1\":[90]}}');
INSERT INTO `x2_exam_point` VALUES (37, '植物转基因技术与基因编辑（CRISPR/Cas9）', 6, '', 1, 0, '{\"1\":6}', '{\"1\":{\"2\":[91,232],\"1\":[92,166,231,271]}}');
INSERT INTO `x2_exam_point` VALUES (38, '杂交育种、诱变育种与分子标记辅助选择', 6, '', 1, 0, '{\"2\":3,\"1\":3}', '{\"2\":{\"1\":[93]},\"1\":{\"2\":[233,234],\"1\":[272]}}');
INSERT INTO `x2_exam_point` VALUES (39, '种质资源保存与利用', 6, '', 1, 0, '{\"1\":6}', '{\"1\":{\"1\":[97,167],\"2\":[98,130,235,236]}}');

-- ----------------------------
-- Table structure for x2_exam_price
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_price`;
CREATE TABLE `x2_exam_price`  (
  `epid` int NOT NULL AUTO_INCREMENT,
  `epbasicid` int NULL DEFAULT NULL,
  `epdays` int NULL DEFAULT NULL,
  `epamount` int NULL DEFAULT NULL,
  PRIMARY KEY (`epid`) USING BTREE,
  INDEX `epbasicid`(`epbasicid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_price
-- ----------------------------
INSERT INTO `x2_exam_price` VALUES (1, 1, 365, 30);

-- ----------------------------
-- Table structure for x2_exam_question
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_question`;
CREATE TABLE `x2_exam_question`  (
  `questionid` int NOT NULL AUTO_INCREMENT,
  `questiontype` int NOT NULL DEFAULT 0,
  `question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `questionuserid` int NOT NULL DEFAULT 0,
  `questionusername` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `questionlastmodifyuser` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `questionselect` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `questionselectnumber` tinyint NOT NULL DEFAULT 0,
  `questionselecttype` tinyint NULL DEFAULT 0,
  `questionanswer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `questiondescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `questioncreatetime` int NOT NULL DEFAULT 0,
  `questionhtml` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `questionparent` int NOT NULL DEFAULT 0,
  `questionisparent` int NULL DEFAULT NULL,
  `questionchildnumber` int NULL DEFAULT NULL,
  `questionchildren` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `questionsequence` int NOT NULL DEFAULT 0,
  `questionlevel` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`questionid`) USING BTREE,
  INDEX `questioncreatetime`(`questioncreatetime` ASC) USING BTREE,
  INDEX `questiontype`(`questiontype` ASC) USING BTREE,
  INDEX `questionuserid`(`questionuserid` ASC) USING BTREE,
  INDEX `questionparent`(`questionparent` ASC) USING BTREE,
  INDEX `questionlevel`(`questionlevel` ASC) USING BTREE,
  INDEX `questionchildnumber`(`questionchildnumber` ASC) USING BTREE,
  INDEX `questionisparent`(`questionisparent` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 274 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_question
-- ----------------------------
INSERT INTO `x2_exam_question` VALUES (1, 1, '植物细胞中，具有双层膜结构且含有自身DNA的细胞器是？', 0, '', '', 'A. 核糖体和内质网\nB. 叶绿体和线粒体\nC. 高尔基体和液泡\nD. 细胞壁和质膜', 0, 0, 'B', '叶绿体和线粒体都是双层膜细胞器，且含有自身DNA，能进行半自主性复制。核糖体无膜，内质网、高尔基体和液泡为单层膜。', 0, '[\"A. 核糖体和内质网\",\"B. 叶绿体和线粒体\",\"C. 高尔基体和液泡\",\"D. 细胞壁和质膜\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (2, 1, '植物细胞壁的主要成分中，赋予细胞壁强度和刚性的物质是？', 0, '', '', 'A. 纤维素\nB. 半纤维素\nC. 果胶\nD. 木质素', 0, 0, 'A', '纤维素是细胞壁的主要结构多糖，其微纤丝网络赋予细胞壁强度和刚性。木质素主要存在于次生壁，增加硬度；果胶和半纤维素填充基质。', 0, '[\"A. 纤维素\",\"B. 半纤维素\",\"C. 果胶\",\"D. 木质素\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (3, 1, '下列哪种质体在植物细胞中负责合成和积累淀粉？', 0, '', '', 'A. 叶绿体\nB. 有色体\nC. 白色体（淀粉体）\nD. 前质体', 0, 0, 'C', '白色体中的淀粉体专门合成和储存淀粉。叶绿体进行光合作用，有色体合成色素，前质体是质体的前体。', 0, '[\"A. 叶绿体\",\"B. 有色体\",\"C. 白色体（淀粉体）\",\"D. 前质体\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (4, 2, '植物组织根据发育阶段和功能可分为多种类型。请阅读以下关于组织类型的描述，回答后续小题。', 0, '', '', '', 0, 0, '', '', 0, '[]', 0, 1, 2, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (5, 1, '具有持续分裂能力，位于根尖和茎尖的组织是？', 0, '', '', 'A. 薄壁组织\nB. 分生组织\nC. 保护组织\nD. 机械组织', 0, 0, 'B', '分生组织细胞具有持续分裂能力，位于根尖、茎尖等部位，是植物生长的来源。', 0, '[\"A. 薄壁组织\",\"B. 分生组织\",\"C. 保护组织\",\"D. 机械组织\"]', 4, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (6, 1, '下列哪项不属于输导组织的功能？', 0, '', '', 'A. 运输水分\nB. 运输无机盐\nC. 运输有机物\nD. 支撑植物体', 0, 0, 'D', '输导组织（导管和筛管）主要负责水分、无机盐和有机物的运输。支撑功能主要由机械组织（厚壁组织、厚角组织）完成。', 0, '[\"A. 运输水分\",\"B. 运输无机盐\",\"C. 运输有机物\",\"D. 支撑植物体\"]', 4, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (7, 1, '根尖分生区细胞分裂产生的细胞，经过伸长区后，在成熟区形成的主要结构是？', 0, '', '', 'A. 根冠\nB. 分生组织\nC. 根毛和维管组织\nD. 芽原基', 0, 0, 'C', '成熟区是根吸收水分和无机盐的主要区域，其表皮细胞向外突出形成根毛，内部分化出成熟的维管组织（木质部和韧皮部）。', 0, '[\"A. 根冠\",\"B. 分生组织\",\"C. 根毛和维管组织\",\"D. 芽原基\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (8, 1, '根的初生结构中，木质部和韧皮部相间排列，且二者之间有一层具有潜在分裂能力的细胞层，称为？', 0, '', '', 'A. 内皮层\nB. 中柱鞘\nC. 皮层\nD. 表皮', 0, 0, 'B', '中柱鞘是中柱最外层的一层细胞，具有潜在分裂能力，可形成侧根、不定根、木栓形成层等。内皮层是皮层最内层，具有凯氏带。', 0, '[\"A. 内皮层\",\"B. 中柱鞘\",\"C. 皮层\",\"D. 表皮\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (9, 1, '根的初生结构中，内皮层细胞壁上的带状增厚结构（凯氏带）的主要功能是？', 0, '', '', 'A. 增强机械支持\nB. 控制水分和溶质径向运输\nC. 储存营养物质\nD. 进行光合作用', 0, 0, 'B', '凯氏带是内皮层细胞径向壁和横向壁上的木质化或栓质化带状增厚，强制水分和溶质必须通过细胞质膜进入中柱，起到选择性屏障作用。', 0, '[\"A. 增强机械支持\",\"B. 控制水分和溶质径向运输\",\"C. 储存营养物质\",\"D. 进行光合作用\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (10, 1, '双子叶植物茎的初生结构中，维管束排列成一轮，这种排列方式称为？', 0, '', '', 'A. 散生维管束\nB. 环状维管束\nC. 有限维管束\nD. 无限维管束', 0, 0, 'B', '双子叶植物茎的初生维管束排列成一圈（环状），中央为髓。散生维管束是单子叶植物的特征。', 0, '[\"A. 散生维管束\",\"B. 环状维管束\",\"C. 有限维管束\",\"D. 无限维管束\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (11, 1, '茎的次生生长中，形成层细胞分裂向外产生次生韧皮部，向内产生次生木质部。形成层属于哪种组织？', 0, '', '', 'A. 顶端分生组织\nB. 侧生分生组织\nC. 居间分生组织\nD. 薄壁组织', 0, 0, 'B', '形成层属于侧生分生组织，与木栓形成层一起负责茎和根的次生加粗生长。', 0, '[\"A. 顶端分生组织\",\"B. 侧生分生组织\",\"C. 居间分生组织\",\"D. 薄壁组织\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (12, 1, '温带木本植物茎的横切面上，年轮的形成主要与下列哪种因素有关？', 0, '', '', 'A. 形成层活动周期性变化\nB. 木质部导管大小季节性变化\nC. 韧皮部筛管活性变化\nD. 木栓形成层活动', 0, 0, 'A', '年轮是形成层活动季节性变化的结果。春季形成层活动旺盛，形成导管大、壁薄的早材（春材）；夏秋季活动减弱，形成导管小、壁厚的晚材（夏材），二者构成一个年轮。', 0, '[\"A. 形成层活动周期性变化\",\"B. 木质部导管大小季节性变化\",\"C. 韧皮部筛管活性变化\",\"D. 木栓形成层活动\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (13, 1, '叶片中，进行光合作用的主要组织是？', 0, '', '', 'A. 表皮\nB. 叶肉（栅栏组织和海绵组织）\nC. 叶脉（维管束）\nD. 气孔', 0, 0, 'B', '叶肉是叶片进行光合作用的主要部位，包括栅栏组织和海绵组织，内含大量叶绿体。表皮起保护作用，叶脉起运输和支撑作用，气孔是气体交换通道。', 0, '[\"A. 表皮\",\"B. 叶肉（栅栏组织和海绵组织）\",\"C. 叶脉（维管束）\",\"D. 气孔\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (14, 1, '气孔由两个保卫细胞围成，其开闭机制主要与哪种离子的转运有关？', 0, '', '', 'A. 钙离子（Ca²⁺）\nB. 钾离子（K⁺）\nC. 钠离子（Na⁺）\nD. 氯离子（Cl⁻）', 0, 0, 'B', '气孔开闭主要由保卫细胞中钾离子（K⁺）浓度的变化引起。K⁺进入保卫细胞，渗透势降低，水分进入，细胞膨胀，气孔张开；K⁺外流则气孔关闭。', 0, '[\"A. 钙离子（Ca²⁺）\",\"B. 钾离子（K⁺）\",\"C. 钠离子（Na⁺）\",\"D. 氯离子（Cl⁻）\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (15, 1, '花的结构中，直接与有性生殖相关的部分包括雄蕊和雌蕊。雌蕊由柱头、花柱和子房组成，子房内含有？', 0, '', '', 'A. 胚珠\nB. 花粉\nC. 花药\nD. 蜜腺', 0, 0, 'A', '子房是雌蕊基部膨大部分，内含一个或多个胚珠，胚珠受精后发育成种子。花粉在花药中产生。', 0, '[\"A. 胚珠\",\"B. 花粉\",\"C. 花药\",\"D. 蜜腺\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (16, 2, '花是被子植物特有的生殖器官，其形态和结构多样性丰富。请回答以下关于花结构的问题。', 0, '', '', '', 0, 0, '', '', 0, '[]', 0, 1, 2, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (17, 1, '花萼和花冠合称为？', 0, '', '', 'A. 花被\nB. 花盘\nC. 花梗\nD. 花托', 0, 0, 'A', '花被是花萼和花冠的总称，主要起保护或吸引传粉者的作用。', 0, '[\"A. 花被\",\"B. 花盘\",\"C. 花梗\",\"D. 花托\"]', 16, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (18, 1, '下列哪种演化趋势在花的演化中较为常见？', 0, '', '', 'A. 花部数由少变多\nB. 花由单性花演化为两性花\nC. 花由虫媒花演化为风媒花\nD. 花托由凸起变为凹陷', 0, 0, 'D', '被子植物花的演化趋势包括花托由圆锥形变为凹陷（如子房下位），花部数由多到少且定数，由两性花到单性花，由虫媒到风媒等。', 0, '[\"A. 花部数由少变多\",\"B. 花由单性花演化为两性花\",\"C. 花由虫媒花演化为风媒花\",\"D. 花托由凸起变为凹陷\"]', 16, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (19, 1, '果实完全由子房发育而成，不包含花被或其他花部附属结构，这类果实称为？', 0, '', '', 'A. 真果\nB. 假果\nC. 聚合果\nD. 聚花果', 0, 0, 'A', '真果仅由子房发育而来，如桃、杏。假果除子房外，还包含花托、花萼等部分，如苹果、草莓。', 0, '[\"A. 真果\",\"B. 假果\",\"C. 聚合果\",\"D. 聚花果\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (20, 1, '种子成熟时，种皮由珠被发育而来。具有两层种皮（外种皮和内种皮）的种子称为？', 0, '', '', 'A. 单种皮种子\nB. 双种皮种子\nC. 无种皮种子\nD. 假种皮种子', 0, 0, 'B', '胚珠通常具有两层珠被（外珠被和内珠被），受精后发育为外种皮和内种皮，形成双种皮种子，如蓖麻、油菜。单种皮种子由一层珠被发育，如豆类。', 0, '[\"A. 单种皮种子\",\"B. 双种皮种子\",\"C. 无种皮种子\",\"D. 假种皮种子\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (21, 1, '植物细胞中水势（Ψw）由几个组分构成。在标准大气压下，纯水的水势规定为？', 0, '', '', 'A. 1 MPa\nB. 0 MPa\nC. -1 MPa\nD. 不确定', 0, 0, 'B', '纯水在标准大气压下的水势规定为0 MPa。植物细胞水势通常为负值，由渗透势、压力势和衬质势组成。', 0, '[\"A. 1 MPa\",\"B. 0 MPa\",\"C. -1 MPa\",\"D. 不确定\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (22, 1, '根系吸收水分的主要区域是根尖的？', 0, '', '', 'A. 根冠区\nB. 分生区\nC. 伸长区\nD. 成熟区（根毛区）', 0, 0, 'D', '成熟区（根毛区）有大量根毛，大大增加了吸收面积，是根系吸收水分和无机盐的主要区域。', 0, '[\"A. 根冠区\",\"B. 分生区\",\"C. 伸长区\",\"D. 成熟区（根毛区）\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (23, 1, '蒸腾作用的主要方式是？', 0, '', '', 'A. 皮孔蒸腾\nB. 角质层蒸腾\nC. 气孔蒸腾\nD. 伤口蒸腾', 0, 0, 'C', '气孔蒸腾是植物蒸腾作用最主要的方式，占全部蒸腾量的80%-90%。气孔是水蒸气从植物体内扩散到外界的主要通道。', 0, '[\"A. 皮孔蒸腾\",\"B. 角质层蒸腾\",\"C. 气孔蒸腾\",\"D. 伤口蒸腾\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (24, 1, '植物必需的矿质元素中，属于大量元素的是？', 0, '', '', 'A. Fe、Mn、Cu\nB. N、P、K\nC. Zn、B、Mo\nD. Cl、Ni、Na', 0, 0, 'B', '植物必需的大量元素包括碳、氢、氧、氮、磷、钾、钙、镁、硫（共9种），其中N、P、K是典型的肥料三要素。Fe、Mn、Cu、Zn、B、Mo、Cl等属微量元素。', 0, '[\"A. Fe、Mn、Cu\",\"B. N、P、K\",\"C. Zn、B、Mo\",\"D. Cl、Ni、Na\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (25, 1, '植物缺氮时，最典型的症状是？', 0, '', '', 'A. 叶片失绿黄化，老叶先出现\nB. 叶片出现坏死斑点\nC. 叶脉间失绿\nD. 植株矮小，叶片暗绿', 0, 0, 'A', '氮是叶绿素和蛋白质的重要组成元素，缺氮时叶片发黄，且老叶中的氮可转移到幼叶，故老叶先黄化。B多为缺钾或缺镁；C为缺铁或缺镁；D为缺磷症状。', 0, '[\"A. 叶片失绿黄化，老叶先出现\",\"B. 叶片出现坏死斑点\",\"C. 叶脉间失绿\",\"D. 植株矮小，叶片暗绿\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (26, 1, '植物吸收矿质离子的主要方式为主动运输，该过程需要能量和？', 0, '', '', 'A. 载体蛋白\nB. 通道蛋白\nC. 胞饮作用\nD. 离子交换', 0, 0, 'A', '主动运输需要载体蛋白参与，同时消耗能量（ATP）逆浓度梯度转运离子。通道蛋白介导被动运输。', 0, '[\"A. 载体蛋白\",\"B. 通道蛋白\",\"C. 胞饮作用\",\"D. 离子交换\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (27, 1, '光合作用的光反应发生在类囊体膜上，其产物包括ATP、NADPH以及？', 0, '', '', 'A. CO₂\nB. O₂\nC. C₃\nD. 葡萄糖', 0, 0, 'B', '光反应中，水在光系统II被裂解，释放氧气，同时产生ATP和NADPH。CO₂在暗反应中被固定，C₃是暗反应中间产物，葡萄糖是最终产物之一。', 0, '[\"A. CO₂\",\"B. O₂\",\"C. C₃\",\"D. 葡萄糖\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (28, 2, '光合作用碳反应（暗反应）存在多种途径。请根据以下描述回答问题。', 0, '', '', '', 0, 0, '', '', 0, '[]', 0, 1, 2, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (29, 1, 'C3途径中，CO₂被固定的第一个稳定产物是？', 0, '', '', 'A. 草酰乙酸（OAA）\nB. 3-磷酸甘油酸（PGA）\nC. 苹果酸\nD. 磷酸烯醇式丙酮酸（PEP）', 0, 0, 'B', 'C3途径中，CO₂与RuBP（核酮糖-1,5-二磷酸）在Rubisco催化下生成两个分子的3-磷酸甘油酸（PGA），这是第一个稳定产物。', 0, '[\"A. 草酰乙酸（OAA）\",\"B. 3-磷酸甘油酸（PGA）\",\"C. 苹果酸\",\"D. 磷酸烯醇式丙酮酸（PEP）\"]', 28, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (30, 1, 'CAM途径与C4途径的主要区别在于？', 0, '', '', 'A. 固定CO₂的酶不同\nB. 是否具有维管束鞘细胞\nC. CO₂固定和同化在时间上分离\nD. 最终产物不同', 0, 0, 'C', 'CAM途径（景天酸代谢）中，CO₂在夜间固定为苹果酸储存在液泡，白天脱羧释放CO₂进入卡尔文循环，即固定和同化在时间上分离。C4途径在空间上分离（叶肉细胞和维管束鞘细胞）。', 0, '[\"A. 固定CO₂的酶不同\",\"B. 是否具有维管束鞘细胞\",\"C. CO₂固定和同化在时间上分离\",\"D. 最终产物不同\"]', 28, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (31, 1, '植物呼吸作用中，糖酵解发生的场所是？', 0, '', '', 'A. 线粒体基质\nB. 线粒体内膜\nC. 细胞质基质\nD. 叶绿体基质', 0, 0, 'C', '糖酵解发生在细胞质基质中，将葡萄糖分解为丙酮酸，并产生少量ATP和NADH。三羧酸循环和电子传递链分别发生在线粒体基质和内膜。', 0, '[\"A. 线粒体基质\",\"B. 线粒体内膜\",\"C. 细胞质基质\",\"D. 叶绿体基质\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (32, 1, '三羧酸循环（TCA循环）中，乙酰CoA与草酰乙酸缩合生成柠檬酸，该循环发生在？', 0, '', '', 'A. 细胞质基质\nB. 线粒体基质\nC. 线粒体内膜\nD. 线粒体外膜', 0, 0, 'B', '三羧酸循环发生在线粒体基质中，是需氧呼吸第二阶段，彻底氧化丙酮酸，产生NADH、FADH₂和ATP。', 0, '[\"A. 细胞质基质\",\"B. 线粒体基质\",\"C. 线粒体内膜\",\"D. 线粒体外膜\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (33, 1, '无氧呼吸中，植物细胞将丙酮酸转化为乙醇和CO₂，该过程属于？', 0, '', '', 'A. 糖酵解\nB. 发酵\nC. 氧化磷酸化\nD. 光呼吸', 0, 0, 'B', '在无氧条件下，植物细胞进行酒精发酵，丙酮酸脱羧生成乙醛，再还原为乙醇，该过程不产生额外的ATP。发酵是糖酵解的延伸。', 0, '[\"A. 糖酵解\",\"B. 发酵\",\"C. 氧化磷酸化\",\"D. 光呼吸\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (34, 1, '植物激素中，具有促进细胞伸长生长、顶端优势等作用的激素是？', 0, '', '', 'A. 赤霉素（GA）\nB. 细胞分裂素（CTK）\nC. 生长素（IAA）\nD. 脱落酸（ABA）', 0, 0, 'C', '生长素（IAA）促进细胞伸长、诱导不定根、维持顶端优势等。赤霉素促进茎伸长和种子萌发，细胞分裂素促进细胞分裂和延缓衰老，脱落酸促进休眠和逆境响应。', 0, '[\"A. 赤霉素（GA）\",\"B. 细胞分裂素（CTK）\",\"C. 生长素（IAA）\",\"D. 脱落酸（ABA）\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (35, 1, '促进果实成熟、促进器官脱落和衰老的植物激素是？', 0, '', '', 'A. 乙烯\nB. 生长素\nC. 赤霉素\nD. 细胞分裂素', 0, 0, 'A', '乙烯是气体激素，主要促进果实成熟、器官脱落、叶片衰老等。生长素、赤霉素、细胞分裂素多促进生长和发育。', 0, '[\"A. 乙烯\",\"B. 生长素\",\"C. 赤霉素\",\"D. 细胞分裂素\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (36, 2, '植物激素在生长发育中相互调控。请回答以下关于激素的问题。', 0, '', '', '', 0, 0, '', '', 0, '[]', 0, 1, 2, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (37, 1, '在种子休眠解除过程中，起拮抗作用的两种激素通常为？', 0, '', '', 'A. 生长素和乙烯\nB. 赤霉素和脱落酸\nC. 细胞分裂素和赤霉素\nD. 生长素和脱落酸', 0, 0, 'B', '赤霉素促进种子萌发，脱落酸维持休眠，二者在种子休眠调控中具有拮抗作用。', 0, '[\"A. 生长素和乙烯\",\"B. 赤霉素和脱落酸\",\"C. 细胞分裂素和赤霉素\",\"D. 生长素和脱落酸\"]', 36, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (38, 1, '下列哪种激素主要促进细胞分裂和侧芽生长，并延缓叶片衰老？', 0, '', '', 'A. 生长素\nB. 赤霉素\nC. 细胞分裂素\nD. 乙烯', 0, 0, 'C', '细胞分裂素促进细胞分裂，促进侧芽生长（解除顶端优势），延迟叶片衰老。', 0, '[\"A. 生长素\",\"B. 赤霉素\",\"C. 细胞分裂素\",\"D. 乙烯\"]', 36, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (39, 1, '光形态建成中，感受红光和远红光的光受体是？', 0, '', '', 'A. 光敏色素\nB. 隐花色素\nC. 向光素\nD. UV-B受体', 0, 0, 'A', '光敏色素是感受红光（660nm）和远红光（730nm）的光受体，在光形态建成、光周期等过程中起关键作用。隐花色素和向光素主要感受蓝光。', 0, '[\"A. 光敏色素\",\"B. 隐花色素\",\"C. 向光素\",\"D. UV-B受体\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (40, 1, '根据光周期反应类型，短日照植物在日照长度短于临界日长时开花。下列哪种植物属于短日照植物？', 0, '', '', 'A. 小麦\nB. 菠菜\nC. 大豆\nD. 豌豆', 0, 0, 'C', '大豆是典型的短日照植物，在日照短于一定临界长度时开花。小麦、菠菜、豌豆多为长日照植物。', 0, '[\"A. 小麦\",\"B. 菠菜\",\"C. 大豆\",\"D. 豌豆\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (41, 1, '植物遭受干旱胁迫时，常通过积累哪种物质来调节渗透势，减少水分损失？', 0, '', '', 'A. 淀粉\nB. 脯氨酸\nC. 纤维素\nD. 木质素', 0, 0, 'B', '干旱胁迫下，植物细胞积累脯氨酸、甜菜碱等渗透调节物质，降低渗透势，维持细胞膨压，提高抗旱性。', 0, '[\"A. 淀粉\",\"B. 脯氨酸\",\"C. 纤维素\",\"D. 木质素\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (42, 1, '盐碱胁迫对植物的伤害主要包括离子毒害和渗透胁迫。植物通过将Na⁺区隔化在液泡中，依靠的转运蛋白主要是？', 0, '', '', 'A. H⁺-ATP酶\nB. Na⁺/H⁺反向转运体\nC. 水通道蛋白\nD. 钾离子通道', 0, 0, 'B', 'Na⁺/H⁺反向转运体（如NHX家族）将Na⁺转运入液泡，减少细胞质中Na⁺浓度，减轻离子毒害。该过程依赖液泡膜H⁺-ATP酶建立的质子驱动力。', 0, '[\"A. H⁺-ATP酶\",\"B. Na⁺\\/H⁺反向转运体\",\"C. 水通道蛋白\",\"D. 钾离子通道\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (43, 1, '植物在低温适应过程中，细胞膜中脂肪酸的不饱和度通常会增加，这有助于？', 0, '', '', 'A. 降低膜流动性\nB. 提高膜流动性\nC. 增加膜刚性\nD. 减少膜蛋白活性', 0, 0, 'B', '低温下，增加脂肪酸不饱和度可提高膜脂的流动性，维持膜的正常功能，是植物抗冷性的重要机制之一。', 0, '[\"A. 降低膜流动性\",\"B. 提高膜流动性\",\"C. 增加膜刚性\",\"D. 减少膜蛋白活性\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (44, 1, '生物分类的基本单位是？双名法中，属名和种加词分别采用什么语言形式？', 0, '', '', 'A. 种；拉丁文，拉丁文\nB. 属；拉丁文，英文\nC. 科；拉丁文，希腊文\nD. 种；英文，拉丁文', 0, 0, 'A', '种是分类的基本单位。双名法由属名（拉丁文名词）和种加词（拉丁文形容词或名词）组成，后接命名人缩写。', 0, '[\"A. 种；拉丁文，拉丁文\",\"B. 属；拉丁文，英文\",\"C. 科；拉丁文，希腊文\",\"D. 种；英文，拉丁文\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (45, 1, '在植物分类等级序列（界、门、纲、目、科、属、种）中，科与属之间的等级是？', 0, '', '', 'A. 亚科\nB. 族\nC. 目\nD. 亚属', 0, 0, 'C', '完整等级序列：界、门、纲、目、科、属、种。科与属之间没有直接插入等级，目高于科，科高于属。', 0, '[\"A. 亚科\",\"B. 族\",\"C. 目\",\"D. 亚属\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (46, 1, '下列藻类中，属于红藻门的是？', 0, '', '', 'A. 石莼\nB. 海带\nC. 紫菜\nD. 裙带菜', 0, 0, 'C', '紫菜属于红藻门（Rhodophyta）。石莼是绿藻，海带和裙带菜是褐藻。', 0, '[\"A. 石莼\",\"B. 海带\",\"C. 紫菜\",\"D. 裙带菜\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (47, 1, '褐藻的色素体中除了叶绿素a和c外，还含有大量哪种辅助色素，使其呈现褐色？', 0, '', '', 'A. 藻红蛋白\nB. 藻蓝蛋白\nC. 岩藻黄素\nD. 叶黄素', 0, 0, 'C', '褐藻含有岩藻黄素（墨角藻黄素），这是一种类胡萝卜素，掩盖了叶绿素的绿色，使藻体呈褐色。藻红蛋白和藻蓝蛋白是红藻和蓝藻的色素。', 0, '[\"A. 藻红蛋白\",\"B. 藻蓝蛋白\",\"C. 岩藻黄素\",\"D. 叶黄素\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (48, 1, '苔藓植物中，具有茎、叶分化，但无真根，且孢子体寄生在配子体上的类群是？', 0, '', '', 'A. 苔纲\nB. 藓纲\nC. 角苔纲\nD. 地钱纲', 0, 0, 'B', '藓纲植物有茎和叶分化，无真根（具假根），孢子体寄生在配子体上。苔纲多为叶状体或茎叶体，角苔纲有角状孢子体。地钱纲属于苔纲。', 0, '[\"A. 苔纲\",\"B. 藓纲\",\"C. 角苔纲\",\"D. 地钱纲\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (49, 1, '苔藓植物的生活史中，占优势的是？', 0, '', '', 'A. 孢子体世代\nB. 配子体世代\nC. 孢子体和配子体并重\nD. 无世代交替', 0, 0, 'B', '苔藓植物的配子体世代占优势，孢子体寄生在配子体上，不能独立生活。', 0, '[\"A. 孢子体世代\",\"B. 配子体世代\",\"C. 孢子体和配子体并重\",\"D. 无世代交替\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (50, 1, '蕨类植物中，具有大型叶（即叶有叶柄和叶脉分化）且孢子囊生于叶背或边缘的类群主要是？', 0, '', '', 'A. 石松类\nB. 木贼类\nC. 真蕨类\nD. 卷柏类', 0, 0, 'C', '真蕨类具有大型叶（羽状复叶或单叶），孢子囊通常生于叶背或边缘，形成孢子囊群。石松类和卷柏类具小型叶，木贼类具节和轮生叶。', 0, '[\"A. 石松类\",\"B. 木贼类\",\"C. 真蕨类\",\"D. 卷柏类\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (51, 1, '石松类植物的孢子体具有？', 0, '', '', 'A. 根、茎、叶分化，但叶为小型叶\nB. 仅具茎和叶\nC. 无根\nD. 大型叶', 0, 0, 'A', '石松类植物有根、茎、叶的分化，叶为小型叶（单脉，无叶隙），孢子囊生于叶腋或孢子叶腋。', 0, '[\"A. 根、茎、叶分化，但叶为小型叶\",\"B. 仅具茎和叶\",\"C. 无根\",\"D. 大型叶\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (52, 1, '裸子植物中，具有扇形叶，种子核果状，种皮分三层，且为孑遗植物的科是？', 0, '', '', 'A. 苏铁科\nB. 银杏科\nC. 松柏科\nD. 麻黄科', 0, 0, 'B', '银杏科仅存银杏一种，叶扇形，种子核果状（外种皮肉质，中种皮骨质，内种皮纸质），是著名的孑遗植物。', 0, '[\"A. 苏铁科\",\"B. 银杏科\",\"C. 松柏科\",\"D. 麻黄科\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (53, 1, '松柏科的球果（雌球果）由多枚珠鳞组成，胚珠着生在珠鳞上。裸子植物的胚珠是裸露的，因此种子外面没有？', 0, '', '', 'A. 种皮\nB. 胚\nC. 果皮\nD. 胚乳', 0, 0, 'C', '裸子植物的胚珠裸露，无子房包被，因此种子没有果皮包被，种子裸露。种皮由珠被发育而来。', 0, '[\"A. 种皮\",\"B. 胚\",\"C. 果皮\",\"D. 胚乳\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (54, 1, 'APG分类系统中，被子植物的基部类群包括？', 0, '', '', 'A. 双子叶植物\nB. 单子叶植物\nC. 无油樟目、睡莲目等\nD. 壳斗目', 0, 0, 'C', 'APG系统中，无油樟目、睡莲目、木兰藤目等属于被子植物的基部类群（ANITA类群），它们是最早分化的被子植物支系。', 0, '[\"A. 双子叶植物\",\"B. 单子叶植物\",\"C. 无油樟目、睡莲目等\",\"D. 壳斗目\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (55, 1, 'APG分类系统中，核心双子叶植物包括菊类、蔷薇类等。单子叶植物与双子叶植物的主要区别之一是？', 0, '', '', 'A. 叶脉类型\nB. 花基数\nC. 根系类型\nD. 以上都是', 0, 0, 'D', '单子叶植物通常具有平行脉、花基数3、须根系；双子叶植物通常为网状脉、花基数4或5、直根系。', 0, '[\"A. 叶脉类型\",\"B. 花基数\",\"C. 根系类型\",\"D. 以上都是\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (56, 2, '识别重要植物科的特征是分类学的基本技能。请回答以下问题。', 0, '', '', '', 0, 0, '', '', 0, '[]', 0, 1, 3, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (57, 1, '豆科（Leguminosae）的典型果实类型是？', 0, '', '', 'A. 角果\nB. 荚果\nC. 颖果\nD. 蒴果', 0, 0, 'B', '豆科的果实为荚果，由单心皮发育而成，成熟时沿腹缝线和背缝线开裂。角果是十字花科的果实，颖果是禾本科的果实。', 0, '[\"A. 角果\",\"B. 荚果\",\"C. 颖果\",\"D. 蒴果\"]', 56, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (58, 1, '菊科（Asteraceae）的头状花序中，边缘花通常为舌状花，中央花为管状花。菊科植物的果实类型为？', 0, '', '', 'A. 瘦果\nB. 坚果\nC. 核果\nD. 浆果', 0, 0, 'A', '菊科的果实为瘦果，由子房上位、一室发育而来，果皮与种皮分离。', 0, '[\"A. 瘦果\",\"B. 坚果\",\"C. 核果\",\"D. 浆果\"]', 56, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (59, 1, '禾本科（Poaceae）植物的茎通常中空，具有明显的节和节间，这种茎称为？', 0, '', '', 'A. 草质茎\nB. 木质茎\nC. 秆\nD. 藤', 0, 0, 'C', '禾本科植物的茎特称为秆，具有明显的节和节间，常中空（如水稻、小麦），是禾本科的重要特征。', 0, '[\"A. 草质茎\",\"B. 木质茎\",\"C. 秆\",\"D. 藤\"]', 56, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (60, 1, '种群生态学中，单位面积或单位体积内个体的数量称为？', 0, '', '', 'A. 密度\nB. 多度\nC. 盖度\nD. 频度', 0, 0, 'A', '种群密度是指单位面积（或体积）内个体数，是种群数量特征的基本指标。多度指物种个体的丰富程度，盖度指植被覆盖面积比例，频度指物种在样方中出现的频率。', 0, '[\"A. 密度\",\"B. 多度\",\"C. 盖度\",\"D. 频度\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (61, 1, '在种群增长模型中，逻辑斯蒂方程描述的种群增长具有环境容纳量（K），其增长曲线呈？', 0, '', '', 'A. J型\nB. S型\nC. 直线型\nD. 指数型', 0, 0, 'B', '逻辑斯蒂增长（S型增长）考虑了环境阻力，种群密度达到K值时增长停止，曲线呈S形。J型增长为指数增长，不考虑环境容纳量。', 0, '[\"A. J型\",\"B. S型\",\"C. 直线型\",\"D. 指数型\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (62, 1, '群落中，物种丰富度最高的群落通常具有较高的？', 0, '', '', 'A. 优势种数量\nB. 物种多样性指数\nC. 群落盖度\nD. 均匀度', 0, 0, 'B', '物种丰富度是物种多样性指数的重要组成之一，丰富度高通常意味着多样性指数高。优势种数量指优势种个数，盖度指覆盖度，均匀度指个体分配均匀程度。', 0, '[\"A. 优势种数量\",\"B. 物种多样性指数\",\"C. 群落盖度\",\"D. 均匀度\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (63, 1, '群落演替中，从裸地开始的演替称为原生演替。在原生演替中，最先出现的植物类型通常是？', 0, '', '', 'A. 乔木\nB. 灌木\nC. 地衣和苔藓\nD. 草本植物', 0, 0, 'C', '原生演替开始于无土壤的裸地，最先定居的通常是地衣和苔藓等耐旱、耐贫瘠的先锋植物，它们能促进土壤形成。', 0, '[\"A. 乔木\",\"B. 灌木\",\"C. 地衣和苔藓\",\"D. 草本植物\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (64, 1, '生态系统能量流动的特点是单向流动和？', 0, '', '', 'A. 循环往复\nB. 逐级递减\nC. 无损失\nD. 双向传递', 0, 0, 'B', '能量在生态系统中的流动是单向的（从生产者到消费者），且逐级递减（每个营养级能量传递效率约为10%-20%）。物质循环则具有全球性和循环性。', 0, '[\"A. 循环往复\",\"B. 逐级递减\",\"C. 无损失\",\"D. 双向传递\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (65, 1, '碳循环中，植物通过光合作用将大气中的CO₂固定在有机物中，又通过什么过程将碳释放回大气？', 0, '', '', 'A. 蒸腾作用\nB. 呼吸作用和分解作用\nC. 固氮作用\nD. 硝化作用', 0, 0, 'B', '植物通过呼吸作用（自身）和分解者（分解动植物遗体）将有机碳转化为CO₂释放回大气，完成碳循环。蒸腾作用释放水，固氮和硝化是氮循环过程。', 0, '[\"A. 蒸腾作用\",\"B. 呼吸作用和分解作用\",\"C. 固氮作用\",\"D. 硝化作用\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (66, 1, '两种植物共同生长时，一种植物通过分泌化学物质抑制另一种植物生长的现象称为？', 0, '', '', 'A. 竞争\nB. 寄生\nC. 化感作用\nD. 互利共生', 0, 0, 'C', '化感作用（他感作用）指植物通过释放化学物质到环境中，对其他植物产生抑制或促进效应。竞争是资源争夺，寄生是一种生物寄居在另一种生物体上获取营养。', 0, '[\"A. 竞争\",\"B. 寄生\",\"C. 化感作用\",\"D. 互利共生\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (67, 1, '豆科植物与根瘤菌的关系属于？', 0, '', '', 'A. 寄生\nB. 共生（互利共生）\nC. 化感作用\nD. 偏利共生', 0, 0, 'B', '豆科植物与根瘤菌互利共生，根瘤菌为植物提供固定态氮，植物为根瘤菌提供有机营养。', 0, '[\"A. 寄生\",\"B. 共生（互利共生）\",\"C. 化感作用\",\"D. 偏利共生\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (68, 1, '植物区系中，热带分布的类型通常具有丰富的科属多样性。下列哪个分布区类型属于热带分布？', 0, '', '', 'A. 泛热带分布\nB. 北温带分布\nC. 东亚分布\nD. 地中海分布', 0, 0, 'A', '泛热带分布是指分布在全球热带地区的类群，属于热带区系成分。北温带分布、东亚分布、地中海分布均属于温带或亚热带成分。', 0, '[\"A. 泛热带分布\",\"B. 北温带分布\",\"C. 东亚分布\",\"D. 地中海分布\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (69, 1, '中国特有植物分布区类型中，特有属最丰富的区域通常位于？', 0, '', '', 'A. 东北平原\nB. 青藏高原\nC. 横断山脉及西南地区\nD. 西北荒漠', 0, 0, 'C', '横断山脉及中国西南地区（云南、四川等）是全球生物多样性热点地区之一，特有属和特有种极为丰富。', 0, '[\"A. 东北平原\",\"B. 青藏高原\",\"C. 横断山脉及西南地区\",\"D. 西北荒漠\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (70, 1, '热带雨林的主要特征包括物种丰富度高、层次结构复杂以及？', 0, '', '', 'A. 有大量藤本和附生植物\nB. 植物多具旱生结构\nC. 优势种明显\nD. 林冠稀疏', 0, 0, 'A', '热带雨林物种极丰富，层次复杂，有大量藤本、附生植物，老茎生花现象普遍。旱生结构是荒漠植物的特征，优势种不明显，林冠连续。', 0, '[\"A. 有大量藤本和附生植物\",\"B. 植物多具旱生结构\",\"C. 优势种明显\",\"D. 林冠稀疏\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (71, 1, '落叶阔叶林是温带地区的典型植被，其树种在冬季落叶的主要适应性意义是？', 0, '', '', 'A. 减少水分蒸发和防止冻害\nB. 增加养分回流\nC. 促进来年新叶生长\nD. 减少昆虫取食', 0, 0, 'A', '冬季落叶可减少水分蒸腾和避免冰雪对叶片的冻害，是温带木本植物对寒冷干旱季节的适应策略。', 0, '[\"A. 减少水分蒸发和防止冻害\",\"B. 增加养分回流\",\"C. 促进来年新叶生长\",\"D. 减少昆虫取食\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (72, 1, '草原植被中，禾本科植物占优势，其特点是适应？', 0, '', '', 'A. 高温多雨气候\nB. 干旱和半干旱气候\nC. 低温潮湿气候\nD. 土壤贫瘠', 0, 0, 'B', '草原分布在干旱和半干旱地区，降水较少，禾草类植物具有耐旱、根系发达、叶面积小等适应性特征。', 0, '[\"A. 高温多雨气候\",\"B. 干旱和半干旱气候\",\"C. 低温潮湿气候\",\"D. 土壤贫瘠\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (73, 1, '植物进化研究中最直接的证据来源于？', 0, '', '', 'A. 化石\nB. 分子系统学\nC. 比较形态学\nD. 胚胎学', 0, 0, 'A', '化石是生物进化最直接的证据，通过地质年代中的植物化石可以了解植物形态演化、类群出现时间等。分子系统学提供间接证据，但能揭示系统发育关系。', 0, '[\"A. 化石\",\"B. 分子系统学\",\"C. 比较形态学\",\"D. 胚胎学\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (74, 1, '分子系统学中，常用的分子标记是DNA序列，例如核基因ITS、叶绿体基因rbcL等。分子系统学构建的系统发育树主要基于？', 0, '', '', 'A. 形态相似度\nB. 遗传距离或序列变异\nC. 生态位重叠\nD. 地理分布', 0, 0, 'B', '分子系统学通过比较DNA或蛋白质序列的差异，计算遗传距离，构建系统发育树，推断类群间的亲缘关系。', 0, '[\"A. 形态相似度\",\"B. 遗传距离或序列变异\",\"C. 生态位重叠\",\"D. 地理分布\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (75, 1, '植物从水生到陆生的适应演化中，一个重要关键是形成配子体保护和？', 0, '', '', 'A. 维管系统\nB. 种子\nC. 花\nD. 根', 0, 0, 'A', '陆生植物演化中，维管系统的出现（木质部和韧皮部）实现了水分和营养物质的远距离运输，是适应陆生的重要突破。胚胎保护和维管系统是最关键的适应特征。', 0, '[\"A. 维管系统\",\"B. 种子\",\"C. 花\",\"D. 根\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (76, 1, '苔藓植物虽然具有胚的保护，但没有真正的维管组织，因此它们属于？', 0, '', '', 'A. 维管植物\nB. 非维管植物\nC. 种子植物\nD. 被子植物', 0, 0, 'B', '苔藓植物没有真正的木质部和韧皮部，属于非维管植物。它们虽有类似茎叶结构，但水分运输能力有限。', 0, '[\"A. 维管植物\",\"B. 非维管植物\",\"C. 种子植物\",\"D. 被子植物\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (77, 1, '种子植物相比于蕨类植物的演化优势包括种子具有保护结构和？', 0, '', '', 'A. 配子体独立生活\nB. 孢子体占优势\nC. 花粉管形成，受精过程摆脱水的依赖\nD. 无胚结构', 0, 0, 'C', '种子植物产生种子，且花粉管将精子直接送到卵细胞，受精过程不依赖水，是重要的演化优势。种子植物孢子体占优势，配子体极度简化且寄生在孢子体上。', 0, '[\"A. 配子体独立生活\",\"B. 孢子体占优势\",\"C. 花粉管形成，受精过程摆脱水的依赖\",\"D. 无胚结构\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (78, 1, '裸子植物中，松柏类的胚珠裸露，但其种子具有种皮保护。裸子植物的种子是由什么结构发育而来？', 0, '', '', 'A. 子房\nB. 胚珠\nC. 果实\nD. 花粉', 0, 0, 'B', '种子由胚珠发育而来，胚珠包括珠被、珠心和卵细胞。裸子植物的胚珠裸露，无子房包被。', 0, '[\"A. 子房\",\"B. 胚珠\",\"C. 果实\",\"D. 花粉\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (79, 1, '关于被子植物的起源，目前多数学者认为其起源时间在？', 0, '', '', 'A. 古生代\nB. 中生代（侏罗纪至白垩纪）\nC. 新生代\nD. 元古代', 0, 0, 'B', '最早的被子植物化石出现在早白垩纪（约1.3亿年前），因此被子植物起源于中生代，并在白垩纪晚期至新生代经历了快速辐射演化。', 0, '[\"A. 古生代\",\"B. 中生代（侏罗纪至白垩纪）\",\"C. 新生代\",\"D. 元古代\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (80, 1, '被子植物在演化中，花的出现促进了与动物传粉者的协同进化，这被认为有助于？', 0, '', '', 'A. 提高光合效率\nB. 增加基因流动和适应多样性\nC. 减少水分蒸发\nD. 提高抗病性', 0, 0, 'B', '花与传粉动物的协同进化促进了异花传粉，增加了基因流动，有利于被子植物的快速分化和辐射演化。', 0, '[\"A. 提高光合效率\",\"B. 增加基因流动和适应多样性\",\"C. 减少水分蒸发\",\"D. 提高抗病性\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (81, 1, '共衍征（synapomorphy）是指两个或多个类群共同拥有的衍生性状，常用于构建系统发育树。在种子植物中，种子本身是一个共衍征，它支持？', 0, '', '', 'A. 裸子植物单系群\nB. 种子植物单系群\nC. 被子植物单系群\nD. 蕨类植物与种子植物的亲缘关系', 0, 0, 'B', '种子是所有种子植物（包括裸子植物和被子植物）的共同衍征，因此支持种子植物为一个单系群。', 0, '[\"A. 裸子植物单系群\",\"B. 种子植物单系群\",\"C. 被子植物单系群\",\"D. 蕨类植物与种子植物的亲缘关系\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (82, 1, '在构建系统发育树时，将形态特征与分子数据结合进行分析的方法称为？', 0, '', '', 'A. 分子钟\nB. 全基因组比对\nC. 总证据分析\nD. 表型聚类', 0, 0, 'C', '总证据分析将形态、分子、生理等多种数据整合起来进行系统发育推断，提高了分析的可靠性和信息量。', 0, '[\"A. 分子钟\",\"B. 全基因组比对\",\"C. 总证据分析\",\"D. 表型聚类\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (83, 1, '物种形成中，地理隔离是最常见的机制。当两个种群被山脉、河流等地理障碍隔开，各自演化后可能形成新物种，这种方式称为？', 0, '', '', 'A. 异域物种形成\nB. 同域物种形成\nC. 邻域物种形成\nD. 瞬时物种形成', 0, 0, 'A', '异域物种形成（地理隔离）是最常见的物种形成方式，种群因地理障碍隔离后独立演化，积累遗传差异，最终形成生殖隔离。', 0, '[\"A. 异域物种形成\",\"B. 同域物种形成\",\"C. 邻域物种形成\",\"D. 瞬时物种形成\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (84, 1, '多倍体化在植物物种形成中具有重要意义。一个物种的染色体组加倍，形成同源多倍体或异源多倍体。异源多倍体通常由？', 0, '', '', 'A. 同一物种染色体组加倍\nB. 两个不同物种杂交后染色体加倍\nC. 染色体片段缺失\nD. 基因突变', 0, 0, 'B', '异源多倍体由两个不同物种杂交，然后染色体组加倍形成，如小麦（异源六倍体）。同源多倍体是同一物种染色体组加倍。', 0, '[\"A. 同一物种染色体组加倍\",\"B. 两个不同物种杂交后染色体加倍\",\"C. 染色体片段缺失\",\"D. 基因突变\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (85, 1, '植物基因组中，重复序列所占比例较高，其中转座子（转座元件）属于？', 0, '', '', 'A. 单拷贝序列\nB. 中度重复序列\nC. 高度重复序列\nD. 编码序列', 0, 0, 'C', '转座子通常属于高度重复序列，在基因组中拷贝数多，可以移动或复制。单拷贝序列主要包含编码基因，中度重复序列包括rRNA基因等。', 0, '[\"A. 单拷贝序列\",\"B. 中度重复序列\",\"C. 高度重复序列\",\"D. 编码序列\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (86, 1, '植物基因组的大小在不同物种间差异很大，这种现象称为C值悖论。C值大小主要与什么相关？', 0, '', '', 'A. 基因数目\nB. 重复序列含量\nC. 染色体数目\nD. 物种进化程度', 0, 0, 'B', 'C值悖论指基因组大小与生物复杂程度不相关，差异主要源于非编码重复序列（特别是转座子）的扩增或丢失。', 0, '[\"A. 基因数目\",\"B. 重复序列含量\",\"C. 染色体数目\",\"D. 物种进化程度\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (87, 1, '细胞质遗传中，叶绿体和线粒体的DNA通常遵循母系遗传，原因是？', 0, '', '', 'A. 精子不含细胞质\nB. 卵细胞提供大部分细胞质\nC. 父本细胞质被降解\nD. 以上都是', 0, 0, 'D', '在多数被子植物中，合子的细胞质主要来自卵细胞，精子携带的细胞质（包括质体、线粒体）很少或被降解，因此质体和线粒体基因通常母系遗传。', 0, '[\"A. 精子不含细胞质\",\"B. 卵细胞提供大部分细胞质\",\"C. 父本细胞质被降解\",\"D. 以上都是\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (88, 1, '杂交育种中，利用细胞质雄性不育（CMS）材料可以方便地生产杂交种子。CMS通常由什么控制？', 0, '', '', 'A. 核基因\nB. 线粒体基因\nC. 叶绿体基因\nD. 质粒', 0, 0, 'B', '细胞质雄性不育（CMS）通常由线粒体基因组中的不育基因控制，是核质互作的结果，可用于杂交种子生产中省去除雄环节。', 0, '[\"A. 核基因\",\"B. 线粒体基因\",\"C. 叶绿体基因\",\"D. 质粒\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (89, 1, '数量性状（如产量、株高）受多基因控制，且对环境敏感。定位控制数量性状的基因位点称为？', 0, '', '', 'A. RFLP\nB. SSR\nC. QTL\nD. SNP', 0, 0, 'C', 'QTL（数量性状位点）是控制数量性状的基因所在的染色体区域。RFLP、SSR、SNP是分子标记类型，可用于QTL定位。', 0, '[\"A. RFLP\",\"B. SSR\",\"C. QTL\",\"D. SNP\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (90, 1, 'QTL定位通常需要构建遗传连锁图谱，并使用分子标记对分离群体进行基因型分析和性状表型鉴定。常用的分子标记包括SSR和SNP，其中SNP的全称是？', 0, '', '', 'A. 单核苷酸多态性\nB. 简单重复序列\nC. 随机扩增多态性DNA\nD. 扩增片段长度多态性', 0, 0, 'A', 'SNP（Single Nucleotide Polymorphism）指基因组上单个核苷酸的变异，是目前应用最广泛的分子标记之一。SSR是简单重复序列。', 0, '[\"A. 单核苷酸多态性\",\"B. 简单重复序列\",\"C. 随机扩增多态性DNA\",\"D. 扩增片段长度多态性\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (91, 1, 'CRISPR/Cas9基因编辑技术中，Cas9蛋白的作用是？', 0, '', '', 'A. 识别DNA靶点\nB. 切割双链DNA\nC. 连接DNA片段\nD. 转录目的基因', 0, 0, 'B', 'Cas9蛋白是一种核酸内切酶，在guide RNA引导下结合靶DNA并切割双链，造成DNA断裂，从而启动细胞修复机制，实现基因编辑。', 0, '[\"A. 识别DNA靶点\",\"B. 切割双链DNA\",\"C. 连接DNA片段\",\"D. 转录目的基因\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (92, 1, '植物转基因技术中，最常用的载体是？', 0, '', '', 'A. 质粒\nB. Ti质粒（农杆菌转化）\nC. 病毒载体\nD. 脂质体', 0, 0, 'B', '农杆菌介导的转化利用Ti质粒将目的基因整合到植物基因组中，是应用最广泛的植物转基因方法。', 0, '[\"A. 质粒\",\"B. Ti质粒（农杆菌转化）\",\"C. 病毒载体\",\"D. 脂质体\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (93, 2, '育种技术不断发展，传统杂交育种与现代分子育种各有优势。请回答以下问题。', 0, '', '', '', 0, 0, '', '', 0, '[]', 0, 1, 3, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (94, 1, '杂交育种中，两个纯合亲本杂交后，F1代自交，F2代发生性状分离。育种家在F2代开始选择单株，主要因为？', 0, '', '', 'A. F2代表现型稳定遗传\nB. F2代基因型纯合\nC. F2代出现性状重组和变异，选择效率高\nD. F2代全部为优良性状', 0, 0, 'C', 'F2代是性状分离最充分的世代，目标性状的重组体出现，选择单株可从中选出优良基因型组合。', 0, '[\"A. F2代表现型稳定遗传\",\"B. F2代基因型纯合\",\"C. F2代出现性状重组和变异，选择效率高\",\"D. F2代全部为优良性状\"]', 93, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (95, 1, '分子标记辅助选择（MAS）相比于传统表型选择的优势在于？', 0, '', '', 'A. 不受环境影响，可在苗期选择\nB. 成本更低\nC. 不需要DNA技术\nD. 可直接改良数量性状', 0, 0, 'A', 'MAS基于基因型筛选，不受环境和管理因素的影响，可在苗期进行早期选择，加速育种进程。', 0, '[\"A. 不受环境影响，可在苗期选择\",\"B. 成本更低\",\"C. 不需要DNA技术\",\"D. 可直接改良数量性状\"]', 93, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (96, 1, '诱变育种中，物理诱变剂（如γ射线）和化学诱变剂（如EMS）的主要作用是？', 0, '', '', 'A. 促进基因重组\nB. 引起基因突变\nC. 增加染色体数目\nD. 诱导外源基因转入', 0, 0, 'B', '诱变育种利用物理或化学诱变剂诱导植物产生基因突变，再从中筛选符合育种目标的突变体。', 0, '[\"A. 促进基因重组\",\"B. 引起基因突变\",\"C. 增加染色体数目\",\"D. 诱导外源基因转入\"]', 93, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (97, 1, '种质资源保存中，种子保存在低温库中可延长寿命，主要原因是低温降低了种子的？', 0, '', '', 'A. 含水量\nB. 呼吸速率\nC. 基因突变率\nD. 酶活性', 0, 0, 'B', '低温降低种子呼吸速率和代谢活动，减少营养物质消耗，从而延长种子寿命。低含水量也有助于保存，但低温主要通过降低呼吸作用。', 0, '[\"A. 含水量\",\"B. 呼吸速率\",\"C. 基因突变率\",\"D. 酶活性\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (98, 1, '植物种质资源除了种子保存外，还可以通过离体保存（组织培养）和田间种质圃等方式保存。离体保存适合于？', 0, '', '', 'A. 顽拗型种子\nB. 常规种子\nC. 所有植物\nD. 仅木本植物', 0, 0, 'A', '顽拗型种子（如椰子、可可）不耐干燥和低温，不适合种子库保存，通常采用离体保存（茎尖培养、胚培养）等方法。', 0, '[\"A. 顽拗型种子\",\"B. 常规种子\",\"C. 所有植物\",\"D. 仅木本植物\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (99, 1, '植物细胞中，液泡的主要功能不包括以下哪项？', 0, '', '', 'A. 维持细胞膨压\nB. 储存营养物质\nC. 合成蛋白质\nD. 参与细胞自噬', 0, 0, 'C', '蛋白质合成主要在核糖体进行。液泡功能包括维持膨压、储存离子和代谢物、参与细胞自噬和胞内消化。', 0, '[\"A. 维持细胞膨压\",\"B. 储存营养物质\",\"C. 合成蛋白质\",\"D. 参与细胞自噬\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (100, 1, '下列哪种组织类型是植物体内分布最广、分化程度较低、具有潜在分裂能力的薄壁组织？', 0, '', '', 'A. 分生组织\nB. 基本组织\nC. 机械组织\nD. 输导组织', 0, 0, 'B', '基本组织（薄壁组织）是植物体内分布最广、分化程度较低的组织，具有同化、贮藏、通气和再生等功能，部分细胞可恢复分裂能力。', 0, '[\"A. 分生组织\",\"B. 基本组织\",\"C. 机械组织\",\"D. 输导组织\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (101, 1, '根的成熟区中，导管和筛管分别属于哪种组织类型？', 0, '', '', 'A. 机械组织和薄壁组织\nB. 输导组织和输导组织\nC. 保护组织和薄壁组织\nD. 分生组织和保护组织', 0, 0, 'B', '导管（运输水分和无机盐）和筛管（运输有机物）都属于输导组织。', 0, '[\"A. 机械组织和薄壁组织\",\"B. 输导组织和输导组织\",\"C. 保护组织和薄壁组织\",\"D. 分生组织和保护组织\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (102, 1, '双子叶植物茎的次生结构中，年轮的形成与形成层的活动有关。在温带气候下，一个年轮通常包括早材和晚材。晚材的特点是？', 0, '', '', 'A. 导管大、壁薄\nB. 导管小、壁厚\nC. 纤维少\nD. 颜色浅', 0, 0, 'B', '晚材（夏材）形成于生长季后期，导管直径小、壁厚，木纤维多，色深，质地紧密。早材（春材）导管大、壁薄，色浅。', 0, '[\"A. 导管大、壁薄\",\"B. 导管小、壁厚\",\"C. 纤维少\",\"D. 颜色浅\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (103, 1, '叶片中，气孔的开闭受保卫细胞膨压调节。保卫细胞具有哪种特殊结构，有助于气孔的快速开闭？', 0, '', '', 'A. 角质层厚\nB. 细胞壁不均匀加厚\nC. 含大量叶绿体\nD. 没有液泡', 0, 0, 'B', '保卫细胞细胞壁不均匀加厚（近孔壁厚，远孔壁薄），当细胞吸水膨胀时，薄壁处伸长多，使细胞弯曲，气孔张开。', 0, '[\"A. 角质层厚\",\"B. 细胞壁不均匀加厚\",\"C. 含大量叶绿体\",\"D. 没有液泡\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (104, 1, '被子植物的花中，如果花萼和花冠均不存在，则称为无被花。下列哪种花通常无被花？', 0, '', '', 'A. 百合\nB. 月季\nC. 杨树（柔荑花序）\nD. 豌豆', 0, 0, 'C', '杨、柳等柔荑花序的植物常为无被花（缺花萼和花冠），以适应风媒传粉。百合有花被片，月季和豌豆有花萼和花冠。', 0, '[\"A. 百合\",\"B. 月季\",\"C. 杨树（柔荑花序）\",\"D. 豌豆\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (105, 1, '果实由子房和花的其他部分（如花托、花萼）共同发育而成，称为假果。以下属于假果的是？', 0, '', '', 'A. 桃\nB. 葡萄\nC. 梨\nD. 大豆', 0, 0, 'C', '梨是假果，子房下位，果实由子房和花托共同发育。桃、葡萄、大豆属于真果（由子房发育）。', 0, '[\"A. 桃\",\"B. 葡萄\",\"C. 梨\",\"D. 大豆\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (106, 1, '水分在植物体内运输的动力包括根压、蒸腾拉力等。其中，蒸腾拉力占主导地位，它主要来自叶片气孔蒸腾产生的？', 0, '', '', 'A. 正压\nB. 负压（张力）\nC. 渗透压\nD. 水势差', 0, 0, 'B', '蒸腾作用使叶片细胞水势降低，产生负压（张力），通过水柱的连续性将水分从根部向上拉，这是水分上升的主要动力。', 0, '[\"A. 正压\",\"B. 负压（张力）\",\"C. 渗透压\",\"D. 水势差\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (107, 1, '矿质元素中，镁是叶绿素的中心元素，缺镁时叶片会出现脉间失绿。镁离子在植物体内的移动性？', 0, '', '', 'A. 不易移动\nB. 可移动，老叶症状先出现\nC. 不可移动\nD. 仅在韧皮部移动', 0, 0, 'B', '镁在植物体内移动性较强，缺镁时老叶中的镁可转移到幼叶，因此老叶先出现脉间失绿症状。', 0, '[\"A. 不易移动\",\"B. 可移动，老叶症状先出现\",\"C. 不可移动\",\"D. 仅在韧皮部移动\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (108, 1, '光合作用的暗反应中，Rubisco（核酮糖-1,5-二磷酸羧化酶/加氧酶）具有双重活性，既催化羧化反应，也催化加氧反应。加氧反应导致光呼吸，该过程消耗能量并释放？', 0, '', '', 'A. O₂\nB. CO₂\nC. 乙醇酸\nD. ATP', 0, 0, 'B', 'Rubisco加氧反应导致乙醇酸代谢，在光呼吸过程中最终将有机碳转化为CO₂释放，并消耗能量。', 0, '[\"A. O₂\",\"B. CO₂\",\"C. 乙醇酸\",\"D. ATP\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (109, 1, '呼吸作用中，电子传递链位于线粒体内膜上，最终电子受体是？', 0, '', '', 'A. NAD⁺\nB. 氧气（O₂）\nC. 水\nD. 二氧化碳', 0, 0, 'B', '需氧呼吸的电子传递链最终将电子传递给氧气，生成水。氧气是最终的电子受体。', 0, '[\"A. NAD⁺\",\"B. 氧气（O₂）\",\"C. 水\",\"D. 二氧化碳\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (110, 1, '植物激素中，赤霉素（GA）的主要生理效应包括促进茎伸长和诱导α-淀粉酶合成。在种子萌发中，赤霉素促进α-淀粉酶合成的部位是？', 0, '', '', 'A. 胚芽\nB. 胚乳\nC. 糊粉层\nD. 胚根', 0, 0, 'C', '赤霉素在种子萌发时诱导糊粉层细胞合成α-淀粉酶，将淀粉水解为糖供胚生长利用。', 0, '[\"A. 胚芽\",\"B. 胚乳\",\"C. 糊粉层\",\"D. 胚根\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (111, 1, '光形态建成中，隐花色素和向光素主要感受蓝光，参与调控植物向光性和光周期等。向光素的主要功能是？', 0, '', '', 'A. 感受红光/远红光比例\nB. 介导蓝光诱导的向光反应\nC. 调控开花时间\nD. 参与UV-B响应', 0, 0, 'B', '向光素是蓝光受体，主要介导植物的向光性反应、叶绿体运动和气孔开放等。光敏色素感受红光/远红光，隐花色素调节光周期和开花。', 0, '[\"A. 感受红光\\/远红光比例\",\"B. 介导蓝光诱导的向光反应\",\"C. 调控开花时间\",\"D. 参与UV-B响应\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (112, 1, '植物在高温胁迫下，会诱导热激蛋白（HSP）的合成。热激蛋白的主要功能是？', 0, '', '', 'A. 促进光合作用\nB. 作为分子伴侣，帮助蛋白质正确折叠\nC. 增加膜流动性\nD. 清除活性氧', 0, 0, 'B', '热激蛋白（HSP）作为分子伴侣，协助变性蛋白质重新折叠，防止蛋白质聚集，提高植物耐热性。', 0, '[\"A. 促进光合作用\",\"B. 作为分子伴侣，帮助蛋白质正确折叠\",\"C. 增加膜流动性\",\"D. 清除活性氧\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (113, 1, '藻类植物中，绿藻门植物的色素体含有叶绿素a和b，与高等植物相同。绿藻的贮存物主要是？', 0, '', '', 'A. 淀粉\nB. 褐藻淀粉\nC. 红藻淀粉\nD. 葡萄糖', 0, 0, 'A', '绿藻贮存淀粉，与高等植物一致。褐藻贮存褐藻淀粉，红藻贮存红藻淀粉。', 0, '[\"A. 淀粉\",\"B. 褐藻淀粉\",\"C. 红藻淀粉\",\"D. 葡萄糖\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (114, 1, '苔藓植物中，角苔纲的孢子体形态独特，呈角状，基部有居间分生组织。角苔的配子体多为？', 0, '', '', 'A. 叶状体\nB. 茎叶体\nC. 丝状体\nD. 球状体', 0, 0, 'A', '角苔纲配子体为简单的叶状体（片状），无茎叶分化。苔纲多为叶状体，藓纲为茎叶体。', 0, '[\"A. 叶状体\",\"B. 茎叶体\",\"C. 丝状体\",\"D. 球状体\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (115, 1, '真蕨类植物的孢子囊群通常生于叶背，外有囊群盖（如鳞毛蕨）或无盖。真蕨类的配子体为？', 0, '', '', 'A. 原叶体（心形）\nB. 丝状体\nC. 叶状体\nD. 球状体', 0, 0, 'A', '真蕨类配子体为原叶体，呈心形，具有假根，独立生活，能进行光合作用。', 0, '[\"A. 原叶体（心形）\",\"B. 丝状体\",\"C. 叶状体\",\"D. 球状体\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (116, 1, '裸子植物中，苏铁科（苏铁目）植物通常具有大型羽状复叶，茎粗壮。苏铁的种子裸露，其胚珠着生在大孢子叶上。苏铁类植物属于？', 0, '', '', 'A. 松柏类\nB. 银杏类\nC. 苏铁类\nD. 麻黄类', 0, 0, 'C', '苏铁科是苏铁类植物的代表，具有大型羽状复叶，大孢子叶呈叶状，胚珠裸露着生。', 0, '[\"A. 松柏类\",\"B. 银杏类\",\"C. 苏铁类\",\"D. 麻黄类\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (117, 1, 'APG分类系统中，被子植物分为基部类群、核心双子叶植物和单子叶植物等主要支系。其中，核心双子叶植物包括蔷薇类、菊类等。蔷薇类中有一个大科，其果实为核果、梨果或聚合果，该科是？', 0, '', '', 'A. 蔷薇科\nB. 豆科\nC. 菊科\nD. 禾本科', 0, 0, 'A', '蔷薇科是蔷薇类的重要科，果实类型多样，包括核果（桃）、梨果（苹果）、聚合瘦果（草莓）等。', 0, '[\"A. 蔷薇科\",\"B. 豆科\",\"C. 菊科\",\"D. 禾本科\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (118, 1, '十字花科（Brassicaceae）的重要特征包括十字形花冠、四强雄蕊（长雄蕊4短雄蕊2）和角果。下列哪种蔬菜属于十字花科？', 0, '', '', 'A. 白菜\nB. 番茄\nC. 马铃薯\nD. 辣椒', 0, 0, 'A', '白菜（油菜、甘蓝等）属于十字花科。番茄、马铃薯和辣椒属于茄科。', 0, '[\"A. 白菜\",\"B. 番茄\",\"C. 马铃薯\",\"D. 辣椒\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (119, 1, '种群分布格局中，个体在空间上呈均匀分布、随机分布和集群分布三种类型。在自然界中，集群分布最常见，原因是？', 0, '', '', 'A. 环境资源均匀\nB. 种子散布限制和微环境异质性\nC. 种内竞争强烈\nD. 人为干扰', 0, 0, 'B', '集群分布是自然界最常见的分布格局，主要由种子散布方式（母树周围）和环境资源斑块分布导致。', 0, '[\"A. 环境资源均匀\",\"B. 种子散布限制和微环境异质性\",\"C. 种内竞争强烈\",\"D. 人为干扰\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (120, 1, '群落演替中，从已存在土壤（如农田弃耕）开始的演替称为次生演替。相比于原生演替，次生演替的特点是？', 0, '', '', 'A. 速度较慢\nB. 速度较快\nC. 不经过先锋植物阶段\nD. 最终不可能形成顶级群落', 0, 0, 'B', '次生演替有土壤和种子库基础，因此速度通常比原生演替快，但仍会经历草本、灌木到乔木等阶段。', 0, '[\"A. 速度较慢\",\"B. 速度较快\",\"C. 不经过先锋植物阶段\",\"D. 最终不可能形成顶级群落\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (121, 1, '在生态系统中，初级生产力是指单位时间内生产者固定的能量或有机物的量。热带雨林的初级生产力通常较高，主要是因为？', 0, '', '', 'A. 降水丰富、温度高、光照充足\nB. 土壤肥沃\nC. 物种多样性低\nD. 分解速率慢', 0, 0, 'A', '热带雨林水热条件优越，光合作用旺盛，初级生产力高。土壤肥力通常不高（养分循环快），物种多样，分解速率快。', 0, '[\"A. 降水丰富、温度高、光照充足\",\"B. 土壤肥沃\",\"C. 物种多样性低\",\"D. 分解速率慢\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (122, 1, '植物与微生物之间的共生关系包括菌根共生。菌根中，丛枝菌根（AM）对植物磷营养吸收有重要促进作用。AM菌根真菌属于？', 0, '', '', 'A. 子囊菌\nB. 担子菌\nC. 球囊菌门\nD. 半知菌', 0, 0, 'C', '丛枝菌根（AM）真菌属于球囊菌门（Glomeromycota），与约80%的陆生植物形成共生，促进磷等矿质营养吸收。', 0, '[\"A. 子囊菌\",\"B. 担子菌\",\"C. 球囊菌门\",\"D. 半知菌\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (123, 1, '植物区系成分中，东亚分布类型指分布在东亚地区的特有属种。下列哪个属属于东亚分布？', 0, '', '', 'A. 杜鹃花属\nB. 杉木属（Cunninghamia）\nC. 松属\nD. 栎属', 0, 0, 'B', '杉木属（Cunninghamia）为东亚特有分布属，分布于中国和东南亚。杜鹃花属、松属和栎属为世界广布或北温带分布。', 0, '[\"A. 杜鹃花属\",\"B. 杉木属（Cunninghamia）\",\"C. 松属\",\"D. 栎属\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (124, 1, '苔原植被生长在寒带地区，以地衣、苔藓和小型灌木为主，其特点是？', 0, '', '', 'A. 高大乔木丰富\nB. 生长季短，植物矮小\nC. 生物多样性极高\nD. 土壤深厚肥沃', 0, 0, 'B', '苔原气候严寒，生长季短，植物矮小（地衣、苔藓、矮灌木），没有高大乔木。生物多样性较低，土壤下常有永冻层。', 0, '[\"A. 高大乔木丰富\",\"B. 生长季短，植物矮小\",\"C. 生物多样性极高\",\"D. 土壤深厚肥沃\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (125, 1, '植物进化中，种子植物的出现晚于蕨类植物。种子植物的优势之一是种子有休眠特性，这有利于？', 0, '', '', 'A. 快速萌发\nB. 度过不良环境\nC. 减少能量消耗\nD. 增加需光性', 0, 0, 'B', '种子休眠是种子植物的重要适应特性，可使种子在不利环境下暂时不萌发，等待适宜条件，提高存活率。', 0, '[\"A. 快速萌发\",\"B. 度过不良环境\",\"C. 减少能量消耗\",\"D. 增加需光性\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (126, 1, '被子植物中，多倍体化是重要演化机制。多倍体通常具有更大的细胞和器官，这可能带来什么优势？', 0, '', '', 'A. 更小的种子\nB. 更强的适应性和生态多样性\nC. 开花更早\nD. 根系更浅', 0, 0, 'B', '多倍体植物常常表现出更强的适应性和更广泛的生态分布，许多农作物（小麦、棉花等）都是多倍体。', 0, '[\"A. 更小的种子\",\"B. 更强的适应性和生态多样性\",\"C. 开花更早\",\"D. 根系更浅\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (127, 1, '在系统发育树中，外群（outgroup）的作用是？', 0, '', '', 'A. 确定树的根部\nB. 增加物种数量\nC. 显示分子进化速率\nD. 用于估算分化时间', 0, 0, 'A', '外群是与研究类群亲缘关系较远的类群，用于给系统发育树定根，确定进化方向。', 0, '[\"A. 确定树的根部\",\"B. 增加物种数量\",\"C. 显示分子进化速率\",\"D. 用于估算分化时间\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (128, 1, '物种形成中，杂交可以导致基因渗入（introgression），即一个物种的基因进入另一个物种的基因库。这在植物中常见，因为？', 0, '', '', 'A. 花粉传播距离远\nB. 种间杂交后代可育性较高\nC. 植物不能移动\nD. 自交不亲和', 0, 0, 'B', '植物种间杂交后代有时具有较高的可育性（尤其多倍体后），且可多次回交，导致基因渗入，这是植物进化的重要机制。', 0, '[\"A. 花粉传播距离远\",\"B. 种间杂交后代可育性较高\",\"C. 植物不能移动\",\"D. 自交不亲和\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (129, 1, '植物基因组中，转座子（转座元件）的活性受表观遗传调控（如DNA甲基化）。DNA甲基化通常抑制转座子转座，从而？', 0, '', '', 'A. 增加基因组突变率\nB. 维持基因组稳定性\nC. 激活基因表达\nD. 促进转座子扩增', 0, 0, 'B', 'DNA甲基化等表观修饰使转座子异染色质化，抑制其转座活性，维持基因组稳定性，减少有害突变。', 0, '[\"A. 增加基因组突变率\",\"B. 维持基因组稳定性\",\"C. 激活基因表达\",\"D. 促进转座子扩增\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (130, 1, '植物育种中，利用种质资源进行预育种（pre-breeding）的目的是将野生种的优良基因（如抗病基因）导入栽培种。这过程中常用的方法是？', 0, '', '', 'A. 回交育种\nB. 诱变育种\nC. 转基因技术\nD. 单倍体育种', 0, 0, 'A', '回交育种是将野生种或近缘种的有利基因通过回交逐渐导入栽培品种，同时恢复栽培种优良背景的常用方法。', 0, '[\"A. 回交育种\",\"B. 诱变育种\",\"C. 转基因技术\",\"D. 单倍体育种\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (131, 1, '植物组织培养中，通过调整激素比例可以诱导器官发生或体细胞胚胎发生。高浓度细胞分裂素与低浓度生长素通常有利于诱导？', 0, '', '', 'A. 根形成\nB. 芽形成\nC. 愈伤组织生长\nD. 体细胞胚成熟', 0, 0, 'B', '植物组织培养中，细胞分裂素/生长素比例高时促进芽分化，比例低时促进根分化，二者比例中等时愈伤组织增殖。', 0, '[\"A. 根形成\",\"B. 芽形成\",\"C. 愈伤组织生长\",\"D. 体细胞胚成熟\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (132, 1, '植物细胞中，细胞壁分为初生壁和次生壁。次生壁通常在细胞停止生长后形成，其特点包括？', 0, '', '', 'A. 薄而弹性大\nB. 富含纤维素，常有木质素沉积\nC. 没有纤维素\nD. 仅存在于薄壁组织中', 0, 0, 'B', '次生壁在细胞成熟后加厚，纤维素含量高，常有木质素沉积，使细胞壁坚硬并具疏水性。主要存在于厚壁组织、导管、纤维等。', 0, '[\"A. 薄而弹性大\",\"B. 富含纤维素，常有木质素沉积\",\"C. 没有纤维素\",\"D. 仅存在于薄壁组织中\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (133, 1, '保护组织包括表皮和周皮。周皮是由木栓形成层产生的次生保护组织，其组成不包括？', 0, '', '', 'A. 木栓层\nB. 栓内层\nC. 木栓形成层\nD. 表皮', 0, 0, 'D', '周皮由木栓层、木栓形成层和栓内层组成，替代茎根次生生长后的表皮。表皮是初生保护组织。', 0, '[\"A. 木栓层\",\"B. 栓内层\",\"C. 木栓形成层\",\"D. 表皮\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (134, 1, '根的初生结构中，内皮层细胞中凯氏带带状的增厚是木质化或栓质化的。凯氏带对水分运输的阻挡作用在根的哪个部位最为关键？', 0, '', '', 'A. 表皮\nB. 皮层\nC. 内皮层\nD. 中柱鞘', 0, 0, 'C', '内皮层的凯氏带强制水分和溶质必须通过内皮层细胞质膜进入中柱，是控制根径向运输的关键屏障。', 0, '[\"A. 表皮\",\"B. 皮层\",\"C. 内皮层\",\"D. 中柱鞘\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (135, 1, '单子叶植物茎通常无次生生长，维管束散生。玉米茎的维管束为有限维管束，因为？', 0, '', '', 'A. 没有形成层\nB. 形成层活动受限\nC. 韧皮部发育不良\nD. 木质部退化', 0, 0, 'A', '单子叶植物茎中维管束无形成层，因此不能进行次生生长，属有限维管束。双子叶植物茎维管束有形成层，属无限维管束。', 0, '[\"A. 没有形成层\",\"B. 形成层活动受限\",\"C. 韧皮部发育不良\",\"D. 木质部退化\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (136, 1, '叶片的表皮细胞通常不具有叶绿体，但保卫细胞含有叶绿体。气孔通常分布在叶片的上下表皮，对于浮水叶（如睡莲），气孔主要分布在？', 0, '', '', 'A. 上表皮\nB. 下表皮\nC. 上下表皮均有\nD. 无气孔', 0, 0, 'A', '浮水叶（如睡莲）气孔主要分布在上表皮（近水面），以适应气体交换。陆生植物气孔多分布于下表皮。', 0, '[\"A. 上表皮\",\"B. 下表皮\",\"C. 上下表皮均有\",\"D. 无气孔\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (137, 1, '花的发育受A、B、C类基因调控，形成ABCDE模型。在花器官发育中，B类基因突变可能导致？', 0, '', '', 'A. 花瓣转变为萼片\nB. 雄蕊转变为心皮\nC. 萼片转变为心皮\nD. 心皮转变为雄蕊', 0, 0, 'A', 'ABCDE模型中，B类基因决定花瓣和雄蕊发育。B类基因突变时，花瓣变成萼片，雄蕊变成心皮。', 0, '[\"A. 花瓣转变为萼片\",\"B. 雄蕊转变为心皮\",\"C. 萼片转变为心皮\",\"D. 心皮转变为雄蕊\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (138, 1, '种子中，胚的结构包括胚芽、胚轴、胚根和子叶。双子叶植物的胚通常有两枚子叶，而单子叶植物有一枚子叶（盾片）。种子萌发时，首先突破种皮的结构通常是？', 0, '', '', 'A. 胚芽\nB. 胚根\nC. 子叶\nD. 胚轴', 0, 0, 'B', '种子萌发时，胚根首先突破种皮，形成根，固定植株并吸收水分和无机盐。', 0, '[\"A. 胚芽\",\"B. 胚根\",\"C. 子叶\",\"D. 胚轴\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (139, 1, '植物水分代谢中，水势的概念是化学势差。水势高的水会流向水势低处。在土壤-植物-大气连续体中，水势梯度方向是？', 0, '', '', 'A. 大气 > 植物 > 土壤\nB. 土壤 > 植物 > 大气\nC. 植物 > 土壤 > 大气\nD. 大气 > 土壤 > 植物', 0, 0, 'B', '水分从土壤（水势高）经过植物（水势较低）流向大气（水势极低），形成连续的水势梯度，驱动水分的运输。', 0, '[\"A. 大气 > 植物 > 土壤\",\"B. 土壤 > 植物 > 大气\",\"C. 植物 > 土壤 > 大气\",\"D. 大气 > 土壤 > 植物\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (140, 1, '植物矿质营养中，微量元素硼（B）参与花粉萌发和糖运输。缺硼的典型症状包括？', 0, '', '', 'A. 老叶黄化\nB. 新叶卷曲、生长点坏死\nC. 叶脉间失绿\nD. 茎秆软弱', 0, 0, 'B', '缺硼时，生长点和幼叶先受影响，出现卷曲、坏死，因为硼不易移动。老叶黄化多为缺氮，脉间失绿多为缺镁或缺铁，茎秆软弱多为缺钾或缺硅。', 0, '[\"A. 老叶黄化\",\"B. 新叶卷曲、生长点坏死\",\"C. 叶脉间失绿\",\"D. 茎秆软弱\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (141, 1, 'C4植物（如玉米）的光合效率高，其CO₂固定最初形成的产物是草酰乙酸（OAA），涉及的酶是PEP羧化酶。PEP羧化酶对CO₂的亲和力比Rubisco高，因此C4植物在高温、干旱条件下有优势。C4途径将CO₂在维管束鞘细胞中释放并进入卡尔文循环，该过程属于？', 0, '', '', 'A. 光反应\nB. C3途径\nC. C4循环\nD. CAM途径', 0, 0, 'B', 'C4途径中，维管束鞘细胞内的CO₂仍通过C3途径（卡尔文循环）被固定为PGA，C4途径只是CO₂浓缩机制。', 0, '[\"A. 光反应\",\"B. C3途径\",\"C. C4循环\",\"D. CAM途径\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (142, 1, '植物呼吸作用中，糖酵解产生的丙酮酸在无氧条件下可还原为乙醇或乳酸。在植物组织中，酒精发酵产生的乙醇对细胞可能产生毒害。长期淹水条件下，植物根系往往进行酒精发酵，导致？', 0, '', '', 'A. 能量产生增加\nB. 根系活力下降，甚至死亡\nC. 促进有氧呼吸\nD. 增加糖分积累', 0, 0, 'B', '长期淹水缺氧，植物进行酒精发酵，产生乙醇积累在根细胞中，毒害细胞，使根系活力下降，严重时死亡。', 0, '[\"A. 能量产生增加\",\"B. 根系活力下降，甚至死亡\",\"C. 促进有氧呼吸\",\"D. 增加糖分积累\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (143, 1, '植物激素中，脱落酸（ABA）在逆境响应中起重要作用。ABA能促进气孔关闭，其机制是促进保卫细胞中？', 0, '', '', 'A. K⁺内流\nB. K⁺外流\nC. Cl⁻内流\nD. 水进入', 0, 0, 'B', 'ABA促进保卫细胞中K⁺外流，渗透势升高，水分外流，气孔关闭，减少水分蒸腾。', 0, '[\"A. K⁺内流\",\"B. K⁺外流\",\"C. Cl⁻内流\",\"D. 水进入\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (144, 1, '光周期现象中，有些植物的开花由夜间长度（暗期）决定，而非日长。对于短日照植物，夜长中断（用红光处理）会？', 0, '', '', 'A. 促进开花\nB. 抑制开花\nC. 无影响\nD. 改变花型', 0, 0, 'B', '短日照植物需要连续长暗期才能开花，如果暗期被红光中断，则抑制开花。远红光照射可逆转这种效应，说明光敏色素参与。', 0, '[\"A. 促进开花\",\"B. 抑制开花\",\"C. 无影响\",\"D. 改变花型\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (145, 1, '盐碱胁迫下，植物不仅受离子毒害，还受渗透胁迫。植物合成脯氨酸等渗透调节物质，主要发生在细胞的？', 0, '', '', 'A. 细胞壁\nB. 细胞质\nC. 液泡\nD. 线粒体', 0, 0, 'B', '渗透调节物质（脯氨酸、甜菜碱等）主要积累在细胞质中，以降低细胞质渗透势，维持水分平衡。Na⁺等离子则被区隔在液泡中。', 0, '[\"A. 细胞壁\",\"B. 细胞质\",\"C. 液泡\",\"D. 线粒体\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (146, 1, '植物分类中，属名和种加词的写法有规范要求。以下哪个是符合双名法规范的写法？', 0, '', '', 'A. Oryza sativa L.\nB. Oryza Sativa L.\nC. oryza sativa\nD. Oryza sativa (L.)', 0, 0, 'A', '双名法中，属名首字母大写，种加词小写，整体斜体，后面可加命名人缩写（正体）。A选项符合规范。', 0, '[\"A. Oryza sativa L.\",\"B. Oryza Sativa L.\",\"C. oryza sativa\",\"D. Oryza sativa (L.)\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (147, 1, '藻类中，蓝藻（蓝细菌）虽然常被称为蓝绿藻，但属于原核生物。蓝藻的色素体中不含叶绿体，但含有叶绿素a和藻蓝素等。蓝藻的繁殖方式主要是？', 0, '', '', 'A. 有性生殖\nB. 无性分裂\nC. 孢子生殖\nD. 配子生殖', 0, 0, 'B', '蓝藻为原核生物，没有有性生殖，主要通过细胞分裂（二分裂）进行无性繁殖。', 0, '[\"A. 有性生殖\",\"B. 无性分裂\",\"C. 孢子生殖\",\"D. 配子生殖\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (148, 1, '苔藓植物的孢子体产生孢子，孢子萌发形成原丝体。在藓纲中，原丝体发育成配子枝，配子枝上产生颈卵器和精子器。苔藓的受精过程依赖水，这是因为精子具有鞭毛，需要游动到颈卵器。苔藓植物在生活史中，哪一个阶段是单倍体？', 0, '', '', 'A. 孢子体\nB. 配子体\nC. 合子\nD. 原丝体', 0, 0, 'B', '苔藓植物的配子体是单倍体，产生配子；孢子体是二倍体，产生孢子。原丝体是配子体发育的早期阶段，也是单倍体。合子是二倍体。但最直接问哪个阶段是单倍体，配子体是主要单倍体阶段。', 0, '[\"A. 孢子体\",\"B. 配子体\",\"C. 合子\",\"D. 原丝体\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (149, 1, '真蕨类的孢子囊通常聚集成孢子囊群，孢子囊具有环带结构，有助于孢子散布。环带是孢子囊壁上一列细胞壁不均匀加厚的结构，干燥时环带细胞收缩，导致孢子囊开裂，弹射孢子。环带的细胞壁加厚方式为？', 0, '', '', 'A. 全部均匀加厚\nB. 径向壁和内壁加厚\nC. 仅外壁加厚\nD. 无加厚', 0, 0, 'B', '环带细胞常在径向壁和内壁加厚，外壁薄，干燥时细胞收缩变形，导致孢子囊纵向开裂。', 0, '[\"A. 全部均匀加厚\",\"B. 径向壁和内壁加厚\",\"C. 仅外壁加厚\",\"D. 无加厚\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (150, 1, '裸子植物中，麻黄科（麻黄属）植物具有小叶和对生或轮生分枝，其茎呈绿色，能进行光合作用。麻黄科植物在干旱地区常见，其适应干旱的特征包括？', 0, '', '', 'A. 叶片大而薄\nB. 茎叶均肉质\nC. 叶片退化，茎进行光合作用\nD. 根浅', 0, 0, 'C', '麻黄科植物叶片退化呈鳞片状，茎绿色代替叶进行光合作用，减少水分蒸腾，适应干旱环境。', 0, '[\"A. 叶片大而薄\",\"B. 茎叶均肉质\",\"C. 叶片退化，茎进行光合作用\",\"D. 根浅\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (151, 1, 'APG分类系统中，单子叶植物是一个单系群，包括兰花、百合、禾本科等。单子叶植物的主要特征不包括？', 0, '', '', 'A. 胚具一枚子叶\nB. 叶脉平行\nC. 花基数3\nD. 维管束排列成环状', 0, 0, 'D', '单子叶植物茎的维管束散生，不是环状排列。胚具单子叶、平行脉、花基数3是典型特征。', 0, '[\"A. 胚具一枚子叶\",\"B. 叶脉平行\",\"C. 花基数3\",\"D. 维管束排列成环状\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (152, 1, '蔷薇科分为四个亚科：绣线菊亚科、蔷薇亚科、李亚科（桃亚科）和苹果亚科。其中，子房上位、花托凹陷形成杯状花筒、果实为核果的亚科是？', 0, '', '', 'A. 绣线菊亚科\nB. 蔷薇亚科\nC. 李亚科\nD. 苹果亚科', 0, 0, 'C', '李亚科（桃亚科）具有杯状花筒，子房上位，果实为核果（桃、李、杏）。蔷薇亚科果实为聚合瘦果，苹果亚科子房下位，果实为梨果。', 0, '[\"A. 绣线菊亚科\",\"B. 蔷薇亚科\",\"C. 李亚科\",\"D. 苹果亚科\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (153, 1, '种群增长模型中，当种群密度接近环境容纳量（K）时，种群增长率（r）会？', 0, '', '', 'A. 增大\nB. 减小\nC. 不变\nD. 先减后增', 0, 0, 'B', '逻辑斯蒂增长中，随着种群密度接近K值，有效增长率（r）逐渐减小，直至为0，种群数量达到稳定。', 0, '[\"A. 增大\",\"B. 减小\",\"C. 不变\",\"D. 先减后增\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (154, 1, '群落生态学中，生态位（niche）是指物种在群落中的地位和资源利用方式。两个物种的生态位重叠可能导致？', 0, '', '', 'A. 协同进化\nB. 竞争排斥\nC. 互利共生\nD. 寄生', 0, 0, 'B', '生态位重叠意味着两个物种需求相似资源，可能产生竞争。竞争排斥原理认为，完全相同的生态位不能长久共存，一个物种会取代另一个。', 0, '[\"A. 协同进化\",\"B. 竞争排斥\",\"C. 互利共生\",\"D. 寄生\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (155, 1, '生态系统中的碳循环是全球性的。人类活动（如燃烧化石燃料）增加了大气CO₂浓度，可能导致温室效应。植物在减缓温室效应中起关键作用，主要通过？', 0, '', '', 'A. 蒸腾作用\nB. 光合作用固定CO₂\nC. 呼吸作用\nD. 分解作用', 0, 0, 'B', '植物通过光合作用吸收CO₂，将其固定在有机物中，是陆地生态系统重要的碳汇，有助于减缓温室效应。', 0, '[\"A. 蒸腾作用\",\"B. 光合作用固定CO₂\",\"C. 呼吸作用\",\"D. 分解作用\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (156, 1, '植物与动物之间也存在互利共生，如传粉和种子散布。有些植物的果实颜色鲜艳、味道甜美，吸引动物取食并传播种子，这种适应属于？', 0, '', '', 'A. 拟态\nB. 化感作用\nC. 协同进化\nD. 寄生', 0, 0, 'C', '植物与传粉者、种子传播者之间的相互适应属于协同进化，两者相互塑造，提高繁殖成功率。', 0, '[\"A. 拟态\",\"B. 化感作用\",\"C. 协同进化\",\"D. 寄生\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (157, 1, '中国植物区系丰富，特有成分比例高。中国特有属中，银杏（Ginkgo biloba）是著名的孑遗植物。银杏在分类上属于？', 0, '', '', 'A. 被子植物\nB. 裸子植物\nC. 蕨类植物\nD. 苔藓植物', 0, 0, 'B', '银杏是裸子植物，属于银杏科，是中生代孑遗植物，种子裸露。', 0, '[\"A. 被子植物\",\"B. 裸子植物\",\"C. 蕨类植物\",\"D. 苔藓植物\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (158, 1, '热带雨林虽然面积仅占陆地面积的一小部分，但拥有全球半数的物种多样性。热带雨林物种丰富度高的原因不包括？', 0, '', '', 'A. 气候稳定，长期演化\nB. 空间异质性强\nC. 竞争压力小\nD. 传粉和散布机制多样', 0, 0, 'C', '热带雨林物种丰富度高，但竞争压力并不小，竞争反而促进了生态位分化。稳定气候、高异质性和多样化互惠关系是多样性高的原因。', 0, '[\"A. 气候稳定，长期演化\",\"B. 空间异质性强\",\"C. 竞争压力小\",\"D. 传粉和散布机制多样\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (159, 1, '分子系统学中，DNA序列比对是基础步骤。序列比对的主要目的是？', 0, '', '', 'A. 寻找基因功能\nB. 确定同源位点\nC. 分析蛋白质结构\nD. 测定基因组大小', 0, 0, 'B', '序列比对将不同物种的DNA序列排列，识别同源位点（来自共同祖先的位置），为后续进化距离计算和系统发育重建提供基础。', 0, '[\"A. 寻找基因功能\",\"B. 确定同源位点\",\"C. 分析蛋白质结构\",\"D. 测定基因组大小\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (160, 1, '从水生到陆生的演化过程中，植物逐步形成了角质层和气孔。角质层的作用是减少水分蒸发，但同时也妨碍气体交换，因此气孔的出现是重要适应。最早出现角质层和气孔的植物类群可能是？', 0, '', '', 'A. 绿藻\nB. 苔藓植物\nC. 蕨类植物\nD. 种子植物', 0, 0, 'B', '苔藓植物是最早登陆的陆生植物之一，具有角质层和气孔（或类似结构），但缺乏维管组织。角质层和气孔是适应陆生的重要创新。', 0, '[\"A. 绿藻\",\"B. 苔藓植物\",\"C. 蕨类植物\",\"D. 种子植物\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (161, 1, '裸子植物的种子比蕨类植物的孢子具有更强的生命力，主要是因为种子有胚和营养组织（胚乳），以及种皮保护。种子植物的配子体进一步简化，雌配子体在胚珠内发育，这有利于？', 0, '', '', 'A. 增加配子数量\nB. 保护配子体和受精后的胚胎\nC. 减少花粉传播\nD. 促进多胚现象', 0, 0, 'B', '种子植物配子体在胚珠（孢子体）内发育，受孢子体保护，受精后的胚胎也在种子中受保护，提高了后代存活率。', 0, '[\"A. 增加配子数量\",\"B. 保护配子体和受精后的胚胎\",\"C. 减少花粉传播\",\"D. 促进多胚现象\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (162, 1, '被子植物的花与果实是其在演化中的关键创新。花的出现促进了传粉效率，果实则帮助种子散布。被子植物还拥有双受精现象，即两个精细胞分别与卵细胞和中央细胞融合，形成胚和胚乳。胚乳是？', 0, '', '', 'A. 单倍体\nB. 二倍体\nC. 三倍体\nD. 四倍体', 0, 0, 'C', '双受精中，一个精细胞与卵细胞（n）融合形成合子（2n），另一个精细胞与两个极核（中央细胞，n+n）融合形成初生胚乳核（3n），故胚乳为三倍体。', 0, '[\"A. 单倍体\",\"B. 二倍体\",\"C. 三倍体\",\"D. 四倍体\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (163, 1, '系统发育树构建中，最大简约法（Maximum Parsimony）的原则是选择对数据变异解释所需假设最少的树。这种方法常应用于形态性状分析。最大简约法的缺点是？', 0, '', '', 'A. 计算速度慢\nB. 可能忽略长枝吸引等问题\nC. 不适用于DNA数据\nD. 需要先验模型', 0, 0, 'B', '最大简约法在长分支情况下可能存在长枝吸引（long branch attraction）误差，导致系统发育推断错误。贝叶斯或最大似然法可以更好地处理速率变异。', 0, '[\"A. 计算速度慢\",\"B. 可能忽略长枝吸引等问题\",\"C. 不适用于DNA数据\",\"D. 需要先验模型\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (164, 1, '多倍体在植物中很常见。多倍体形成通常是物种形成的重要机制，尤其在被子植物中。多倍体植物通常表现有杂种优势和较大器官。多倍体化的细胞学基础是？', 0, '', '', 'A. 减数分裂异常导致配子未减数\nB. 有丝分裂失败\nC. 染色体缺失\nD. 基因突变', 0, 0, 'A', '多倍体化通常由减数分裂异常（如纺锤体形成缺陷）导致2n配子产生，两个未减数配子结合或2n配子与n配子结合形成多倍体。', 0, '[\"A. 减数分裂异常导致配子未减数\",\"B. 有丝分裂失败\",\"C. 染色体缺失\",\"D. 基因突变\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (165, 1, '植物基因组学研究利用高通量测序技术。全基因组关联分析（GWAS）是解析复杂性状遗传基础的重要方法。GWAS的原理是将表型与分子标记（SNP）进行关联分析。GWAS的关键是？', 0, '', '', 'A. 需要大的自然群体\nB. 需要构建连锁群体\nC. 需要转基因技术\nD. 不能用于数量性状', 0, 0, 'A', 'GWAS利用自然群体的多样性，通过大群体中的分子标记与表型关联，寻找与性状相关的基因位点，不依赖人工杂交群体。', 0, '[\"A. 需要大的自然群体\",\"B. 需要构建连锁群体\",\"C. 需要转基因技术\",\"D. 不能用于数量性状\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (166, 1, '植物基因编辑技术CRISPR/Cas9可以敲除基因或引入点突变。该技术对作物育种的意义在于？', 0, '', '', 'A. 不能用于改良目标性状\nB. 可以精确改良性状，缩短育种年限\nC. 只能用于模式植物\nD. 属于传统诱变育种', 0, 0, 'B', 'CRISPR/Cas9技术可以精准修饰目标基因，实现定向改良，明显缩短育种周期，并且可以避免传统育种的连锁累赘问题。', 0, '[\"A. 不能用于改良目标性状\",\"B. 可以精确改良性状，缩短育种年限\",\"C. 只能用于模式植物\",\"D. 属于传统诱变育种\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (167, 1, '在植物育种中，种质资源是育种的基础。种质资源不仅包括地方品种和野生种，还包括遗传材料和近缘种。种质资源收集和保存的首要目的是？', 0, '', '', 'A. 提供观赏材料\nB. 保护遗传多样性，为育种提供基因源\nC. 增加作物产量\nD. 扩大种植面积', 0, 0, 'B', '种质资源保存的目的是保护植物遗传多样性，为当前和未来的育种工作提供抗逆、优质等基因来源。', 0, '[\"A. 提供观赏材料\",\"B. 保护遗传多样性，为育种提供基因源\",\"C. 增加作物产量\",\"D. 扩大种植面积\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (168, 1, '植物细胞中，细胞质基质内含有多种细胞器。下列哪种细胞器在植物细胞中主要负责蛋白质的加工和分类包装？', 0, '', '', 'A. 核糖体\nB. 内质网\nC. 高尔基体\nD. 溶酶体', 0, 0, 'C', '高尔基体在植物细胞中负责对蛋白质进行加工、分类和包装，并参与细胞壁多糖的合成。核糖体是蛋白质合成场所，内质网负责初步加工和脂质合成。', 0, '[\"A. 核糖体\",\"B. 内质网\",\"C. 高尔基体\",\"D. 溶酶体\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (169, 1, '植物细胞中的胞间连丝是细胞间物质运输和信息交流的通道。胞间连丝穿越的结构是？', 0, '', '', 'A. 细胞壁和细胞膜\nB. 仅细胞壁\nC. 仅细胞膜\nD. 液泡膜', 0, 0, 'A', '胞间连丝是穿越相邻细胞细胞壁和细胞膜的通道，使细胞质相连，允许小分子和RNA等物质在细胞间运输。', 0, '[\"A. 细胞壁和细胞膜\",\"B. 仅细胞壁\",\"C. 仅细胞膜\",\"D. 液泡膜\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (170, 1, '植物体内，具有支持作用且细胞壁通常在角隅处加厚的组织是？', 0, '', '', 'A. 厚壁组织\nB. 厚角组织\nC. 薄壁组织\nD. 分生组织', 0, 0, 'B', '厚角组织的细胞壁在角隅处增厚，具有弹性支持作用，常见于幼茎和叶柄中。厚壁组织细胞壁均匀增厚且多为死细胞。', 0, '[\"A. 厚壁组织\",\"B. 厚角组织\",\"C. 薄壁组织\",\"D. 分生组织\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (171, 1, '植物体的输导组织包括导管和筛管。导管的细胞在成熟后失去原生质体，形成中空的管道。导管运输的主要物质是？', 0, '', '', 'A. 有机物\nB. 水和无机盐\nC. 氨基酸\nD. 植物激素', 0, 0, 'B', '导管运输水分和无机盐，方向向上。筛管运输有机物（如蔗糖），方向从源到库。', 0, '[\"A. 有机物\",\"B. 水和无机盐\",\"C. 氨基酸\",\"D. 植物激素\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (172, 1, '根尖从顶端起依次分为根冠、分生区、伸长区和成熟区。其中，根冠的作用除了保护分生组织外，还能分泌粘液，起什么作用？', 0, '', '', 'A. 促进细胞分裂\nB. 润滑土壤，利于根生长\nC. 吸收水分\nD. 合成激素', 0, 0, 'B', '根冠分泌粘液润滑根尖，减少摩擦，帮助根系在土壤中推进，并可能参与根际微生物互作。', 0, '[\"A. 促进细胞分裂\",\"B. 润滑土壤，利于根生长\",\"C. 吸收水分\",\"D. 合成激素\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (173, 1, '在根的初生结构中，中柱鞘细胞具有分生潜力。侧根起源于中柱鞘的哪一部分？', 0, '', '', 'A. 正对木质部的位置\nB. 正对韧皮部的位置\nC. 正对皮层的位置\nD. 任意位置', 0, 0, 'A', '双子叶植物侧根通常起源于中柱鞘细胞中正对木质部的位置（或木质部脊之间），穿过内皮层和皮层向外生长。', 0, '[\"A. 正对木质部的位置\",\"B. 正对韧皮部的位置\",\"C. 正对皮层的位置\",\"D. 任意位置\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (174, 1, '茎的初生结构中，维管束包括初生木质部和初生韧皮部。在双子叶植物中，初生木质部的分化方向是？', 0, '', '', 'A. 从外向内（外始式）\nB. 从内向外（内始式）\nC. 从中心向四周\nD. 没有方向', 0, 0, 'B', '双子叶植物茎的初生木质部分化方向为内始式，即原生木质部靠近中心，后生木质部向外发育。根的木质部为外始式。', 0, '[\"A. 从外向内（外始式）\",\"B. 从内向外（内始式）\",\"C. 从中心向四周\",\"D. 没有方向\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (175, 1, '木本植物茎的次生生长中，木栓形成层属于侧生分生组织，它起源于？', 0, '', '', 'A. 表皮\nB. 皮层或韧皮部\nC. 形成层\nD. 髓', 0, 0, 'B', '木栓形成层通常起源于皮层细胞，有时也起源于韧皮部薄壁细胞。它向外产生木栓层，向内产生栓内层，形成周皮。', 0, '[\"A. 表皮\",\"B. 皮层或韧皮部\",\"C. 形成层\",\"D. 髓\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (176, 1, '叶片中的栅栏组织细胞排列紧密，含较多叶绿体，主要功能是进行光合作用。在阴生植物中，栅栏组织通常？', 0, '', '', 'A. 更发达\nB. 不发达或只有一层\nC. 不含叶绿体\nD. 排列疏松', 0, 0, 'B', '阴生植物叶片较薄，栅栏组织不发达（常为一层），以增加单位面积的光能捕获效率。阳生植物栅栏组织发达，多层细胞。', 0, '[\"A. 更发达\",\"B. 不发达或只有一层\",\"C. 不含叶绿体\",\"D. 排列疏松\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (177, 1, '气孔的开闭受保卫细胞中K⁺浓度调控，同时也受其他信号分子影响。脱落酸（ABA）促进气孔关闭，其信号转导中涉及？', 0, '', '', 'A. Ca²⁺浓度升高\nB. 活性氧清除\nC. 细胞壁酸化\nD. 赤霉素合成', 0, 0, 'A', 'ABA信号途径中，ABA与其受体结合，使保卫细胞中Ca²⁺浓度升高，进而激活阴离子通道，使K⁺外流，气孔关闭。', 0, '[\"A. Ca²⁺浓度升高\",\"B. 活性氧清除\",\"C. 细胞壁酸化\",\"D. 赤霉素合成\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (178, 1, '花的着生方式中，花序分为无限花序和有限花序。无限花序的特点是花轴顶端继续生长，花从下向上（或从外向内）依次开放。下列哪种花序属于无限花序？', 0, '', '', 'A. 聚伞花序\nB. 总状花序\nC. 头状花序\nD. 伞形花序', 0, 0, 'B', '总状花序是典型的无限花序，花轴伸长，花柄等长，由下向上开放。聚伞花序是有限花序。头状花序是总状花序的特殊类型，但花轴短缩成盘状；伞形花序也是无限花序，总状花序更典型。', 0, '[\"A. 聚伞花序\",\"B. 总状花序\",\"C. 头状花序\",\"D. 伞形花序\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (179, 1, '在被子植物的花中，子房位置可分为上位子房、半下位子房和下位子房。当子房着生在花托的凹陷内，花萼和花冠着生在子房上方时，称为？', 0, '', '', 'A. 上位子房\nB. 半下位子房\nC. 下位子房\nD. 周位花', 0, 0, 'C', '下位子房（子房下位）是指子房位于花托凹陷内，花萼和花冠着生于子房顶端，如苹果、梨。上位子房花萼花冠生于子房下方。', 0, '[\"A. 上位子房\",\"B. 半下位子房\",\"C. 下位子房\",\"D. 周位花\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (180, 1, '有些植物的种子在成熟后，珠心组织未被完全吸收，形成外胚乳。外胚乳属于哪类组织？', 0, '', '', 'A. 胚乳\nB. 珠心残余\nC. 珠被\nD. 胚柄', 0, 0, 'B', '外胚乳是由珠心组织（母体组织）发育而来的储藏组织，区别于由受精极核发育的胚乳。常见于某些甜菜、胡椒等。', 0, '[\"A. 胚乳\",\"B. 珠心残余\",\"C. 珠被\",\"D. 胚柄\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (181, 1, '干果中的裂果和闭果是常见的两种类型。闭果的果皮在成熟时不开裂，如瘦果、坚果、颖果等。下列哪种属于闭果？', 0, '', '', 'A. 荚果\nB. 角果\nC. 瘦果\nD. 蒴果', 0, 0, 'C', '瘦果是闭果，果皮不开裂，内含一粒种子，如向日葵。荚果、角果、蒴果为裂果。', 0, '[\"A. 荚果\",\"B. 角果\",\"C. 瘦果\",\"D. 蒴果\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (182, 1, '水分从土壤进入根毛细胞，依次经过的途径是：根毛细胞→皮层→内皮层→中柱。其中，水分在皮层中主要通过什么途径运输？', 0, '', '', 'A. 质外体途径\nB. 共质体途径\nC. 跨细胞途径\nD. 质外体和共质体结合', 0, 0, 'A', '在根的皮层中，水分主要通过质外体途径（细胞壁和细胞间隙）横向运输，直到遇到内皮层的凯氏带，被阻挡后转为共质体途径。', 0, '[\"A. 质外体途径\",\"B. 共质体途径\",\"C. 跨细胞途径\",\"D. 质外体和共质体结合\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (183, 1, '蒸腾拉力是水分运输的主要动力。当叶片气孔开放，蒸腾作用加速，叶片细胞的水势降低，从导管中吸水。这种吸水产生拉力，使导管中的水柱处于什么状态？', 0, '', '', 'A. 正压\nB. 负压（张力）\nC. 无压力\nD. 交替变化', 0, 0, 'B', '蒸腾拉力使导管水柱产生负压（张力），这种拉力通过水分子间的内聚力传递，是水分向上运输的主要机制。', 0, '[\"A. 正压\",\"B. 负压（张力）\",\"C. 无压力\",\"D. 交替变化\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (184, 1, '植物矿质营养中，钙（Ca）是细胞壁果胶质的重要成分，钙缺乏通常导致什么症状？', 0, '', '', 'A. 生长点坏死\nB. 老叶黄化\nC. 叶片紫红色\nD. 茎秆柔软', 0, 0, 'A', '钙在植物体内不易移动，缺钙时生长点（根尖、茎尖）首先受害，出现坏死、卷曲。老叶黄化多为缺氮，紫红叶为缺磷，茎秆软为缺钾。', 0, '[\"A. 生长点坏死\",\"B. 老叶黄化\",\"C. 叶片紫红色\",\"D. 茎秆柔软\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (185, 1, '植物对矿质离子的吸收具有选择性，这主要与细胞膜上的哪种结构有关？', 0, '', '', 'A. 磷脂双分子层\nB. 转运蛋白（载体和通道）\nC. 细胞壁\nD. 液泡膜', 0, 0, 'B', '细胞膜上的转运蛋白具有特异性，不同离子通过不同载体或通道进行转运，从而实现选择性吸收。', 0, '[\"A. 磷脂双分子层\",\"B. 转运蛋白（载体和通道）\",\"C. 细胞壁\",\"D. 液泡膜\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (186, 1, '光合作用中，光反应产生的NADPH（还原型辅酶Ⅱ）在暗反应中用于什么反应？', 0, '', '', 'A. CO₂的固定\nB. 3-磷酸甘油酸的还原\nC. RuBP的再生\nD. 淀粉的合成', 0, 0, 'B', 'NADPH在暗反应中用于将3-磷酸甘油酸（PGA）还原为甘油醛-3-磷酸（G3P），这是碳同化步骤。CO₂固定由Rubisco催化，不需要NADPH。', 0, '[\"A. CO₂的固定\",\"B. 3-磷酸甘油酸的还原\",\"C. RuBP的再生\",\"D. 淀粉的合成\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (187, 1, 'CAM植物的特点是夜间气孔开放吸收CO₂，以苹果酸形式储存，白天气孔关闭，利用苹果酸脱羧释放的CO₂进行光合作用。这种模式适应的主要环境是？', 0, '', '', 'A. 高温高湿\nB. 干旱缺水\nC. 低温阴雨\nD. 强光弱风', 0, 0, 'B', 'CAM途径通过夜间固定CO₂，白天关闭气孔减少水分蒸腾，是对干旱环境的高度适应。常见于景天、仙人掌等。', 0, '[\"A. 高温高湿\",\"B. 干旱缺水\",\"C. 低温阴雨\",\"D. 强光弱风\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (188, 1, '在呼吸作用中，葡萄糖被彻底氧化分解成CO₂和H₂O。糖酵解、三羧酸循环和电子传递链三个阶段的ATP生成比例中，哪个阶段产生的ATP最多？', 0, '', '', 'A. 糖酵解\nB. 三羧酸循环\nC. 电子传递链（氧化磷酸化）\nD. 三者相同', 0, 0, 'C', '电子传递链和氧化磷酸化产生的ATP最多（约28-34个ATP），糖酵解产2个ATP，TCA循环产2个GTP（约2个ATP）。', 0, '[\"A. 糖酵解\",\"B. 三羧酸循环\",\"C. 电子传递链（氧化磷酸化）\",\"D. 三者相同\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (189, 1, '植物在无氧条件下进行发酵，在酒精发酵中，丙酮酸脱羧产生的乙醛随后被还原成乙醇。该还原过程所需的氢来自？', 0, '', '', 'A. NADH\nB. NADPH\nC. FADH₂\nD. ATP', 0, 0, 'A', '酒精发酵中，乙醛在乙醇脱氢酶催化下被NADH还原为乙醇，同时NADH氧化为NAD⁺，维持糖酵解所需NAD⁺的再生。', 0, '[\"A. NADH\",\"B. NADPH\",\"C. FADH₂\",\"D. ATP\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (190, 1, '植物激素中，细胞分裂素（CTK）主要在根尖合成，通过木质部向上运输。细胞分裂素的生理效应不包括？', 0, '', '', 'A. 促进细胞分裂\nB. 延缓叶片衰老\nC. 促进侧芽生长\nD. 促进果实成熟', 0, 0, 'D', '促进果实成熟是乙烯的作用。细胞分裂素促进细胞分裂、延缓叶片衰老、促进侧芽生长（与生长素拮抗）。', 0, '[\"A. 促进细胞分裂\",\"B. 延缓叶片衰老\",\"C. 促进侧芽生长\",\"D. 促进果实成熟\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (191, 1, '生长素在植物体内具有极性运输的特性，即从形态学上端运输到形态学下端。这种运输方式属于？', 0, '', '', 'A. 被动运输\nB. 主动运输\nC. 自由扩散\nD. 胞间连丝运输', 0, 0, 'B', '生长素极性运输是逆浓度梯度的主动运输过程，需要载体和能量，方向由PIN蛋白决定。', 0, '[\"A. 被动运输\",\"B. 主动运输\",\"C. 自由扩散\",\"D. 胞间连丝运输\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (192, 1, '在光周期调控开花中，植物叶片感知光周期信号后产生开花素（成花素），并通过韧皮部运输到茎顶端，诱导开花。目前认为开花素的化学本质是？', 0, '', '', 'A. 蛋白质\nB. 核酸\nC. 小分子代谢物\nD. 脂类', 0, 0, 'A', '成花素（Florigen）被鉴定为一种蛋白质，在拟南芥中为FT蛋白，在叶片合成后经韧皮部运输至茎尖分生组织。', 0, '[\"A. 蛋白质\",\"B. 核酸\",\"C. 小分子代谢物\",\"D. 脂类\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (193, 1, '植物在适应低温过程中，会积累可溶性糖、脯氨酸等物质，这些物质的作用是？', 0, '', '', 'A. 增加细胞质浓度，降低冰点\nB. 增加膜流动性\nC. 促进冰晶形成\nD. 减少水分吸收', 0, 0, 'A', '低温驯化中积累的可溶性糖和脯氨酸等作为渗透调节物质，增加细胞质浓度，降低冰点，保护细胞免受冰冻伤害。', 0, '[\"A. 增加细胞质浓度，降低冰点\",\"B. 增加膜流动性\",\"C. 促进冰晶形成\",\"D. 减少水分吸收\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (194, 1, '植物对高温的适应机制包括合成热激蛋白（HSP）、改变膜脂成分等。HSP的作用是帮助变性蛋白质重新折叠，这一过程需要？', 0, '', '', 'A. 水解ATP\nB. 合成ATP\nC. 氧化还原\nD. 光能', 0, 0, 'A', '热激蛋白作为分子伴侣，其蛋白折叠功能需要ATP水解提供能量。', 0, '[\"A. 水解ATP\",\"B. 合成ATP\",\"C. 氧化还原\",\"D. 光能\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (195, 1, '双名法中，种加词经常是形容词或名词，通常表示形态、产地或用途等。在“Oryza sativa L.”中，种加词“sativa”的意思是？', 0, '', '', 'A. 野生的\nB. 栽培的\nC. 大型的\nD. 芳香的', 0, 0, 'B', 'sativa是拉丁文形容词，意为“栽培的”，常用于栽培种，如Oryza sativa（水稻）。', 0, '[\"A. 野生的\",\"B. 栽培的\",\"C. 大型的\",\"D. 芳香的\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (196, 1, '绿藻门中，有些种类具有鞭毛，能在水中游动，如衣藻。衣藻的繁殖方式包括无性生殖和？', 0, '', '', 'A. 有性生殖（同配生殖）\nB. 孢子生殖\nC. 断裂生殖\nD. 出芽生殖', 0, 0, 'A', '衣藻有无性生殖（产生游动孢子）和有性生殖（同配生殖或异配生殖）。绿藻多数具有有性生殖。', 0, '[\"A. 有性生殖（同配生殖）\",\"B. 孢子生殖\",\"C. 断裂生殖\",\"D. 出芽生殖\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (197, 1, '红藻门植物中，藻红蛋白是其特有的辅助色素，使藻体呈红色。红藻的储存物是红藻淀粉，这种物质遇碘显什么颜色？', 0, '', '', 'A. 蓝色\nB. 紫色\nC. 红棕色\nD. 黄色', 0, 0, 'C', '红藻淀粉遇碘呈红棕色或红紫色，不同于淀粉遇碘显蓝色。这是鉴别红藻的一个化学特征。', 0, '[\"A. 蓝色\",\"B. 紫色\",\"C. 红棕色\",\"D. 黄色\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (198, 1, '藓纲（如葫芦藓）的孢子体由孢蒴、蒴柄和基足组成。孢蒴成熟后，通过环带和蒴齿协助释放孢子。藓纲的配子体在形态上具有？', 0, '', '', 'A. 背腹分化的叶状体\nB. 直立茎和螺旋状着生的小叶\nC. 无茎叶分化\nD. 角状孢子体', 0, 0, 'B', '藓纲配子体为茎叶体，有直立茎和螺旋状着生的小叶，具假根。苔纲多背腹分化的叶状体。', 0, '[\"A. 背腹分化的叶状体\",\"B. 直立茎和螺旋状着生的小叶\",\"C. 无茎叶分化\",\"D. 角状孢子体\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (199, 1, '角苔纲的配子体为叶状体，孢子体为角状，具有居间分生组织。角苔的孢子体绿色，能进行光合作用，这与苔纲和藓纲不同。角苔的叶状体上通常有？', 0, '', '', 'A. 气孔\nB. 假根\nC. 维管束\nD. 种子', 0, 0, 'B', '角苔配子体叶状体背面有气孔，腹面有假根。与藓纲不同，角苔假根为单细胞。', 0, '[\"A. 气孔\",\"B. 假根\",\"C. 维管束\",\"D. 种子\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (200, 1, '木贼类（Equisetum）的茎具有明显的节和节间，节上有轮生的小叶，茎表面有硅质沉积，因此粗糙。木贼类的孢子叶球着生在茎顶，孢子囊内孢子分化出弹丝，弹丝的作用是？', 0, '', '', 'A. 帮助传播\nB. 吸收水分\nC. 提供营养\nD. 保护孢子', 0, 0, 'A', '木贼的孢子具有弹丝，弹丝在潮湿时卷曲，干燥时伸展，协助孢子的散布。', 0, '[\"A. 帮助传播\",\"B. 吸收水分\",\"C. 提供营养\",\"D. 保护孢子\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (201, 1, '真蕨类中，许多种类具有根状茎，叶通常为大型叶（复叶），幼叶拳卷。真蕨类植物的孢子体发达，配子体为原叶体。真蕨类与石松类的关键区别之一在于？', 0, '', '', 'A. 是否具有维管束\nB. 叶的大小和叶脉类型\nC. 是否具有根\nD. 孢子体是否占优势', 0, 0, 'B', '真蕨类为大型叶（具叶柄和叶脉），石松类为小型叶（单脉）。两者均有维管束、根和孢子体占优势。', 0, '[\"A. 是否具有维管束\",\"B. 叶的大小和叶脉类型\",\"C. 是否具有根\",\"D. 孢子体是否占优势\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (202, 1, '裸子植物中，松科（如马尾松）的花粉具有两个气囊，这有助于风媒传粉。松树的雌球果成熟时，珠鳞张开释放种子。松树的种子具有翅，有利于？', 0, '', '', 'A. 动物传播\nB. 风力传播\nC. 水力传播\nD. 自体传播', 0, 0, 'B', '松树种子具翅，成熟后从张开珠鳞脱落，借风力传播。这是裸子植物风媒散布的典型方式。', 0, '[\"A. 动物传播\",\"B. 风力传播\",\"C. 水力传播\",\"D. 自体传播\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (203, 1, '苏铁科的植物具有粗壮的树干和大型羽状复叶。苏铁的生殖结构为大孢子叶和小孢子叶，分别着生胚珠和花粉囊。苏铁的精子具有鞭毛，能游动，这反映了？', 0, '', '', 'A. 对干燥环境的适应\nB. 与蕨类相似的原始特征\nC. 高度进化\nD. 专性自花传粉', 0, 0, 'B', '苏铁精子具鞭毛，需水环境受精，这是裸子植物中的原始特征，保留了与蕨类相似的特点。', 0, '[\"A. 对干燥环境的适应\",\"B. 与蕨类相似的原始特征\",\"C. 高度进化\",\"D. 专性自花传粉\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (204, 1, 'APG分类系统中，被子植物的科和属的界定多采用分子数据。APG系统与传统的恩格勒系统（Engler系统）在分类依据上有何不同？', 0, '', '', 'A. 恩格勒系统基于形态，APG基于分子和形态\nB. 两者完全一致\nC. APG不承认单子叶植物\nD. APG基于地理分布', 0, 0, 'A', 'APG系统主要依据分子系统学数据，结合形态学，强调单系群。恩格勒系统主要依据形态特征和生态习性。', 0, '[\"A. 恩格勒系统基于形态，APG基于分子和形态\",\"B. 两者完全一致\",\"C. APG不承认单子叶植物\",\"D. APG基于地理分布\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (205, 1, 'APG系统中，菊类（Asterids）是一个大的分支，包括菊科、茄科、唇形科等。菊科头状花序中，花序托上密集的小花称为？', 0, '', '', 'A. 花盘\nB. 小花\nC. 管状花\nD. 舌状花', 0, 0, 'B', '头状花序中密集着生的小花（floret）可分为舌状花和管状花。所有小花共同着生在花序托上。', 0, '[\"A. 花盘\",\"B. 小花\",\"C. 管状花\",\"D. 舌状花\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (206, 1, '豆科根据花的形态和雄蕊特征可分为三个亚科：含羞草亚科、苏木亚科和蝶形花亚科。蝶形花亚科的花冠为蝶形，雄蕊多为二体雄蕊。下列哪种属于蝶形花亚科？', 0, '', '', 'A. 合欢\nB. 皂荚\nC. 豌豆\nD. 含羞草', 0, 0, 'C', '豌豆是典型的蝶形花亚科，具有蝶形花冠和二体雄蕊。合欢和含羞草属于含羞草亚科，皂荚属于苏木亚科。', 0, '[\"A. 合欢\",\"B. 皂荚\",\"C. 豌豆\",\"D. 含羞草\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (207, 1, '菊科是双子叶植物中最大的科，其特点之一是花萼退化为冠毛或鳞片。冠毛在果实成熟后有助于种子散布。菊科的果实类型为瘦果，瘦果顶端通常具有冠毛，这利于？', 0, '', '', 'A. 风力传播\nB. 动物传播\nC. 水力传播\nD. 自体传播', 0, 0, 'A', '菊科瘦果顶端的冠毛（如蒲公英）可以增加空气浮力，协助风力传播。', 0, '[\"A. 风力传播\",\"B. 动物传播\",\"C. 水力传播\",\"D. 自体传播\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (208, 1, '禾本科（Poaceae）的花序为小穗，小穗由颖片和若干小花组成。小花外稃和内稃包裹浆片、雄蕊和雌蕊。禾本科的授粉方式主要是？', 0, '', '', 'A. 虫媒\nB. 风媒\nC. 鸟媒\nD. 水媒', 0, 0, 'B', '禾本科植物多为风媒传粉，花小、无蜜腺、花药丁字形着生、花粉量大，适应风媒。', 0, '[\"A. 虫媒\",\"B. 风媒\",\"C. 鸟媒\",\"D. 水媒\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (209, 1, '种群的空间分布格局中，均匀分布较少见，通常出现在种内竞争激烈的环境，如某些沙漠植物。均匀分布的主要形成机制是？', 0, '', '', 'A. 资源随机分布\nB. 个体间相互排斥\nC. 种子聚集散布\nD. 环境条件均一', 0, 0, 'B', '均匀分布通常由个体间的强烈竞争或相斥作用（如根系争夺水分）导致，使个体间隔较均匀。', 0, '[\"A. 资源随机分布\",\"B. 个体间相互排斥\",\"C. 种子聚集散布\",\"D. 环境条件均一\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (210, 1, '在生态学中，阿尔利效应（Allee effect）是指种群密度过低时，个体适合度下降的现象，这可能导致种群灭绝。Allee效应的可能原因是？', 0, '', '', 'A. 捕食压力减小\nB. 交配困难或合作行为减少\nC. 资源更充足\nD. 竞争减弱', 0, 0, 'B', 'Allee效应中，低密度种群可能面临交配对象难找、群体防御或合作行为减弱等困难，导致适合度下降。', 0, '[\"A. 捕食压力减小\",\"B. 交配困难或合作行为减少\",\"C. 资源更充足\",\"D. 竞争减弱\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (211, 1, '群落生态学中，物种多样性指数（如Shannon-Wiener指数）综合反映了物种丰富度和均匀度。如果一个群落中物种丰富度很高，但均匀度很低（少数物种占多数），多样性指数会？', 0, '', '', 'A. 很高\nB. 较低\nC. 不受影响\nD. 等于丰富度', 0, 0, 'B', 'Shannon-Wiener指数综合考虑丰富度和均匀度，均匀度低时多样性指数会降低，即使物种数较多。', 0, '[\"A. 很高\",\"B. 较低\",\"C. 不受影响\",\"D. 等于丰富度\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (212, 1, '群落演替的顶极概念中，单元顶极理论认为一个气候区内只有一个顶级群落（气候顶极），而多元顶极理论认为存在多个顶极。单元顶极理论的主要支持者是？', 0, '', '', 'A. 达尔文\nB. 林德曼\nC. 克莱门茨\nD. 坦斯利', 0, 0, 'C', '美国生态学家克莱门茨提出了单元顶极理论，认为演替最终到达与气候相适应的稳定顶极。坦斯利提出了多元顶极理论。', 0, '[\"A. 达尔文\",\"B. 林德曼\",\"C. 克莱门茨\",\"D. 坦斯利\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (213, 1, '生态系统中，能量流动沿食物链传递，能量损失的主要方式是？', 0, '', '', 'A. 未被摄食\nB. 呼吸散失\nC. 排泄物\nD. 以上都是', 0, 0, 'D', '能量在营养级间传递时，大部分能量用于呼吸消耗，部分以排泄物、热散失，极少部分同化并传递。', 0, '[\"A. 未被摄食\",\"B. 呼吸散失\",\"C. 排泄物\",\"D. 以上都是\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (214, 1, '氮循环中，植物不能直接利用大气中的N₂。固氮微生物将N₂转化为铵态氮（NH₄⁺），硝化作用将NH₄⁺转化为硝态氮（NO₃⁻），反硝化作用则将NO₃⁻还原为N₂释放回大气。反硝化作用主要发生在什么条件下？', 0, '', '', 'A. 好氧条件\nB. 厌氧条件\nC. 高温条件\nD. 酸性土壤', 0, 0, 'B', '反硝化作用由厌氧微生物（如假单胞菌）在缺氧条件下进行，将硝酸盐还原为N₂，完成氮循环。', 0, '[\"A. 好氧条件\",\"B. 厌氧条件\",\"C. 高温条件\",\"D. 酸性土壤\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (215, 1, '化感作用在植物群落中具有生态学意义。有些植物释放的化学物质能抑制其他植物的种子萌发，这种现象在什么类型的群落中较常见？', 0, '', '', 'A. 热带雨林\nB. 荒漠灌丛和温带森林\nC. 水生群落\nD. 农田生态系统', 0, 0, 'B', '化感作用在荒漠灌丛（如桉树、灌木蒿）和某些温带森林（如核桃、黑胡桃）中常见，可减少种间竞争。', 0, '[\"A. 热带雨林\",\"B. 荒漠灌丛和温带森林\",\"C. 水生群落\",\"D. 农田生态系统\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (216, 1, '地衣是藻类和真菌的共生体，真菌提供保护结构和水分，藻类提供光合产物。这种共生关系属于？', 0, '', '', 'A. 寄生\nB. 互利共生\nC. 偏利共生\nD. 化感作用', 0, 0, 'B', '地衣中真菌和藻类相互受益，真菌获得有机营养，藻类获得水分和保护，是典型的互利共生。', 0, '[\"A. 寄生\",\"B. 互利共生\",\"C. 偏利共生\",\"D. 化感作用\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (217, 1, '植物区系分区中，中国植物区系属于泛北极植物区，但南部属于古热带植物区。中国-日本森林植物亚区是东亚植物区的重要部分，其特点是？', 0, '', '', 'A. 物种贫乏\nB. 特有成分丰富\nC. 全是常绿树种\nD. 无珍稀植物', 0, 0, 'B', '中国-日本亚区是东亚植物区的重要组成部分，特有属和特有科丰富，如银杏、水杉等。', 0, '[\"A. 物种贫乏\",\"B. 特有成分丰富\",\"C. 全是常绿树种\",\"D. 无珍稀植物\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (218, 1, '高山植物通常具有矮小、匍匐生长、叶革质或具绒毛等形态特征，这些适应主要是为了应对什么环境压力？', 0, '', '', 'A. 强风、低温和强辐射\nB. 水分过多\nC. 高氧浓度\nD. 高pH土壤', 0, 0, 'A', '高山环境强风、低温、紫外线强，植物矮小匍匐可减少风损和保温，绒毛和厚角质层减少蒸腾和辐射伤害。', 0, '[\"A. 强风、低温和强辐射\",\"B. 水分过多\",\"C. 高氧浓度\",\"D. 高pH土壤\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (219, 1, '荒漠植被中，植物以旱生灌木和肉质植物为主，根系深广，叶退化或为刺。荒漠植物的主要水分来源是？', 0, '', '', 'A. 降水\nB. 深层地下水\nC. 露水\nD. 大气水', 0, 0, 'B', '荒漠植物多具深根系，可获取深层地下水；同时叶片退化、角质层厚以减少蒸腾。降水稀少，深层地下水是主要水源。', 0, '[\"A. 降水\",\"B. 深层地下水\",\"C. 露水\",\"D. 大气水\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (220, 1, '化石研究表明，最早的陆生植物出现在奥陶纪。这些早期陆生植物没有维管系统，形态简单，类似于今天的？', 0, '', '', 'A. 苔藓植物\nB. 蕨类植物\nC. 裸子植物\nD. 被子植物', 0, 0, 'A', '早期陆生植物（如顶囊蕨）结构简单，但最古老的完全陆生化石类似苔藓植物。苔藓是现存最古老的非维管陆生植物类群。', 0, '[\"A. 苔藓植物\",\"B. 蕨类植物\",\"C. 裸子植物\",\"D. 被子植物\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (221, 1, '分子系统学中，分子钟理论基于DNA序列的进化速率相对恒定，可用于推断类群分化时间。分子钟的应用需要？', 0, '', '', 'A. 化石标定\nB. 形态数据\nC. 地理分布\nD. 生态位模型', 0, 0, 'A', '分子钟分析需要利用化石作为校准点，将遗传距离转化为绝对时间，估算分歧时间。', 0, '[\"A. 化石标定\",\"B. 形态数据\",\"C. 地理分布\",\"D. 生态位模型\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (222, 1, '维管系统的形成包括木质部和韧皮部的分化，这使植物能长高和实现长距离运输。维管植物中，木质部的主要功能是运输水分和矿质，韧皮部运输有机物。最早具有真正维管系统的植物化石属于？', 0, '', '', 'A. 苔藓\nB. 莱尼蕨类（Rhyniophytes）\nC. 种子蕨\nD. 松柏类', 0, 0, 'B', '莱尼蕨类（如莱尼蕨）是已知最早的维管植物化石，出现于志留纪晚期，具有简单的维管系统。', 0, '[\"A. 苔藓\",\"B. 莱尼蕨类（Rhyniophytes）\",\"C. 种子蕨\",\"D. 松柏类\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (223, 1, '种子植物的演化中，大孢子（雌配子体）在胚珠内发育，不需要水环境，这是对陆生环境的重要适应。胚珠的结构包括珠被、珠心和珠孔。珠被在种子形成时发育为？', 0, '', '', 'A. 种皮\nB. 胚乳\nC. 果皮\nD. 胚', 0, 0, 'A', '珠被在受精后发育为种皮，保护胚。胚乳由受精极核发育，胚由合子发育。裸子植物种子无果皮。', 0, '[\"A. 种皮\",\"B. 胚乳\",\"C. 果皮\",\"D. 胚\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (224, 1, '被子植物中，花的演化趋势之一是从两性花到单性花，从虫媒到风媒。风媒花通常具有哪项特征？', 0, '', '', 'A. 花色鲜艳\nB. 花蜜丰富\nC. 花小、无香气\nD. 花大而美丽', 0, 0, 'C', '风媒花通常花小、无香气、无蜜腺，花粉量大且轻，利于风传。鲜艳花色和花蜜是虫媒花的特征。', 0, '[\"A. 花色鲜艳\",\"B. 花蜜丰富\",\"C. 花小、无香气\",\"D. 花大而美丽\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (225, 1, '被子植物在晚白垩世至新生代发生快速辐射演化，取代裸子植物成为陆地植被优势类群。其成功的关键创新包括花、果实和？', 0, '', '', 'A. 双受精和胚乳\nB. 维管系统\nC. 种子\nD. 胚', 0, 0, 'A', '双受精和胚乳是被子植物特有的创新，提高了种子萌发效率和营养供应，配合花和果实共同促进被子植物成功。', 0, '[\"A. 双受精和胚乳\",\"B. 维管系统\",\"C. 种子\",\"D. 胚\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (226, 1, '系统发育树中，单系群、并系群和多系群是重要的概念。一个单系群包含一个共同祖先及其所有后代。下列哪个属于单系群的例子？', 0, '', '', 'A. 被子植物\nB. 双子叶植物（传统）\nC. 苔藓植物（传统）\nD. 藻类（传统）', 0, 0, 'A', '被子植物是一个单系群，包含共同祖先及所有后代。传统双子叶、苔藓、藻类常被视为并系或多系群，不满足单系条件。', 0, '[\"A. 被子植物\",\"B. 双子叶植物（传统）\",\"C. 苔藓植物（传统）\",\"D. 藻类（传统）\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (227, 1, '基因树和物种树不一致的现象常由不完全谱系分选或基因渐渗引起。不完全谱系分选（ILS）在什么情况下更常见？', 0, '', '', 'A. 种群大且分化时间短\nB. 种群小且分化时间长\nC. 无基因交流\nD. 世代时间长', 0, 0, 'A', '不完全谱系分选在大种群和快速连续分歧中更常见，祖先多态性在不同支系中随机保留，导致基因树与物种树不一致。', 0, '[\"A. 种群大且分化时间短\",\"B. 种群小且分化时间长\",\"C. 无基因交流\",\"D. 世代时间长\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (228, 1, '同域物种形成不需要地理隔离，主要通过生态位分化或多倍体化等机制实现。同域物种形成在植物中较动物中常见，主要原因之一是？', 0, '', '', 'A. 植物可自交\nB. 植物不能移动\nC. 多倍体化\nD. 植物的传粉距离远', 0, 0, 'C', '多倍体化是植物同域物种形成的重要途径，可在一个世代内产生生殖隔离，不需要地理隔离。', 0, '[\"A. 植物可自交\",\"B. 植物不能移动\",\"C. 多倍体化\",\"D. 植物的传粉距离远\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (229, 1, '植物基因组中，叶绿体基因组（cpDNA）是研究系统发育的重要工具，其特点是？', 0, '', '', 'A. 单拷贝，变异速率快\nB. 多拷贝，保守性强\nC. 无编码基因\nD. 仅母系遗传', 0, 0, 'D', '叶绿体基因组通常母系遗传（被子植物），大小适中，结构保守，包含编码基因，广泛用于植物系统发育研究。', 0, '[\"A. 单拷贝，变异速率快\",\"B. 多拷贝，保守性强\",\"C. 无编码基因\",\"D. 仅母系遗传\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (230, 1, '数量性状（如产量）受多基因控制，QTL定位可以鉴定控制这些性状的基因座。常用的作图群体包括F2群体、BC群体和DH群体。其中，双单倍体（DH）群体的优点是？', 0, '', '', 'A. 基因型纯合、可长期重复\nB. 后代分离大\nC. 构建简单\nD. 包含所有等位基因', 0, 0, 'A', 'DH群体通过花药培养等产生纯合个体，基因型永久稳定，可重复实验，适合QTL精细定位。', 0, '[\"A. 基因型纯合、可长期重复\",\"B. 后代分离大\",\"C. 构建简单\",\"D. 包含所有等位基因\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (231, 1, '植物基因编辑技术除了CRISPR/Cas9外，还有TALEN和ZFN等技术。CRISPR/Cas9相比TALEN的优势在于？', 0, '', '', 'A. 设计简单、效率高\nB. 只能敲除基因\nC. 不能用于多基因编辑\nD. 需要蛋白质工程', 0, 0, 'A', 'CRISPR/Cas9通过guide RNA引导，设计简便，效率高，可实现多基因编辑。TALEN和ZFN需要构建融合蛋白，更复杂。', 0, '[\"A. 设计简单、效率高\",\"B. 只能敲除基因\",\"C. 不能用于多基因编辑\",\"D. 需要蛋白质工程\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (232, 1, '植物转基因中，选择标记基因（如抗生素抗性基因）用于筛选转化细胞。为了避免环境风险，现在常采用无选择标记或只带筛选标记的转化技术。选择标记基因在转基因植物中是？', 0, '', '', 'A. 必需保留\nB. 在筛选完成后可以删除\nC. 控制目的基因表达\nD. 提高产量', 0, 0, 'B', '选择标记基因用于初期筛选，之后可通过共转化、位点特异性重组等技术删除，以减少公众安全顾虑。', 0, '[\"A. 必需保留\",\"B. 在筛选完成后可以删除\",\"C. 控制目的基因表达\",\"D. 提高产量\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (233, 1, '杂交育种中，杂种优势（heterosis）是指杂交后代在生长势、产量等方面优于亲本的现象。杂种优势的遗传机制包括显性假说和超显性假说。显性假说认为杂种优势来自？', 0, '', '', 'A. 杂合位点产生的超亲效应\nB. 有利显性基因的掩盖\nC. 基因互作\nD. 染色体加倍', 0, 0, 'B', '显性假说认为亲本各自携带不利隐性基因，杂交后杂合状态掩盖了隐性不利基因，从而表现优势。', 0, '[\"A. 杂合位点产生的超亲效应\",\"B. 有利显性基因的掩盖\",\"C. 基因互作\",\"D. 染色体加倍\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (234, 1, '诱变育种中，化学诱变剂EMS（甲基磺酸乙酯）主要引起什么类型的突变？', 0, '', '', 'A. 染色体断裂\nB. 点突变（碱基替换）\nC. 基因组多倍化\nD. 转座子激活', 0, 0, 'B', 'EMS是一种烷化剂，主要引起鸟嘌呤的烷基化，导致G-C到A-T的转换突变，是点突变的主要诱导剂。', 0, '[\"A. 染色体断裂\",\"B. 点突变（碱基替换）\",\"C. 基因组多倍化\",\"D. 转座子激活\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (235, 1, '种质资源保存方式中，低温种质库（-18℃）主要适合储存传统种子。对于野生近缘种或稀有物种，还可以通过什么方式长期保存？', 0, '', '', 'A. 液氮超低温保存\nB. 常温储存\nC. 沙藏\nD. 石蜡密封', 0, 0, 'A', '液氮超低温保存（-196℃）可用于长期保存顽拗型种子、茎尖、胚等材料，保持遗传稳定。', 0, '[\"A. 液氮超低温保存\",\"B. 常温储存\",\"C. 沙藏\",\"D. 石蜡密封\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (236, 1, '种质资源在育种中利用时，常通过核心种质（core collection）的概念提高效率。核心种质是？', 0, '', '', 'A. 所有种质材料\nB. 具有代表性的遗传多样性亚集\nC. 仅地方品种\nD. 仅野生近缘种', 0, 0, 'B', '核心种质是从资源库中选出的有代表性的子集，尽可能保留最大遗传多样性，减少育种材料冗余。', 0, '[\"A. 所有种质材料\",\"B. 具有代表性的遗传多样性亚集\",\"C. 仅地方品种\",\"D. 仅野生近缘种\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (237, 1, '植物细胞中，细胞质基质内的微管和微丝等细胞骨架参与细胞形态维持和物质运输。微管的组成蛋白是？', 0, '', '', 'A. 肌动蛋白\nB. 微管蛋白\nC. 中间纤维\nD. 驱动蛋白', 0, 0, 'B', '微管由α-微管蛋白和β-微管蛋白二聚体聚合而成，参与细胞分裂、形态建成等。肌动蛋白组成微丝。', 0, '[\"A. 肌动蛋白\",\"B. 微管蛋白\",\"C. 中间纤维\",\"D. 驱动蛋白\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (238, 1, '机械组织中，纤维细胞和石细胞都属于厚壁组织，两者区别在于纤维细胞是长形、两端尖，石细胞是等径或不规则形。石细胞通常存在于哪些部位？', 0, '', '', 'A. 果皮、种皮\nB. 茎的韧皮部\nC. 根的皮层\nD. 叶片表皮', 0, 0, 'A', '石细胞广泛分布于果皮（如梨的果肉）、种皮（如核桃壳）等部位，提供硬质保护。纤维主要分布于茎木质部和韧皮部。', 0, '[\"A. 果皮、种皮\",\"B. 茎的韧皮部\",\"C. 根的皮层\",\"D. 叶片表皮\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (239, 1, '植物根的初生结构中，内皮层细胞常具有凯氏带，而禾本科植物根的初生结构内皮层细胞壁会进一步增厚，形成马蹄形或U形增厚，这属于什么类型？', 0, '', '', 'A. 凯氏带\nB. 栓质化\nC. 木质化\nD. 五面增厚', 0, 0, 'D', '禾本科植物内皮层细胞壁在凯氏带基础上进一步五面增厚（内壁、径向壁和横向壁），只留外壁薄，加强屏障功能。', 0, '[\"A. 凯氏带\",\"B. 栓质化\",\"C. 木质化\",\"D. 五面增厚\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (240, 1, '茎的次生结构中，早期形成的次生木质部（靠近形成层）颜色较浅，称为早材，晚材颜色较深。在温带地区，年轮边界通常清晰。年轮宽度受什么因素影响最大？', 0, '', '', 'A. 光照强度\nB. 降水和温度季节变化\nC. 土壤类型\nD. 二氧化碳浓度', 0, 0, 'B', '年轮宽度主要由生长季的水分和温度条件决定，降水多、温度适宜的年分年轮宽，反之窄。', 0, '[\"A. 光照强度\",\"B. 降水和温度季节变化\",\"C. 土壤类型\",\"D. 二氧化碳浓度\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (241, 1, '叶片的解剖结构中，气孔在光合作用和蒸腾作用中起关键调节作用。气孔密度在不同植物和环境中差异显著。干旱条件下，气孔密度通常会？', 0, '', '', 'A. 增大\nB. 减少\nC. 不变\nD. 先增后减', 0, 0, 'A', '干旱条件下，有些植物会增加气孔密度以增强气体交换效率，但更常见的是气孔变小或密度变化根据物种而异；也有研究表明干旱可使气孔密度增加以利于CO₂吸收。', 0, '[\"A. 增大\",\"B. 减少\",\"C. 不变\",\"D. 先增后减\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (242, 1, '花的结构中，胚珠着生在子房内，胚珠的发育过程包括珠心、珠被和胚囊的形成。大孢子母细胞经过减数分裂产生四个大孢子，通常其中三个退化，一个发育为？', 0, '', '', 'A. 胚囊\nB. 花粉囊\nC. 胚乳\nD. 珠被', 0, 0, 'A', '功能大孢子进行三次有丝分裂，形成包含8个核的胚囊（成熟胚囊），即雌配子体。', 0, '[\"A. 胚囊\",\"B. 花粉囊\",\"C. 胚乳\",\"D. 珠被\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (243, 1, '种子萌发时，胚乳或子叶中的营养物质被分解转运到胚根、胚轴和胚芽。在双子叶植物（如大豆）中，子叶在萌发过程中？', 0, '', '', 'A. 留在土中\nB. 出土变绿，进行光合作用\nC. 腐烂\nD. 转变为根系', 0, 0, 'B', '大豆等双子叶植物种子萌发时，子叶出土并变绿，能进行光合作用，为幼苗提供营养。', 0, '[\"A. 留在土中\",\"B. 出土变绿，进行光合作用\",\"C. 腐烂\",\"D. 转变为根系\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (244, 1, '植物对水分的吸收和运输中，水通道蛋白（aquaporin）在细胞膜上形成选择性通道，促进水分快速跨膜运输。水通道蛋白对水分的运输属于？', 0, '', '', 'A. 主动运输\nB. 协助扩散\nC. 自由扩散\nD. 胞吞作用', 0, 0, 'B', '水通道蛋白介导的水分跨膜运输是顺水势梯度的协助扩散，不消耗ATP，但比自由扩散效率高。', 0, '[\"A. 主动运输\",\"B. 协助扩散\",\"C. 自由扩散\",\"D. 胞吞作用\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (245, 1, '植物对矿质元素的吸收受根际pH影响。pH过低时，铝和锰的毒性可能增加；pH过高时，铁、锌等微量元素有效性降低。在石灰性土壤上（pH高），植物常出现什么缺素症？', 0, '', '', 'A. 缺氮\nB. 缺铁失绿\nC. 缺磷\nD. 缺钾', 0, 0, 'B', '石灰性土壤pH高，铁、锌、锰等微量元素溶解度降低，植物容易缺铁，表现为新叶脉间失绿。', 0, '[\"A. 缺氮\",\"B. 缺铁失绿\",\"C. 缺磷\",\"D. 缺钾\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (246, 1, 'C4植物和CAM植物的光合碳固定途径都是对高温干旱的适应，但两者在空间和时间分配上有区别。C4途径中，CO₂固定和C3循环发生在不同细胞中，而CAM途径的CO₂固定和还原发生在？', 0, '', '', 'A. 不同细胞\nB. 同一细胞，但时间上分开\nC. 不同器官\nD. 同时进行', 0, 0, 'B', 'CAM途径在时间上分开：夜间固定CO₂（形成苹果酸），白天在同一个细胞中释放并进入卡尔文循环。', 0, '[\"A. 不同细胞\",\"B. 同一细胞，但时间上分开\",\"C. 不同器官\",\"D. 同时进行\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (247, 1, '植物呼吸作用的速率受温度影响显著。在0-35℃范围内，呼吸速率随温度升高而增加，但超过一定温度后下降。这是因为高温导致？', 0, '', '', 'A. 酶变性失活\nB. 底物耗尽\nC. 氧气不足\nD. CO₂积累', 0, 0, 'A', '超过最适温度后，呼吸酶（尤其是线粒体酶）变性失活，呼吸速率迅速下降。', 0, '[\"A. 酶变性失活\",\"B. 底物耗尽\",\"C. 氧气不足\",\"D. CO₂积累\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (248, 1, '植物激素之间具有协同和拮抗作用。生长素和细胞分裂素在调控顶端优势时表现拮抗，生长素促进顶端优势，细胞分裂素促进侧芽生长。在组织培养中，调整二者比例可以决定什么？', 0, '', '', 'A. 愈伤组织形成\nB. 根或芽的分化\nC. 胚胎发生\nD. 体细胞胚成熟', 0, 0, 'B', '生长素/细胞分裂素比例高时诱导生根，比例低时促进生芽，二者平衡决定器官分化方向。', 0, '[\"A. 愈伤组织形成\",\"B. 根或芽的分化\",\"C. 胚胎发生\",\"D. 体细胞胚成熟\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (249, 1, '植物在光照下，光敏色素通过感受红光和远红光可逆转换来调节植物发育。红光（660nm）使光敏色素转变为Pfr型（活性形式），远红光（730nm）使其转变为Pr型。在黑暗中，种子萌发受什么因素限制？', 0, '', '', 'A. 缺乏水分\nB. 缺乏红光使Pfr不足\nC. 温度过高\nD. 氧气不足', 0, 0, 'B', '许多需光种子在黑暗下因为光敏色素Pfr型含量低而不能萌发，红光照射可提高Pfr水平，促进萌发。', 0, '[\"A. 缺乏水分\",\"B. 缺乏红光使Pfr不足\",\"C. 温度过高\",\"D. 氧气不足\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (250, 1, '植物在涝渍（水淹）胁迫下，根系缺氧，会诱导形成通气组织（aerenchyma），以利于氧气从地上部分向根运输。通气组织的形成通常通过细胞的什么过程？', 0, '', '', 'A. 细胞凋亡（程序性死亡）\nB. 细胞分裂\nC. 细胞扩大\nD. 胞间连丝增加', 0, 0, 'A', '通气组织是通过皮层细胞程序性死亡（PCD）形成，使细胞降解，形成气体通道。', 0, '[\"A. 细胞凋亡（程序性死亡）\",\"B. 细胞分裂\",\"C. 细胞扩大\",\"D. 胞间连丝增加\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (251, 1, '红藻门植物中，多数为海产，少数淡水。红藻的光合色素包括叶绿素a、藻红蛋白和藻蓝蛋白。红藻的细胞壁中含有什么胶质，使其有粘滑感？', 0, '', '', 'A. 琼脂和卡拉胶\nB. 果胶\nC. 木质素\nD. 纤维素', 0, 0, 'A', '红藻细胞壁含有琼脂和卡拉胶等胶质，可提取作为培养基原料和食品添加剂。', 0, '[\"A. 琼脂和卡拉胶\",\"B. 果胶\",\"C. 木质素\",\"D. 纤维素\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (252, 1, '褐藻门植物中，海带（Laminaria）是重要的经济海藻。海带的孢子体为大型带状，具有固着器、柄和叶状体。海带的生活史具有世代交替，其配子体为？', 0, '', '', 'A. 微小丝状体\nB. 大型叶状体\nC. 孢子囊\nD. 球状体', 0, 0, 'A', '海带的配子体微小，为丝状体或分枝丝状，与大型孢子体形成明显世代交替。', 0, '[\"A. 微小丝状体\",\"B. 大型叶状体\",\"C. 孢子囊\",\"D. 球状体\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (253, 1, '地钱（Marchantia）是苔纲的常见代表，配子体为叶状体，背部有气孔和气室，腹面有鳞片和假根。地钱的孢子在萌发后形成原丝体，但原丝体不发达。地钱的无性繁殖依靠？', 0, '', '', 'A. 芽胞\nB. 孢子\nC. 断裂\nD. 根茎', 0, 0, 'A', '地钱叶状体上的杯状结构（胞芽杯）产生胞芽，胞芽脱落后可发育成新的配子体，是重要的无性繁殖方式。', 0, '[\"A. 芽胞\",\"B. 孢子\",\"C. 断裂\",\"D. 根茎\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (254, 1, '松柏类是裸子植物中最大和分布最广的类群。松柏类植物多为常绿乔木，叶针形或鳞片形，适应寒冷或干旱环境。松柏类的输导组织仅具管胞，无导管，这属于？', 0, '', '', 'A. 原始特征\nB. 适应特征\nC. 进化优势\nD. 退化特征', 0, 0, 'A', '裸子植物（包括松柏类）的木质部仅具管胞，无导管，是被子植物出现之前的原始特征，管胞同时承担输导和支持功能。', 0, '[\"A. 原始特征\",\"B. 适应特征\",\"C. 进化优势\",\"D. 退化特征\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (255, 1, '银杏和松柏类不同，其木质部具有导管，但数量少。银杏的生殖结构为雌雄异株，雄球花为柔荑花序状，雌球花顶端二歧分叉。银杏的种子具肉质外种皮，成熟后呈黄色，散发特殊气味，这种气味可能有助于什么？', 0, '', '', 'A. 吸引昆虫传粉\nB. 吸引动物取食传播\nC. 驱避害虫\nD. 抵御细菌', 0, 0, 'B', '银杏种子的肉质外种皮腐烂后散发特殊气味，吸引动物取食，帮助种子散布。', 0, '[\"A. 吸引昆虫传粉\",\"B. 吸引动物取食传播\",\"C. 驱避害虫\",\"D. 抵御细菌\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (256, 1, '禾本科植物的叶具有独特的叶舌和叶耳，叶舌位于叶鞘和叶片的交界处，防止水分和病菌侵入。禾本科的叶片条形，具平行脉，叶片基部常有鞘。禾本科的花序是小穗，小穗的基部有两片颖片。颖片的本质是？', 0, '', '', 'A. 退化叶\nB. 萼片\nC. 苞片\nD. 花瓣', 0, 0, 'C', '颖片是禾本科小穗基部的苞片，不是真正的花萼或花瓣。小花由外稃和内稃包裹浆片、雄蕊和雌蕊。', 0, '[\"A. 退化叶\",\"B. 萼片\",\"C. 苞片\",\"D. 花瓣\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (257, 1, '豆科的蝶形花亚科的花冠由旗瓣、翼瓣和龙骨瓣组成，龙骨瓣包裹雄蕊和雌蕊。蝶形花的雄蕊常为二体雄蕊（9+1）或单体雄蕊。二体雄蕊的特征是？', 0, '', '', 'A. 全部雄蕊分离\nB. 9枚合生，1枚分离\nC. 全部合生成管\nD. 雄蕊数目不定', 0, 0, 'B', '二体雄蕊是蝶形花亚科常见特征，9枚雄蕊花丝合生，1枚离生。单体雄蕊（如锦葵科）全部合生。', 0, '[\"A. 全部雄蕊分离\",\"B. 9枚合生，1枚分离\",\"C. 全部合生成管\",\"D. 雄蕊数目不定\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (258, 1, '群落演替中，中期群落通常物种多样性最高，演替到后期顶级群落时，物种多样性可能略降，这是因为？', 0, '', '', 'A. 优势种竞争排除\nB. 环境压力增大\nC. 资源变少\nD. 干扰增多', 0, 0, 'A', '演替后期，优势种竞争力强，可能使一些竞争弱的物种消失，导致多样性略降，即“中等干扰假说”或“竞争排除”影响。', 0, '[\"A. 优势种竞争排除\",\"B. 环境压力增大\",\"C. 资源变少\",\"D. 干扰增多\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (259, 1, '生态系统中，食物链中的营养级通常不超过4-5级，原因是能量逐级递减。在极地或荒漠生态系统，食物链往往更短，因为？', 0, '', '', 'A. 生产力低\nB. 物种丰富\nC. 能量传递效率高\nD. 分解者少', 0, 0, 'A', '极地或荒漠初级生产力低，可支持的营养级数量有限，因此食物链短。', 0, '[\"A. 生产力低\",\"B. 物种丰富\",\"C. 能量传递效率高\",\"D. 分解者少\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (260, 1, '氮素是植物需求量最大的矿质元素之一。植物吸收的铵态氮和硝态氮在根和叶中可同化为氨基酸。铵态氮的直接同化产物是谷氨酰胺，涉及的酶是？', 0, '', '', 'A. 硝酸还原酶\nB. 谷氨酰胺合成酶\nC. 转氨酶\nD. 固氮酶', 0, 0, 'B', '铵态氮在谷氨酰胺合成酶（GS）催化下与谷氨酸反应生成谷氨酰胺，是氮同化的关键步骤。', 0, '[\"A. 硝酸还原酶\",\"B. 谷氨酰胺合成酶\",\"C. 转氨酶\",\"D. 固氮酶\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (261, 1, '植物的化感物质可影响邻近植物的生长、萌发和微生物活动。化感物质属于植物的次生代谢产物。次生代谢物在植物中的主要生态功能是？', 0, '', '', 'A. 能量储存\nB. 防御和信号传递\nC. 光合作用\nD. 细胞壁合成', 0, 0, 'B', '次生代谢物（如酚类、萜类、生物碱）多用于植物防御（抗虫、抗病）、化感和信号传导，非直接生长所需。', 0, '[\"A. 能量储存\",\"B. 防御和信号传递\",\"C. 光合作用\",\"D. 细胞壁合成\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (262, 1, '泛热带分布类型是指广泛分布于全球热带地区的类群。在中国南部，泛热带分布的科有棕榈科、天南星科等。这些科的植物在温带地区少见，原因是？', 0, '', '', 'A. 不耐低温\nB. 不耐干旱\nC. 需弱光\nD. 需酸性土壤', 0, 0, 'A', '泛热带分布类群通常喜温暖，对低温敏感，因此在温带和寒带地区自然分布受限。', 0, '[\"A. 不耐低温\",\"B. 不耐干旱\",\"C. 需弱光\",\"D. 需酸性土壤\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (263, 1, '硬叶常绿林是地中海气候区的典型植被，树种叶片革质、坚硬，叶缘常具刺。这种硬叶特征的主要适应性是？', 0, '', '', 'A. 减少水分蒸腾和防食草\nB. 增加光合面积\nC. 促进落叶\nD. 吸收更多阳光', 0, 0, 'A', '地中海气候夏季炎热干旱，硬叶革质、具刺可减少水分蒸腾，并防御草食动物啃食。', 0, '[\"A. 减少水分蒸腾和防食草\",\"B. 增加光合面积\",\"C. 促进落叶\",\"D. 吸收更多阳光\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (264, 1, '植物从水生到陆生的演化中，气孔的出现解决了气体交换和蒸腾调控的矛盾。早期陆生植物（如角苔、苔藓）已有气孔，但它们的传粉和受精仍然需要水。种子植物通过什么方式彻底摆脱了水对受精的限制？', 0, '', '', 'A. 异花传粉\nB. 花粉管将精子输送到卵细胞\nC. 借助风力\nD. 自花传粉', 0, 0, 'B', '种子植物的花粉管将精子直接输送到雌配子体，不依赖水作为媒介，这是适应陆生的重要创新。', 0, '[\"A. 异花传粉\",\"B. 花粉管将精子输送到卵细胞\",\"C. 借助风力\",\"D. 自花传粉\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (265, 1, '被子植物中，小孢子母细胞（花粉母细胞）在花药内进行减数分裂产生四分体，随后形成花粉粒。花粉粒在传粉时通常处于什么发育阶段？', 0, '', '', 'A. 单核期\nB. 二核期或三核期\nC. 精子期\nD. 四分体期', 0, 0, 'B', '大多数被子植物的花粉在散出时为二核期（一个营养核和一个生殖核）或三核期（两个精核和一个营养核）。', 0, '[\"A. 单核期\",\"B. 二核期或三核期\",\"C. 精子期\",\"D. 四分体期\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (266, 1, '植物的生长和发育受基因调控。控制花器官发育的ABCDE模型中，D类基因主要控制胚珠发育。在拟南芥中，D类基因突变会导致什么？', 0, '', '', 'A. 胚珠缺失或异常\nB. 花瓣缺失\nC. 雄蕊缺失\nD. 萼片异常', 0, 0, 'A', 'ABCDE模型中，D类基因（如STK、SHP）调控胚珠和心皮的发育，突变会导致胚珠发育异常。', 0, '[\"A. 胚珠缺失或异常\",\"B. 花瓣缺失\",\"C. 雄蕊缺失\",\"D. 萼片异常\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (267, 1, '全基因组测序技术的进步极大推动了比较基因组学和进化研究。在植物中，多倍体化事件后常常发生基因组重排和基因丢失，这个过程称为？', 0, '', '', 'A. 二倍化\nB. 多倍化\nC. 转座扩增\nD. 甲基化', 0, 0, 'A', '多倍体化后，基因组会经历二倍化过程，即基因丢失、重排和功能分化，使多倍体基因组趋向二倍体表现。', 0, '[\"A. 二倍化\",\"B. 多倍化\",\"C. 转座扩增\",\"D. 甲基化\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (268, 1, '植物表观遗传调控包括DNA甲基化、组蛋白修饰和RNA干扰等。这些修饰可影响基因表达而不改变DNA序列，并在植物对环境响应中起作用。DNA甲基化通常发生在哪种序列上？', 0, '', '', 'A. 外显子\nB. CpG岛或对称CG位点\nC. 增强子\nD. 启动子中的所有位置', 0, 0, 'B', '在植物中，DNA甲基化常发生在CG、CHG和CHH序列（H=A/T/C），其中CG甲基化最为常见，主要影响转座子和基因表达。', 0, '[\"A. 外显子\",\"B. CpG岛或对称CG位点\",\"C. 增强子\",\"D. 启动子中的所有位置\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (269, 1, '细胞质雄性不育（CMS）在杂交种子生产中具有重要应用。CMS的育性恢复通常由核基因控制，称为恢复基因（Rf）。Rf基因通过什么机制恢复育性？', 0, '', '', 'A. 敲除不育基因\nB. 编辑线粒体转录本\nC. 增加花粉数量\nD. 促进叶绿体发育', 0, 0, 'B', '恢复基因（Rf）通常编码参与线粒体RNA加工的蛋白（如PPR蛋白），能编辑或切割异常转录本，恢复线粒体功能，使花粉可育。', 0, '[\"A. 敲除不育基因\",\"B. 编辑线粒体转录本\",\"C. 增加花粉数量\",\"D. 促进叶绿体发育\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (270, 1, '数量性状的遗传分析中，遗传力（heritability）是衡量遗传因素对表型变异的贡献程度。广义遗传力包括所有遗传效应，狭义遗传力仅考虑加性遗传效应。狭义遗传力较高的性状在育种中较易选择，因为？', 0, '', '', 'A. 加性效应可稳定遗传\nB. 显性效应占主导\nC. 环境变异小\nD. 非加性效应可传递', 0, 0, 'A', '加性遗传效应在子代中可以稳定传递，狭义遗传力越高，表型选择效果越好，育种进展越快。', 0, '[\"A. 加性效应可稳定遗传\",\"B. 显性效应占主导\",\"C. 环境变异小\",\"D. 非加性效应可传递\"]', 0, 0, 1, '', 0, 2);
INSERT INTO `x2_exam_question` VALUES (271, 1, '植物基因编辑技术可用于提高作物抗病性。例如，通过编辑感病基因（如小麦的MLO基因）获得广谱抗白粉病突变体。这种策略称为？', 0, '', '', 'A. 基因敲除\nB. 基因替换\nC. 基因增强\nD. 基因插入', 0, 0, 'A', '敲除感病基因（如MLO）使病原菌不能侵入，是基因敲除策略在抗病育种中的应用。', 0, '[\"A. 基因敲除\",\"B. 基因替换\",\"C. 基因增强\",\"D. 基因插入\"]', 0, 0, 1, '', 0, 1);
INSERT INTO `x2_exam_question` VALUES (272, 1, '杂交育种中，对于自花授粉作物（如水稻、小麦），通常采用人工去雄和授粉的方法获得杂交种子。对于异花授粉作物（如玉米），常利用什么类型简化杂交？', 0, '', '', 'A. 自交系和雄性不育系\nB. 无融合生殖\nC. 无性繁殖\nD. 嫁接', 0, 0, 'A', '玉米等异花授粉作物利用自交系和雄性不育系生产杂交种，省去人工去雄。', 0, '[\"A. 自交系和雄性不育系\",\"B. 无融合生殖\",\"C. 无性繁殖\",\"D. 嫁接\"]', 0, 0, 1, '', 0, 1);

-- ----------------------------
-- Table structure for x2_exam_question_relation
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_question_relation`;
CREATE TABLE `x2_exam_question_relation`  (
  `qkid` int NOT NULL AUTO_INCREMENT,
  `qkquestionid` int NOT NULL DEFAULT 0,
  `qkpointid` int NOT NULL DEFAULT 0,
  `qkstatus` int NULL DEFAULT 1,
  PRIMARY KEY (`qkid`) USING BTREE,
  INDEX `qkpointid`(`qkpointid` ASC) USING BTREE,
  INDEX `qkquestionid`(`qkquestionid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 270 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_question_relation
-- ----------------------------
INSERT INTO `x2_exam_question_relation` VALUES (1, 1, 1, 1);
INSERT INTO `x2_exam_question_relation` VALUES (2, 2, 1, 1);
INSERT INTO `x2_exam_question_relation` VALUES (3, 3, 1, 1);
INSERT INTO `x2_exam_question_relation` VALUES (5, 7, 3, 1);
INSERT INTO `x2_exam_question_relation` VALUES (6, 8, 3, 1);
INSERT INTO `x2_exam_question_relation` VALUES (7, 9, 3, 1);
INSERT INTO `x2_exam_question_relation` VALUES (8, 10, 4, 1);
INSERT INTO `x2_exam_question_relation` VALUES (9, 11, 4, 1);
INSERT INTO `x2_exam_question_relation` VALUES (10, 12, 4, 1);
INSERT INTO `x2_exam_question_relation` VALUES (11, 13, 5, 1);
INSERT INTO `x2_exam_question_relation` VALUES (12, 14, 5, 1);
INSERT INTO `x2_exam_question_relation` VALUES (13, 15, 6, 1);
INSERT INTO `x2_exam_question_relation` VALUES (14, 16, 6, 1);
INSERT INTO `x2_exam_question_relation` VALUES (15, 19, 7, 1);
INSERT INTO `x2_exam_question_relation` VALUES (16, 20, 7, 1);
INSERT INTO `x2_exam_question_relation` VALUES (17, 21, 8, 1);
INSERT INTO `x2_exam_question_relation` VALUES (18, 22, 8, 1);
INSERT INTO `x2_exam_question_relation` VALUES (19, 23, 8, 1);
INSERT INTO `x2_exam_question_relation` VALUES (20, 24, 9, 1);
INSERT INTO `x2_exam_question_relation` VALUES (21, 25, 9, 1);
INSERT INTO `x2_exam_question_relation` VALUES (22, 26, 9, 1);
INSERT INTO `x2_exam_question_relation` VALUES (23, 27, 10, 1);
INSERT INTO `x2_exam_question_relation` VALUES (24, 28, 10, 1);
INSERT INTO `x2_exam_question_relation` VALUES (25, 31, 11, 1);
INSERT INTO `x2_exam_question_relation` VALUES (26, 32, 11, 1);
INSERT INTO `x2_exam_question_relation` VALUES (27, 33, 11, 1);
INSERT INTO `x2_exam_question_relation` VALUES (28, 34, 12, 1);
INSERT INTO `x2_exam_question_relation` VALUES (29, 35, 12, 1);
INSERT INTO `x2_exam_question_relation` VALUES (30, 36, 12, 1);
INSERT INTO `x2_exam_question_relation` VALUES (31, 39, 13, 1);
INSERT INTO `x2_exam_question_relation` VALUES (32, 40, 13, 1);
INSERT INTO `x2_exam_question_relation` VALUES (33, 41, 14, 1);
INSERT INTO `x2_exam_question_relation` VALUES (34, 42, 14, 1);
INSERT INTO `x2_exam_question_relation` VALUES (35, 43, 14, 1);
INSERT INTO `x2_exam_question_relation` VALUES (36, 44, 15, 1);
INSERT INTO `x2_exam_question_relation` VALUES (37, 45, 15, 1);
INSERT INTO `x2_exam_question_relation` VALUES (38, 46, 16, 1);
INSERT INTO `x2_exam_question_relation` VALUES (39, 47, 16, 1);
INSERT INTO `x2_exam_question_relation` VALUES (40, 48, 17, 1);
INSERT INTO `x2_exam_question_relation` VALUES (41, 49, 17, 1);
INSERT INTO `x2_exam_question_relation` VALUES (42, 50, 18, 1);
INSERT INTO `x2_exam_question_relation` VALUES (43, 51, 18, 1);
INSERT INTO `x2_exam_question_relation` VALUES (44, 52, 19, 1);
INSERT INTO `x2_exam_question_relation` VALUES (45, 53, 19, 1);
INSERT INTO `x2_exam_question_relation` VALUES (46, 54, 20, 1);
INSERT INTO `x2_exam_question_relation` VALUES (47, 55, 20, 1);
INSERT INTO `x2_exam_question_relation` VALUES (48, 56, 21, 1);
INSERT INTO `x2_exam_question_relation` VALUES (49, 60, 22, 1);
INSERT INTO `x2_exam_question_relation` VALUES (50, 61, 22, 1);
INSERT INTO `x2_exam_question_relation` VALUES (51, 62, 23, 1);
INSERT INTO `x2_exam_question_relation` VALUES (52, 63, 23, 1);
INSERT INTO `x2_exam_question_relation` VALUES (53, 64, 24, 1);
INSERT INTO `x2_exam_question_relation` VALUES (54, 65, 24, 1);
INSERT INTO `x2_exam_question_relation` VALUES (55, 66, 25, 1);
INSERT INTO `x2_exam_question_relation` VALUES (56, 67, 25, 1);
INSERT INTO `x2_exam_question_relation` VALUES (57, 68, 26, 1);
INSERT INTO `x2_exam_question_relation` VALUES (58, 69, 26, 1);
INSERT INTO `x2_exam_question_relation` VALUES (59, 70, 27, 1);
INSERT INTO `x2_exam_question_relation` VALUES (60, 71, 27, 1);
INSERT INTO `x2_exam_question_relation` VALUES (61, 72, 27, 1);
INSERT INTO `x2_exam_question_relation` VALUES (62, 73, 28, 1);
INSERT INTO `x2_exam_question_relation` VALUES (63, 74, 28, 1);
INSERT INTO `x2_exam_question_relation` VALUES (64, 75, 29, 1);
INSERT INTO `x2_exam_question_relation` VALUES (65, 76, 29, 1);
INSERT INTO `x2_exam_question_relation` VALUES (66, 77, 30, 1);
INSERT INTO `x2_exam_question_relation` VALUES (67, 78, 30, 1);
INSERT INTO `x2_exam_question_relation` VALUES (68, 79, 31, 1);
INSERT INTO `x2_exam_question_relation` VALUES (69, 80, 31, 1);
INSERT INTO `x2_exam_question_relation` VALUES (70, 81, 32, 1);
INSERT INTO `x2_exam_question_relation` VALUES (71, 82, 32, 1);
INSERT INTO `x2_exam_question_relation` VALUES (72, 83, 33, 1);
INSERT INTO `x2_exam_question_relation` VALUES (73, 84, 33, 1);
INSERT INTO `x2_exam_question_relation` VALUES (74, 85, 34, 1);
INSERT INTO `x2_exam_question_relation` VALUES (75, 86, 34, 1);
INSERT INTO `x2_exam_question_relation` VALUES (76, 87, 35, 1);
INSERT INTO `x2_exam_question_relation` VALUES (77, 88, 35, 1);
INSERT INTO `x2_exam_question_relation` VALUES (78, 89, 36, 1);
INSERT INTO `x2_exam_question_relation` VALUES (79, 90, 36, 1);
INSERT INTO `x2_exam_question_relation` VALUES (80, 91, 37, 1);
INSERT INTO `x2_exam_question_relation` VALUES (81, 92, 37, 1);
INSERT INTO `x2_exam_question_relation` VALUES (82, 93, 38, 1);
INSERT INTO `x2_exam_question_relation` VALUES (83, 97, 39, 1);
INSERT INTO `x2_exam_question_relation` VALUES (84, 98, 39, 1);
INSERT INTO `x2_exam_question_relation` VALUES (85, 99, 1, 1);
INSERT INTO `x2_exam_question_relation` VALUES (86, 100, 2, 1);
INSERT INTO `x2_exam_question_relation` VALUES (87, 101, 3, 1);
INSERT INTO `x2_exam_question_relation` VALUES (88, 102, 4, 1);
INSERT INTO `x2_exam_question_relation` VALUES (89, 103, 5, 1);
INSERT INTO `x2_exam_question_relation` VALUES (90, 104, 6, 1);
INSERT INTO `x2_exam_question_relation` VALUES (91, 105, 7, 1);
INSERT INTO `x2_exam_question_relation` VALUES (92, 106, 8, 1);
INSERT INTO `x2_exam_question_relation` VALUES (93, 107, 9, 1);
INSERT INTO `x2_exam_question_relation` VALUES (94, 108, 10, 1);
INSERT INTO `x2_exam_question_relation` VALUES (95, 109, 11, 1);
INSERT INTO `x2_exam_question_relation` VALUES (96, 110, 12, 1);
INSERT INTO `x2_exam_question_relation` VALUES (97, 111, 13, 1);
INSERT INTO `x2_exam_question_relation` VALUES (98, 112, 14, 1);
INSERT INTO `x2_exam_question_relation` VALUES (99, 113, 16, 1);
INSERT INTO `x2_exam_question_relation` VALUES (100, 114, 17, 1);
INSERT INTO `x2_exam_question_relation` VALUES (101, 115, 18, 1);
INSERT INTO `x2_exam_question_relation` VALUES (102, 116, 19, 1);
INSERT INTO `x2_exam_question_relation` VALUES (103, 117, 20, 1);
INSERT INTO `x2_exam_question_relation` VALUES (104, 118, 21, 1);
INSERT INTO `x2_exam_question_relation` VALUES (105, 119, 22, 1);
INSERT INTO `x2_exam_question_relation` VALUES (106, 120, 23, 1);
INSERT INTO `x2_exam_question_relation` VALUES (107, 121, 24, 1);
INSERT INTO `x2_exam_question_relation` VALUES (108, 122, 25, 1);
INSERT INTO `x2_exam_question_relation` VALUES (109, 123, 26, 1);
INSERT INTO `x2_exam_question_relation` VALUES (110, 124, 27, 1);
INSERT INTO `x2_exam_question_relation` VALUES (111, 125, 30, 1);
INSERT INTO `x2_exam_question_relation` VALUES (112, 126, 31, 1);
INSERT INTO `x2_exam_question_relation` VALUES (113, 127, 32, 1);
INSERT INTO `x2_exam_question_relation` VALUES (114, 128, 33, 1);
INSERT INTO `x2_exam_question_relation` VALUES (115, 129, 34, 1);
INSERT INTO `x2_exam_question_relation` VALUES (116, 130, 39, 1);
INSERT INTO `x2_exam_question_relation` VALUES (117, 131, 2, 1);
INSERT INTO `x2_exam_question_relation` VALUES (118, 132, 1, 1);
INSERT INTO `x2_exam_question_relation` VALUES (119, 133, 2, 1);
INSERT INTO `x2_exam_question_relation` VALUES (120, 134, 3, 1);
INSERT INTO `x2_exam_question_relation` VALUES (121, 135, 4, 1);
INSERT INTO `x2_exam_question_relation` VALUES (122, 136, 5, 1);
INSERT INTO `x2_exam_question_relation` VALUES (123, 137, 6, 1);
INSERT INTO `x2_exam_question_relation` VALUES (124, 138, 7, 1);
INSERT INTO `x2_exam_question_relation` VALUES (125, 139, 8, 1);
INSERT INTO `x2_exam_question_relation` VALUES (126, 140, 9, 1);
INSERT INTO `x2_exam_question_relation` VALUES (127, 141, 10, 1);
INSERT INTO `x2_exam_question_relation` VALUES (128, 142, 11, 1);
INSERT INTO `x2_exam_question_relation` VALUES (129, 143, 12, 1);
INSERT INTO `x2_exam_question_relation` VALUES (130, 144, 13, 1);
INSERT INTO `x2_exam_question_relation` VALUES (131, 145, 14, 1);
INSERT INTO `x2_exam_question_relation` VALUES (132, 146, 15, 1);
INSERT INTO `x2_exam_question_relation` VALUES (133, 147, 16, 1);
INSERT INTO `x2_exam_question_relation` VALUES (134, 148, 17, 1);
INSERT INTO `x2_exam_question_relation` VALUES (135, 149, 18, 1);
INSERT INTO `x2_exam_question_relation` VALUES (136, 150, 19, 1);
INSERT INTO `x2_exam_question_relation` VALUES (137, 151, 20, 1);
INSERT INTO `x2_exam_question_relation` VALUES (138, 152, 21, 1);
INSERT INTO `x2_exam_question_relation` VALUES (139, 153, 22, 1);
INSERT INTO `x2_exam_question_relation` VALUES (140, 154, 23, 1);
INSERT INTO `x2_exam_question_relation` VALUES (141, 155, 24, 1);
INSERT INTO `x2_exam_question_relation` VALUES (142, 156, 25, 1);
INSERT INTO `x2_exam_question_relation` VALUES (143, 157, 26, 1);
INSERT INTO `x2_exam_question_relation` VALUES (144, 158, 27, 1);
INSERT INTO `x2_exam_question_relation` VALUES (145, 159, 28, 1);
INSERT INTO `x2_exam_question_relation` VALUES (146, 160, 29, 1);
INSERT INTO `x2_exam_question_relation` VALUES (147, 161, 30, 1);
INSERT INTO `x2_exam_question_relation` VALUES (148, 162, 31, 1);
INSERT INTO `x2_exam_question_relation` VALUES (149, 163, 32, 1);
INSERT INTO `x2_exam_question_relation` VALUES (150, 164, 33, 1);
INSERT INTO `x2_exam_question_relation` VALUES (151, 165, 36, 1);
INSERT INTO `x2_exam_question_relation` VALUES (152, 166, 37, 1);
INSERT INTO `x2_exam_question_relation` VALUES (153, 167, 39, 1);
INSERT INTO `x2_exam_question_relation` VALUES (154, 168, 1, 1);
INSERT INTO `x2_exam_question_relation` VALUES (155, 169, 1, 1);
INSERT INTO `x2_exam_question_relation` VALUES (156, 170, 2, 1);
INSERT INTO `x2_exam_question_relation` VALUES (157, 171, 2, 1);
INSERT INTO `x2_exam_question_relation` VALUES (158, 172, 3, 1);
INSERT INTO `x2_exam_question_relation` VALUES (159, 173, 3, 1);
INSERT INTO `x2_exam_question_relation` VALUES (160, 174, 4, 1);
INSERT INTO `x2_exam_question_relation` VALUES (161, 175, 4, 1);
INSERT INTO `x2_exam_question_relation` VALUES (162, 176, 5, 1);
INSERT INTO `x2_exam_question_relation` VALUES (163, 177, 5, 1);
INSERT INTO `x2_exam_question_relation` VALUES (164, 178, 6, 1);
INSERT INTO `x2_exam_question_relation` VALUES (165, 179, 6, 1);
INSERT INTO `x2_exam_question_relation` VALUES (166, 180, 7, 1);
INSERT INTO `x2_exam_question_relation` VALUES (167, 181, 7, 1);
INSERT INTO `x2_exam_question_relation` VALUES (168, 182, 8, 1);
INSERT INTO `x2_exam_question_relation` VALUES (169, 183, 8, 1);
INSERT INTO `x2_exam_question_relation` VALUES (170, 184, 9, 1);
INSERT INTO `x2_exam_question_relation` VALUES (171, 185, 9, 1);
INSERT INTO `x2_exam_question_relation` VALUES (172, 186, 10, 1);
INSERT INTO `x2_exam_question_relation` VALUES (173, 187, 10, 1);
INSERT INTO `x2_exam_question_relation` VALUES (174, 188, 11, 1);
INSERT INTO `x2_exam_question_relation` VALUES (175, 189, 11, 1);
INSERT INTO `x2_exam_question_relation` VALUES (176, 190, 12, 1);
INSERT INTO `x2_exam_question_relation` VALUES (177, 191, 12, 1);
INSERT INTO `x2_exam_question_relation` VALUES (178, 192, 13, 1);
INSERT INTO `x2_exam_question_relation` VALUES (179, 193, 14, 1);
INSERT INTO `x2_exam_question_relation` VALUES (180, 194, 14, 1);
INSERT INTO `x2_exam_question_relation` VALUES (181, 195, 15, 1);
INSERT INTO `x2_exam_question_relation` VALUES (182, 196, 16, 1);
INSERT INTO `x2_exam_question_relation` VALUES (183, 197, 16, 1);
INSERT INTO `x2_exam_question_relation` VALUES (184, 198, 17, 1);
INSERT INTO `x2_exam_question_relation` VALUES (185, 199, 17, 1);
INSERT INTO `x2_exam_question_relation` VALUES (186, 200, 18, 1);
INSERT INTO `x2_exam_question_relation` VALUES (187, 201, 18, 1);
INSERT INTO `x2_exam_question_relation` VALUES (188, 202, 19, 1);
INSERT INTO `x2_exam_question_relation` VALUES (189, 203, 19, 1);
INSERT INTO `x2_exam_question_relation` VALUES (190, 204, 20, 1);
INSERT INTO `x2_exam_question_relation` VALUES (191, 205, 20, 1);
INSERT INTO `x2_exam_question_relation` VALUES (192, 206, 21, 1);
INSERT INTO `x2_exam_question_relation` VALUES (193, 207, 21, 1);
INSERT INTO `x2_exam_question_relation` VALUES (194, 208, 21, 1);
INSERT INTO `x2_exam_question_relation` VALUES (195, 209, 22, 1);
INSERT INTO `x2_exam_question_relation` VALUES (196, 210, 22, 1);
INSERT INTO `x2_exam_question_relation` VALUES (197, 211, 23, 1);
INSERT INTO `x2_exam_question_relation` VALUES (198, 212, 23, 1);
INSERT INTO `x2_exam_question_relation` VALUES (199, 213, 24, 1);
INSERT INTO `x2_exam_question_relation` VALUES (200, 214, 24, 1);
INSERT INTO `x2_exam_question_relation` VALUES (201, 215, 25, 1);
INSERT INTO `x2_exam_question_relation` VALUES (202, 216, 25, 1);
INSERT INTO `x2_exam_question_relation` VALUES (203, 217, 26, 1);
INSERT INTO `x2_exam_question_relation` VALUES (204, 218, 27, 1);
INSERT INTO `x2_exam_question_relation` VALUES (205, 219, 27, 1);
INSERT INTO `x2_exam_question_relation` VALUES (206, 220, 28, 1);
INSERT INTO `x2_exam_question_relation` VALUES (207, 221, 28, 1);
INSERT INTO `x2_exam_question_relation` VALUES (208, 222, 29, 1);
INSERT INTO `x2_exam_question_relation` VALUES (209, 223, 30, 1);
INSERT INTO `x2_exam_question_relation` VALUES (210, 224, 31, 1);
INSERT INTO `x2_exam_question_relation` VALUES (211, 225, 31, 1);
INSERT INTO `x2_exam_question_relation` VALUES (212, 226, 32, 1);
INSERT INTO `x2_exam_question_relation` VALUES (213, 227, 32, 1);
INSERT INTO `x2_exam_question_relation` VALUES (214, 228, 33, 1);
INSERT INTO `x2_exam_question_relation` VALUES (215, 229, 35, 1);
INSERT INTO `x2_exam_question_relation` VALUES (216, 230, 36, 1);
INSERT INTO `x2_exam_question_relation` VALUES (217, 231, 37, 1);
INSERT INTO `x2_exam_question_relation` VALUES (218, 232, 37, 1);
INSERT INTO `x2_exam_question_relation` VALUES (219, 233, 38, 1);
INSERT INTO `x2_exam_question_relation` VALUES (220, 234, 38, 1);
INSERT INTO `x2_exam_question_relation` VALUES (221, 235, 39, 1);
INSERT INTO `x2_exam_question_relation` VALUES (222, 236, 39, 1);
INSERT INTO `x2_exam_question_relation` VALUES (223, 237, 1, 1);
INSERT INTO `x2_exam_question_relation` VALUES (224, 238, 2, 1);
INSERT INTO `x2_exam_question_relation` VALUES (225, 239, 3, 1);
INSERT INTO `x2_exam_question_relation` VALUES (226, 240, 4, 1);
INSERT INTO `x2_exam_question_relation` VALUES (227, 241, 5, 1);
INSERT INTO `x2_exam_question_relation` VALUES (228, 242, 6, 1);
INSERT INTO `x2_exam_question_relation` VALUES (229, 243, 7, 1);
INSERT INTO `x2_exam_question_relation` VALUES (230, 244, 8, 1);
INSERT INTO `x2_exam_question_relation` VALUES (231, 245, 9, 1);
INSERT INTO `x2_exam_question_relation` VALUES (232, 246, 10, 1);
INSERT INTO `x2_exam_question_relation` VALUES (233, 247, 11, 1);
INSERT INTO `x2_exam_question_relation` VALUES (234, 248, 12, 1);
INSERT INTO `x2_exam_question_relation` VALUES (235, 249, 13, 1);
INSERT INTO `x2_exam_question_relation` VALUES (236, 250, 14, 1);
INSERT INTO `x2_exam_question_relation` VALUES (237, 251, 16, 1);
INSERT INTO `x2_exam_question_relation` VALUES (238, 252, 16, 1);
INSERT INTO `x2_exam_question_relation` VALUES (239, 253, 17, 1);
INSERT INTO `x2_exam_question_relation` VALUES (240, 254, 19, 1);
INSERT INTO `x2_exam_question_relation` VALUES (241, 255, 19, 1);
INSERT INTO `x2_exam_question_relation` VALUES (242, 256, 21, 1);
INSERT INTO `x2_exam_question_relation` VALUES (243, 257, 21, 1);
INSERT INTO `x2_exam_question_relation` VALUES (244, 258, 23, 1);
INSERT INTO `x2_exam_question_relation` VALUES (245, 259, 24, 1);
INSERT INTO `x2_exam_question_relation` VALUES (246, 260, 24, 1);
INSERT INTO `x2_exam_question_relation` VALUES (247, 261, 25, 1);
INSERT INTO `x2_exam_question_relation` VALUES (248, 262, 26, 1);
INSERT INTO `x2_exam_question_relation` VALUES (249, 263, 27, 1);
INSERT INTO `x2_exam_question_relation` VALUES (250, 264, 29, 1);
INSERT INTO `x2_exam_question_relation` VALUES (251, 265, 31, 1);
INSERT INTO `x2_exam_question_relation` VALUES (252, 266, 31, 1);
INSERT INTO `x2_exam_question_relation` VALUES (253, 267, 34, 1);
INSERT INTO `x2_exam_question_relation` VALUES (254, 268, 34, 1);
INSERT INTO `x2_exam_question_relation` VALUES (255, 269, 35, 1);
INSERT INTO `x2_exam_question_relation` VALUES (256, 270, 36, 1);
INSERT INTO `x2_exam_question_relation` VALUES (257, 271, 37, 1);
INSERT INTO `x2_exam_question_relation` VALUES (268, 93, 9, 1);
INSERT INTO `x2_exam_question_relation` VALUES (269, 93, 10, 1);

-- ----------------------------
-- Table structure for x2_exam_question_type
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_question_type`;
CREATE TABLE `x2_exam_question_type`  (
  `questid` int NOT NULL AUTO_INCREMENT,
  `questype` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `questsort` int NOT NULL DEFAULT 0,
  `questchoice` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`questid`) USING BTREE,
  INDEX `questchoice`(`questchoice` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_question_type
-- ----------------------------
INSERT INTO `x2_exam_question_type` VALUES (1, '单选题', 0, 1);
INSERT INTO `x2_exam_question_type` VALUES (2, '多选题', 0, 2);
INSERT INTO `x2_exam_question_type` VALUES (3, '不定项题', 0, 3);
INSERT INTO `x2_exam_question_type` VALUES (4, '判断题', 0, 4);
INSERT INTO `x2_exam_question_type` VALUES (5, '定值填空', 0, 5);
INSERT INTO `x2_exam_question_type` VALUES (6, '填空题', 1, 101);
INSERT INTO `x2_exam_question_type` VALUES (7, '问答题', 1, 102);

-- ----------------------------
-- Table structure for x2_exam_section
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_section`;
CREATE TABLE `x2_exam_section`  (
  `sectionid` int NOT NULL AUTO_INCREMENT,
  `section` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `sectionsubjectid` int NOT NULL DEFAULT 0,
  `sectiondescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `sectionsequence` int NOT NULL,
  PRIMARY KEY (`sectionid`) USING BTREE,
  INDEX `section`(`section` ASC) USING BTREE,
  INDEX `sectionsubjectid`(`sectionsubjectid` ASC) USING BTREE,
  INDEX `sectionsequence`(`sectionsequence` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_section
-- ----------------------------
INSERT INTO `x2_exam_section` VALUES (1, '植物形态解剖学', 1, '', 0);
INSERT INTO `x2_exam_section` VALUES (2, '植物生理学', 1, '', 0);
INSERT INTO `x2_exam_section` VALUES (3, '植物分类学', 1, '', 0);
INSERT INTO `x2_exam_section` VALUES (4, '植物生态学与地理植物学', 1, '', 0);
INSERT INTO `x2_exam_section` VALUES (5, '植物进化与系统学', 1, '', 0);
INSERT INTO `x2_exam_section` VALUES (6, '植物遗传学与育种学', 1, '', 0);

-- ----------------------------
-- Table structure for x2_exam_session
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_session`;
CREATE TABLE `x2_exam_session`  (
  `esid` int NOT NULL AUTO_INCREMENT,
  `espassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `esbasicid` int NULL DEFAULT NULL,
  `esfadtime` int NULL DEFAULT NULL,
  PRIMARY KEY (`esid`) USING BTREE,
  UNIQUE INDEX `espassport`(`espassport` ASC) USING BTREE,
  INDEX `esbasicid`(`esbasicid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_session
-- ----------------------------
INSERT INTO `x2_exam_session` VALUES (1, '0025896gd42safx0001', 1, 0);

-- ----------------------------
-- Table structure for x2_exam_subject
-- ----------------------------
DROP TABLE IF EXISTS `x2_exam_subject`;
CREATE TABLE `x2_exam_subject`  (
  `subjectid` int NOT NULL AUTO_INCREMENT,
  `subject` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `subjectsetting` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`subjectid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_exam_subject
-- ----------------------------
INSERT INTO `x2_exam_subject` VALUES (1, '植物学', '[1,2,3,4]');

-- ----------------------------
-- Table structure for x2_member
-- ----------------------------
DROP TABLE IF EXISTS `x2_member`;
CREATE TABLE `x2_member`  (
  `mid` int NOT NULL AUTO_INCREMENT,
  `mname` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '姓名',
  `mphoto` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '照片',
  `mphone` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '手机号',
  `maddress` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '地址',
  `mpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '身份证号',
  `mpassportimg` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '身份证照片A',
  `msex` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '性别',
  `mbirthday` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '生日',
  `mpolitic` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '政治面貌',
  `medu` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '学历',
  `munit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '单位全称',
  `mcompany` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '公司名称',
  `mjobtime` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '参加工作时间',
  `mjob` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '职务',
  `mjobtitle` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '职称或技能名称',
  `mteam` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '小队名称',
  `mtext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL COMMENT '其他有关情况陈述',
  `mresume` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL COMMENT '工作经历',
  `mtime` int NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`mid`) USING BTREE,
  UNIQUE INDEX `mpassport`(`mpassport` ASC) USING BTREE,
  INDEX `mname`(`mname` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_member
-- ----------------------------
INSERT INTO `x2_member` VALUES (1, '火眼', 'storage/attach/public/20260719/ba315c85cf2d9116a1ebe2ad25a84c87.png', '18738317214', 'noaddress', '0025896gd42safx0001', 'storage/attach/public/20260719/d9d9dae671a6e68b3e8b71f61bf4d035.png', '男', '1990-05-07', '团员', '本科', 'noname', 'noname', '2007', '经理', '工程师', '技术部', '', '', 1784461334);
INSERT INTO `x2_member` VALUES (2, '火眼', '', '', '', '00000d69ad4e72d9e9801269', '', '男', '', '', '', '', '', '', '', '', '', '', '', 0);
INSERT INTO `x2_member` VALUES (3, '无名', '', '', '', '000ba06a730f38defa429b86', '', '男', '', '', '', '', '', '', '', '', '', '', '', 0);
INSERT INTO `x2_member` VALUES (7, '张三22222', '', '', '', '0045c699d234336630dd18e4', '', '', '', '', '', '', '', '', '', '', '', '', '', 0);

-- ----------------------------
-- Table structure for x2_system_app
-- ----------------------------
DROP TABLE IF EXISTS `x2_system_app`;
CREATE TABLE `x2_system_app`  (
  `appid` int NOT NULL AUTO_INCREMENT,
  `appcode` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `appname` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `appthumb` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `appstatus` int NOT NULL DEFAULT 0,
  `appsetting` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`appid`) USING BTREE,
  INDEX `appstatus`(`appstatus` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_system_app
-- ----------------------------
INSERT INTO `x2_system_app` VALUES (1, 'user', '用户', '', 1, '{\"closeregist\":true,\"loginmodel\":0,\"userverify\":0,\"emailverify\":0,\"emailaccount\":\"\",\"emailpassword\":\"\"}');
INSERT INTO `x2_system_app` VALUES (2, 'exam', '考试', '', 1, '{\"watermark\":0,\"fontsize\":\"18\",\"color\":\"#333333\",\"rotate\":0}');

-- ----------------------------
-- Table structure for x2_system_session
-- ----------------------------
DROP TABLE IF EXISTS `x2_system_session`;
CREATE TABLE `x2_system_session`  (
  `serialid` int NOT NULL AUTO_INCREMENT,
  `sessionid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `sessionuserid` int NOT NULL DEFAULT 0,
  `sessionauthtoken` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `sessiongracetoken` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `sessiongraceexpire` int NULL DEFAULT NULL,
  `sessionsalt` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `sessionip` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `sessioncurrent` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT '',
  `sessionlogintime` int NOT NULL DEFAULT 0,
  `sessionlasttime` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`serialid`) USING BTREE,
  UNIQUE INDEX `sessionuserid`(`sessionuserid` ASC) USING BTREE,
  UNIQUE INDEX `sessionid`(`sessionid` ASC) USING BTREE,
  INDEX `sessionauthtoken`(`sessionauthtoken` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_system_session
-- ----------------------------

-- ----------------------------
-- Table structure for x2_task
-- ----------------------------
DROP TABLE IF EXISTS `x2_task`;
CREATE TABLE `x2_task`  (
  `taskid` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '任务ID',
  `tasksid` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属任务计划ID（0表示手动即时任务）',
  `taskname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务名称（手动任务为添加时名称，循环任务为计划名）',
  `taskclass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务调用的类（如 SendEmailTask）',
  `taskpayload` json NOT NULL COMMENT '类参数（JSON格式）',
  `tasktype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default' COMMENT '类别/队列（email/report/payment）',
  `taskmaxattempts` tinyint UNSIGNED NOT NULL DEFAULT 3 COMMENT '最大尝试次数',
  `taskpriority` int NULL DEFAULT NULL,
  `tasktimeout` int UNSIGNED NULL DEFAULT 300 COMMENT '超时时间（秒，0表示不限制）',
  `taskattempts` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '已尝试次数',
  `taskstatus` enum('pending','locked','finished') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT '任务状态（pending=待处理，locked=处理中，finished=已完成）',
  `taskscheduletime` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '预期执行时间（Unix时间戳，0表示立即执行）',
  `tasklocktime` bigint UNSIGNED NULL DEFAULT NULL COMMENT '锁定时间（Unix时间戳）',
  `taskresult` tinyint NOT NULL DEFAULT 0 COMMENT '执行结果（0=未执行，1=失败，2=成功）',
  `taskinfo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '执行返回信息（成功时存结果，失败时存错误信息）',
  `taskstarttime` bigint UNSIGNED NULL DEFAULT NULL COMMENT '任务开始执行时间（Unix时间戳）',
  `taskfinishtime` bigint UNSIGNED NULL DEFAULT NULL COMMENT '任务结束时间（Unix时间戳）',
  PRIMARY KEY (`taskid`) USING BTREE,
  INDEX `idx_status_type_schedule`(`taskstatus` ASC, `tasktype` ASC, `taskscheduletime` ASC) USING BTREE,
  INDEX `idx_tasksid`(`tasksid` ASC) USING BTREE,
  INDEX `idx_tasktype`(`tasktype` ASC) USING BTREE,
  INDEX `idx_taskstatus`(`taskstatus` ASC) USING BTREE,
  INDEX `idx_taskscheduletime`(`taskscheduletime` ASC) USING BTREE,
  INDEX `idx_tasklocktime`(`tasklocktime` ASC) USING BTREE,
  INDEX `idx_taskresult`(`taskresult` ASC) USING BTREE,
  INDEX `taskpriority`(`taskpriority` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '任务队列表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_task
-- ----------------------------
INSERT INTO `x2_task` VALUES (1, 0, '临时任务队列', '\\PHPEMS\\App\\Task\\Service\\Event\\TaskDemo', 'null', 'default', 3, 5, 30, 1, 'finished', 0, 1784949424, 2, '{\"duration\":0,\"timestamp\":1784949424,\"worker_id\":\"worker_35492_6a642ab033f80\"}', 1784949424, 1784949424);
INSERT INTO `x2_task` VALUES (2, 2, '循环任务测试', '\\PHPEMS\\App\\Task\\Service\\Event\\TaskDemo', 'null', 'default', 3, 10, 300, 1, 'finished', 1784911784, 1784949424, 2, '{\"duration\":0,\"timestamp\":1784949424,\"worker_id\":\"worker_35492_6a642ab033f80\"}', 1784949424, 1784949424);
INSERT INTO `x2_task` VALUES (3, 2, '循环任务测试1', '\\PHPEMS\\App\\Task\\Service\\Event\\TaskDemo', 'null', 'default', 3, NULL, 300, 1, 'finished', 1785031831, 1785032282, 2, '{\"duration\":0,\"timestamp\":1785032282,\"worker_id\":\"worker_39944_6a656e5a1dc8c\"}', 1785032282, 1785032282);

-- ----------------------------
-- Table structure for x2_task_schedules
-- ----------------------------
DROP TABLE IF EXISTS `x2_task_schedules`;
CREATE TABLE `x2_task_schedules`  (
  `tsid` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `tsname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '任务名称（便于识别）',
  `tsdescribe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL COMMENT '任务描述',
  `tsuserid` bigint UNSIGNED NULL DEFAULT NULL COMMENT '创建人用户ID',
  `tsclass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '任务调用的类（如 SendEmailTask）',
  `tspayload` json NOT NULL COMMENT '类参数（JSON格式）',
  `tstype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'default' COMMENT '类别/队列（email/report/payment）',
  `tspriority` tinyint NOT NULL DEFAULT 5 COMMENT '优先级（1-10，数字越大越优先）',
  `tsattempts` tinyint UNSIGNED NOT NULL DEFAULT 3 COMMENT '重试次数',
  `tsloop` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '循环周期（如 */5 * * * * 表示每5分钟，或 forever 表示无限）',
  `tstimes` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '已经循环的次数',
  `tsenabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用（1=启用，0=禁用）',
  `tslastrun` int NULL DEFAULT 0 COMMENT '最后调度入列时间',
  `tsnextrun` int NULL DEFAULT 0 COMMENT '预计下次执行时间',
  `tstime` int NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`tsid`) USING BTREE,
  INDEX `idx_enabled_nextrun`(`tsenabled` ASC, `tsnextrun` ASC) USING BTREE,
  INDEX `idx_type`(`tstype` ASC) USING BTREE,
  INDEX `idx_userid`(`tsuserid` ASC) USING BTREE,
  INDEX `idx_tslastrun`(`tslastrun` ASC) USING BTREE,
  INDEX `idx_tstime`(`tstime` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci COMMENT = '循环任务配置表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_task_schedules
-- ----------------------------
INSERT INTO `x2_task_schedules` VALUES (2, '循环任务测试', '循环任务测试', 1, '\\PHPEMS\\App\\Task\\Service\\Event\\TaskDemo', 'null', 'default', 5, 3, '0 0 * * *', 2, 1, 1785031831, 1785031831, 1784852741);

-- ----------------------------
-- Table structure for x2_trade_order
-- ----------------------------
DROP TABLE IF EXISTS `x2_trade_order`;
CREATE TABLE `x2_trade_order`  (
  `orderid` int NOT NULL AUTO_INCREMENT,
  `ordersn` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `ordertitle` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `orderpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `orderitems` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `orderprice` decimal(10, 2) NOT NULL,
  `orderstatus` int NOT NULL,
  `orderrawprice` decimal(10, 2) NOT NULL,
  `ordercreatetime` int NOT NULL,
  `orderpaytime` int NOT NULL,
  `orderpaytype` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `orderfinishtime` int NOT NULL,
  `orderfaq` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `orderdescribe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`orderid`) USING BTREE,
  UNIQUE INDEX `ordersn`(`ordersn` ASC) USING BTREE,
  INDEX `orderprice`(`orderprice` ASC) USING BTREE,
  INDEX `orderpaytype`(`orderpaytype` ASC) USING BTREE,
  INDEX `ordercreatetime`(`ordercreatetime` ASC) USING BTREE,
  INDEX `orderpaytime`(`orderpaytime` ASC) USING BTREE,
  INDEX `orderpassport`(`orderpassport` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_trade_order
-- ----------------------------
INSERT INTO `x2_trade_order` VALUES (1, '20260827213728399', '充值积分10', '0025896gd42safx0001', '{\"itemtype\":\"recharge\",\"items\":{\"rate\":10}}', 1.00, 1, 1.00, 1787837848, 0, '', 0, '', '');

-- ----------------------------
-- Table structure for x2_user
-- ----------------------------
DROP TABLE IF EXISTS `x2_user`;
CREATE TABLE `x2_user`  (
  `userid` int NOT NULL AUTO_INCREMENT,
  `useropenid` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `userunionid` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `username` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `userpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `usersalt` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `useremail` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `userpassword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `userphoto` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `usercoin` int NOT NULL DEFAULT 0,
  `userregip` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `userregtime` int NOT NULL DEFAULT 0,
  `userlogtime` int NOT NULL DEFAULT 0,
  `userverifytime` int NULL DEFAULT NULL,
  `usergroupid` int NOT NULL DEFAULT 0,
  `manager_apps` varchar(240) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `usertruename` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `teacher_subjects` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `userprofile` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `usergender` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `userphone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `useraddress` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `userstatus` int NULL DEFAULT NULL,
  PRIMARY KEY (`userid`) USING BTREE,
  UNIQUE INDEX `useremail`(`useremail` ASC) USING BTREE,
  INDEX `username`(`username` ASC) USING BTREE,
  INDEX `userregtime`(`userregtime` ASC) USING BTREE,
  INDEX `useropenid`(`useropenid` ASC) USING BTREE,
  INDEX `userphone`(`userphone` ASC) USING BTREE,
  INDEX `userunionid`(`userunionid` ASC) USING BTREE,
  INDEX `userpassport`(`userpassport` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_user
-- ----------------------------
INSERT INTO `x2_user` VALUES (1, '', NULL, 'peadmin', '0025896gd42safx0001', NULL, '958074@163.com', '$2y$10$dVUDS6CC1UOx.Jp8f4YYz.xi3EGwoUiGOjt/cb6Tc5IIuKgH4VqNe', 'storage/attach/public/20260630/ce9ce3121a4a8fcbead542e4704b65da.jpg', 259, '127.0.0.1', 1471795200, 0, 1784602372, 1, 'a:7:{i:0;s:4:\"user\";i:1;s:7:\"content\";i:2;s:4:\"exam\";i:3;s:8:\"document\";i:4;s:6:\"course\";i:5;s:4:\"bank\";i:6;s:8:\"autoform\";}', '火眼', '', '', '男', '', '信息部', 3);
INSERT INTO `x2_user` VALUES (2, '', '', 'redrangon', '00000d69ad4e72d9e9801269', NULL, 'redrangon@163.com', '$2y$10$d1ha4mcOmPIgfePVXIfXFeXoj4xRtKuMtkXc2GapU2E6eU0cbN4j2', 'storage/attach/public/20260618/f17ce7f87ee05c1fe25f4cfd08300c5f.jpg', 0, '127.0.0.1', 1781780670, 0, 1784461378, 8, '', '火眼', '', '', '男', '', '', 0);
INSERT INTO `x2_user` VALUES (3, '', '', 'bipowang', '000ba06a730f38defa429b86', NULL, '278768688@qq.com', '$2y$10$4x3n5QGvE5dyZeP8h.XTuuspcn4KOcI.bVNTlAdrdRsvIEnoifWn.', '', 0, '127.0.0.1', 1784907061, 0, 0, 8, '', '无名', '', '', '男', '', '', 0);
INSERT INTO `x2_user` VALUES (7, '', '', 'ltester', '0045c699d234336630dd18e4', NULL, 'ltester@163.com', '$2y$10$ddC4nsrrp9HTlkY2Misfg.q8k5p07AFDMym.v76ou05rTR8fBTXQe', '', 0, '127.0.0.1', 1787836894, 0, 0, 8, '', '张三22222', '', '', '', '', '', 0);

-- ----------------------------
-- Table structure for x2_user_expense
-- ----------------------------
DROP TABLE IF EXISTS `x2_user_expense`;
CREATE TABLE `x2_user_expense`  (
  `ueid` int NOT NULL AUTO_INCREMENT,
  `uepassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ueuserid` int NULL DEFAULT NULL,
  `ueamount` int NULL DEFAULT NULL,
  `uetime` int NULL DEFAULT NULL,
  `uedescribe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ueid`) USING BTREE,
  INDEX `uepassport`(`uepassport` ASC) USING BTREE,
  INDEX `ueuserid`(`ueuserid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_user_expense
-- ----------------------------
INSERT INTO `x2_user_expense` VALUES (1, '0025896gd42safx0001', 1, -1000, 1784461368, '管理员peadmin积分充值');
INSERT INTO `x2_user_expense` VALUES (2, '0025896gd42safx0001', NULL, 30, 1784558436, '购买考场植物学');
INSERT INTO `x2_user_expense` VALUES (3, '0025896gd42safx0001', NULL, 0, 1784601804, '购买课程PHPEMS LITE');
INSERT INTO `x2_user_expense` VALUES (4, '0025896gd42safx0001', NULL, 0, 1787841258, '购买课程PHPEMS LITE');
INSERT INTO `x2_user_expense` VALUES (5, '0025896gd42safx0001', NULL, 0, 1791104505, '购买课程PHPEMS LITE');

-- ----------------------------
-- Table structure for x2_user_group
-- ----------------------------
DROP TABLE IF EXISTS `x2_user_group`;
CREATE TABLE `x2_user_group`  (
  `groupid` int NOT NULL AUTO_INCREMENT,
  `groupname` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `groupmoduleid` tinyint NOT NULL DEFAULT 0,
  `groupdescribe` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `groupright` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `groupmoduledefault` int NOT NULL DEFAULT 0,
  `groupdefault` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`groupid`) USING BTREE,
  INDEX `groupname`(`groupname` ASC, `groupmoduleid` ASC) USING BTREE,
  INDEX `groupmoduledefault`(`groupmoduledefault` ASC) USING BTREE,
  INDEX `groupdefault`(`groupdefault` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_user_group
-- ----------------------------
INSERT INTO `x2_user_group` VALUES (1, '管理员', 1, '管理员', '', 1, 0);
INSERT INTO `x2_user_group` VALUES (8, '普通用户', 9, '普通用户', '', 0, 1);
INSERT INTO `x2_user_group` VALUES (9, '教师', 12, '教师', '', 0, 0);

-- ----------------------------
-- Table structure for x2_user_log
-- ----------------------------
DROP TABLE IF EXISTS `x2_user_log`;
CREATE TABLE `x2_user_log`  (
  `ulid` int NOT NULL AUTO_INCREMENT,
  `uluserid` int NULL DEFAULT NULL,
  `ulip` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ulclient` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `ultime` int NULL DEFAULT NULL,
  PRIMARY KEY (`ulid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 52 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_user_log
-- ----------------------------
INSERT INTO `x2_user_log` VALUES (47, 1, '127.0.0.1', '电脑:Edge', 1787315361);
INSERT INTO `x2_user_log` VALUES (48, 1, '127.0.0.1', '电脑:Edge', 1787836410);
INSERT INTO `x2_user_log` VALUES (49, 1, '127.0.0.1', '电脑:Edge', 1787839152);
INSERT INTO `x2_user_log` VALUES (50, 1, '127.0.0.1', '电脑:Edge', 1791010779);
INSERT INTO `x2_user_log` VALUES (51, 1, '127.0.0.1', '电脑:Edge', 1791104399);

-- ----------------------------
-- Table structure for x2_user_money
-- ----------------------------
DROP TABLE IF EXISTS `x2_user_money`;
CREATE TABLE `x2_user_money`  (
  `umid` int NOT NULL AUTO_INCREMENT,
  `umpassport` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL,
  `umamount` int UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`umid`) USING BTREE,
  UNIQUE INDEX `umpassport`(`umpassport` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_user_money
-- ----------------------------
INSERT INTO `x2_user_money` VALUES (1, '0025896gd42safx0001', 970);

-- ----------------------------
-- Table structure for x2_user_rand_code
-- ----------------------------
DROP TABLE IF EXISTS `x2_user_rand_code`;
CREATE TABLE `x2_user_rand_code`  (
  `urcid` int NOT NULL AUTO_INCREMENT,
  `urctarget` varchar(72) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `urctype` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `urcsendtime` int NULL DEFAULT NULL,
  `urcstring` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`urcid`) USING BTREE,
  INDEX `urctarget`(`urctarget` ASC) USING BTREE,
  INDEX `urctype`(`urctype` ASC) USING BTREE,
  INDEX `urcsendtime`(`urcsendtime` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_user_rand_code
-- ----------------------------
INSERT INTO `x2_user_rand_code` VALUES (1, '278768688@qq.com', 'register', 1784906652, '874781');
INSERT INTO `x2_user_rand_code` VALUES (2, '278768688@qq.com', 'register', 1784906844, '606745');
INSERT INTO `x2_user_rand_code` VALUES (3, '278768688@qq.com', 'register', 1784906965, '709774');

-- ----------------------------
-- Table structure for x2_user_wxlogin
-- ----------------------------
DROP TABLE IF EXISTS `x2_user_wxlogin`;
CREATE TABLE `x2_user_wxlogin`  (
  `wxsid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `wxinfo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `wxtime` int NOT NULL,
  `wxtoken` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`wxsid`) USING BTREE,
  INDEX `wxtime`(`wxtime` ASC) USING BTREE,
  INDEX `wxtoken`(`wxtoken` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_520_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of x2_user_wxlogin
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
