# AppManagement - App升级管理后台

基于 ThinkPHP 6.x 开发的 App 升级管理系统，为 XUpdate 提供完整的版本管理和灰度更新能力。

## 功能特性

### 1. 用户管理
- ✅ 用户登录/登出
- ✅ 用户注册
- ✅ 权限管理（管理员/普通用户）
- ✅ 用户信息管理
- ✅ JWT Token认证

### 2. 应用包管理
- ✅ APK 文件上传
- ✅ 自动解析包名、版本号、应用名称、图标
- ✅ 多应用管理
- ✅ 应用信息编辑
- ✅ 应用权限控制

### 3. 版本管理
- ✅ 多版本管理
- ✅ 版本状态设置（正常更新/强制更新/停用）
- ✅ 更新日志管理
- ✅ 版本发布时间管理
- ✅ 版本删除功能

### 4. 权限控制
- ✅ 基于角色的访问控制 (RBAC)
- ✅ 多用户应用包共享
- ✅ 用户权限验证
- ✅ 操作日志记录

### 5. 渠道版本管理
- ✅ Flavor 渠道版本支持
- ✅ 渠道版本对应
- ✅ 渠道差异化配置

### 6. 灰度更新
- ✅ 按设备号灰度更新
- ✅ 按百分比灰度更新
- ✅ 灰度策略管理
- ✅ 灰度进度统计

### 7. 版本检查 API
- ✅ 客户端版本检查接口
- ✅ 设备序列号记录
- ✅ 下载次数统计
- ✅ 支持多渠道查询

### 8. 文件下载
- ✅ APK 文件下载
- ✅ 下载次数记录
- ✅ 下载日志统计

## 环境要求

- PHP >= 7.4
- ThinkPHP 6.x
- MySQL 5.7+
- Composer

## 快速开始

### 1. 克隆项目

```bash
git clone https://github.com/czhongm/AppManagement.git
cd AppManagement
```

### 2. 安装依赖

```bash
composer install
```

### 3. 配置环境

```bash
cp .env.example .env
# 编辑 .env 文件，配置数据库等信息
```

### 4. 创建数据库表

```bash
php think migrate:run
```

### 5. 创建管理员账户

```bash
php think seed:run
```

### 6. 启动服务

```bash
php think serve
```

服务将运行在 `http://localhost:8000`

## 项目结构

```
AppManagement/
├── app/
│   ├── controller/          # 控制器
│   │   ├── Account.php      # 用户管理
│   │   ├── App.php          # 应用包管理
│   │   ├── Version.php      # 版本管理
│   │   ├── Update.php       # 灰度更新
│   │   └── Check.php        # 版本检查
│   ├── model/               # 数据模型
│   │   ├── Account.php
│   │   ├── App.php
│   │   ├── Version.php
│   │   ├── GrayStrategy.php
│   │   └── DownloadRecord.php
│   ├── validate/            # 验证类
│   ├── service/             # 业务逻辑
│   │   ├── AccountService.php
│   │   ├── AppService.php
│   │   ├── VersionService.php
│   │   ├── GrayUpdateService.php
│   │   └── ApkParseService.php
│   ├── middleware/          # 中间件
│   │   ├── Auth.php         # JWT认证
│   │   └── Permission.php   # 权限检查
│   └── utils/               # 工具类
├── config/                  # 配置文件
│   ├── database.php
│   ├── app.php
│   └── auth.php
├── database/                # 数据库
│   ├── migrations/          # 迁移文件
│   └── seeders/             # 数据填充
├── public/                  # Web根目录
│   ├── uploads/             # 上传文件
│   │   ├── apk/
│   │   └── icons/
│   └── index.php
├── route/                   # 路由
│   └── api.php
├── composer.json
├── .env.example
└── README.md
```

## API 文档

### 用户管理

#### 登录
```
POST /api/account/login

请求体:
{
  "loginName": "admin",
  "password": "123456"
}

响应:
{
  "code": 0,
  "msg": "登录成功",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
      "id": 1,
      "loginName": "admin",
      "nick": "管理员",
      "authority": "admin"
    }
  }
}
```

#### 注册
```
POST /api/account/register

请求体:
{
  "loginName": "user",
  "password": "123456",
  "nick": "用户名"
}
```

