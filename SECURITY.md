# Security Policy

## Supported Versions

| Version | Supported          | 说明 |
| ------- | ------------------ | ---- |
| 12.0.x  | :white_check_mark: | 当前主线，接收安全修复 |
| 11.x    | :x:                | 已停止维护，请升级到 12.0.x |
| < 11    | :x:                | 已停止维护，请升级到 12.0.x |

仅最新的 12.0.x 版本接收安全更新。报告前请先在最新版本或对应 commit 上复现；旧版本的问题可能要求先升级再验证。

## Reporting a Vulnerability

**请不要为安全漏洞创建公开 Issue、PR 或 Discussion。**

首选方式：GitHub 私有漏洞报告

- 打开仓库的 **Security** 标签页
- 点击 **Report a vulnerability**
- 或直接访问：`https://github.com/redrangon/phpemslite/security/advisories/new`

备选方式：邮件 `<SECURITY_EMAIL>`（可选 PGP：`<PGP_FINGERPRINT>`）

报告中请尽量包含：

- 受影响的版本 / commit，以及部署环境（PHP 版本、Web 服务器、扩展、是否启用 Worker）
- 漏洞类型（如已知 CWE）和受影响的接口 / 文件
- 复现步骤和最小 PoC（请使用非破坏性 payload）
- 影响范围与前置条件（是否需要登录、是否依赖特定配置）
- 是否希望公开致谢，以及联系方式

## What to Expect

- **3 个工作日内**确认收到报告
- **7 个工作日内**完成初步分诊（是否可复现、影响范围、严重级别）
- 我们会与报告人确认修复与披露时间表：
  - 严重 / 高危：目标 7–30 天修复
  - 中危：目标 30–90 天修复
  - 低危：随下一个版本修复
- 修复发布前，Advisory 保持私有；采用协调披露
- **接受**：通过 GitHub CNA 申请 CVE，发布 GitHub Security Advisory，并在报告人同意的情况下公开致谢
- **拒绝 / 不在范围**：会说明原因；报告人可请求二次评估

## Scope

**在范围内：**

- PHPEMS Lite 后端（`app/`、`lib/`、`boot/`、`task/`）
- 前端（`frontend/`、`public/`）及默认配置
- 认证/授权、文件上传、任务队列与 Worker、SQL 处理、模板引擎
- 支付、人脸识别、AI 等集成中的安全问题

**不在范围内：**

- 未经书面授权的公开演示站测试（包括 `https://lite.phpems.cn/`）
- 社会工程、物理访问、拒绝服务/邮件轰炸
- 纯第三方依赖问题（请先报告给上游，并在本仓库说明影响）
- 需要已攻陷管理员/主机才能成立的场景
- 已停止维护版本上的问题（请先升级到 12.0.x）

## Safe Harbor

我们支持善意的安全研究。测试时请：

- 只测试你自己的部署，或获得书面授权的测试实例
- 不访问、修改、导出不属于你的数据；使用测试账号，并对个人信息脱敏
- 不破坏服务、不删除数据、不留下 webshell 或后门
- 私下报告，并给予合理的修复时间

对于遵守本政策的善意研究，我们不会采取法律行动。

## Deployment Hardening

建议部署方至少完成以下加固：

- 修改默认/种子账号口令，删除演示账号
- 关闭 `display_errors`；日志移出 Web 根目录
- 禁止 Web 访问 `storage/`、`task/`、`phpemsvue.sql`
- 上传接口恢复鉴权，限制大小，图片重新编码，存储移出 Web 根目录
- Worker/Scheduler 仅通过 CLI/supervisor 启动；对任务类使用白名单
- 移除源码中的硬编码密钥，改用环境变量/密钥管理，并轮换已泄露密钥
- 及时更新 PHP 与依赖；在可行时禁用 `proc_open`、`shell_exec` 等危险函数

## PGP

```
<可选：PGP 公钥指纹或链接>
```

## Credits

感谢所有遵循负责任披露原则的报告人。经确认的漏洞会在 GitHub Security Advisory 中致谢（除非报告人要求匿名）。