#### 获取用户信息
```
GET /api/account/info
```

#### 用户列表（分页）
```
GET /api/account/list?page=1&pageSize=10
```

### 应用管理

#### 应用列表
```
GET /api/app/list?page=1&pageSize=10
```

#### 上传 APK
```
POST /api/app/upload

Content-Type: multipart/form-data
参数: file (APK文件)
```

#### 应用详情
```
GET /api/app/detail/{id}
```

#### 编辑应用
```
POST /api/app/update

请求体:
{
  "id": 1,
  "appName": "应用名称",
  "description": "应用描述"
}
```

#### 删除应用
```
POST /api/app/delete

请求体:
{
  "id": 1
}
```

### 版本管理

#### 版本列表
```
GET /api/version/list?appId=1&page=1&pageSize=10
```

#### 创建版本
```
POST /api/version/create

请求体:
{
  "appId": 1,
  "versionName": "1.0.0",
  "versionCode": 1,
  "modifyContent": "更新日志",
  "updateStatus": 1
}
```

#### 更新版本
```
POST /api/version/update

请求体:
{
  "id": 1,
  "versionName": "1.0.1",
  "versionCode": 2,
  "modifyContent": "更新日志",
  "updateStatus": 1
}
```

#### 删除版本
```
POST /api/version/delete

请求体:
{
  "id": 1
}
```

### 灰度更新

#### 创建灰度策略
```
POST /api/update/grayStrategy

请求体:
{
  "versionId": 1,
  "strategyType": "percentage",  // percentage|device|user
  "strategyValue": "10",          // 10% 或设备/用户列表
  "description": "策略描述"
}
```

#### 灰度策略列表
```
GET /api/update/grayStrategies?versionId=1
```

### 版本检查（客户端调用）

#### 检查更新
```
POST /api/check/update

请求体:
{
  "appKey": "com.example.app",
  "versionCode": 1,
  "deviceId": "device_id",
  "channel": "official"  # 可选
}

响应:
{
  "code": 0,
  "msg": "成功",
  "data": {
    "hasUpdate": true,
    "isMustUpdate": false,
    "versionCode": 2,
    "versionName": "1.0.1",
    "updateContent": "更新日志",
    "downloadUrl": "https://...",
    "apkSize": 51200000,
    "md5": "..."
  }
}
```

#### 下载 APK
```
GET /api/download/{versionId}
```

## 默认账户

初始化后的默认管理员账户：
- 用户名: `admin`
- 密码: `admin123`

**首次登录后请立即修改密码！**

## 数据库表结构

### accounts 表 - 用户表
```sql
- id: 主键
- loginName: 登录名
- password: 密码（加密）
- nick: 昵称
- authority: 权限（admin|editor）
- avatar: 头像URL
- phone: 手机号
- address: 地址
- registerTime: 注册时间
- updateTime: 更新时间
```

### apps 表 - 应用表
```sql
- id: 主键
- appKey: 应用唯一标识（包名）
- appName: 应用名称
- description: 应用描述
- icon: 应用图标URL
- userId: 创建者ID
- createTime: 创建时间
- updateTime: 更新时间
```

### versions 表 - 版本表
```sql
- id: 主键
- appId: 应用ID
- versionName: 版本名称
- versionCode: 版本号
- modifyContent: 更新日志
- apkUrl: APK文件URL
- apkMd5: APK文件MD5
- apkSize: APK文件大小
- updateStatus: 更新状态（1:正常|2:强制|3:停用）
- uploadTime: 上传时间
- createTime: 创建时间
- updateTime: 更新时间
```

### gray_strategies 表 - 灰度策略表
```sql
- id: 主键
- versionId: 版本ID
- strategyType: 策略类型（percentage|device|user）
- strategyValue: 策略值
- description: 描述
- createTime: 创建时间
- updateTime: 更新时间
```

### download_records 表 - 下载记录表
```sql
- id: 主键
- versionId: 版本ID
- deviceId: 设备ID
- userId: 用户ID
- ip: IP地址
- userAgent: User Agent
- downloadTime: 下载时间
```

## 许可证

MIT License

## 作者

czhongm
