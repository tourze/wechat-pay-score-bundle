# wechat-pay-score-bundle

[![Packagist Version](https://img.shields.io/packagist/v/tourze/wechat-pay-score-bundle.svg)](https://packagist.org/packages/tourze/wechat-pay-score-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-pay-score-bundle.svg)](https://github.com/tourze/wechat-pay-score-bundle/blob/master/LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-pay-score-bundle.svg)](https://packagist.org/packages/tourze/wechat-pay-score-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/wechat-pay-score-bundle.svg)](https://packagist.org/packages/tourze/wechat-pay-score-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/wechat-pay-score-bundle/ci.yml?branch=master)](https://github.com/tourze/wechat-pay-score-bundle/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/wechat-pay-score-bundle)](https://codecov.io/gh/tourze/wechat-pay-score-bundle)

[English](README.md) | [中文](README.zh-CN.md)

微信支付分 Symfony Bundle，用于集成微信支付分服务，
支持创建、查询、完成、撤销支付分订单等操作。

## 目录

- [Quick Start](#quick-start)
  - [Installation](#installation)
  - [1. 注册 Bundle](#1-注册-bundle)
  - [2. 配置数据库](#2-配置数据库)
  - [3. 基本使用](#3-基本使用)
- [功能特性](#功能特性)
- [核心组件](#核心组件)
  - [实体类](#实体类)
  - [枚举](#枚举)
  - [服务](#服务)
  - [事件](#事件)
- [配置示例](#配置示例)
  - [实体关系配置](#实体关系配置)
  - [事件监听器配置](#事件监听器配置)
- [API 参考](#api-参考)
  - [订单状态](#订单状态)
  - [回调接口](#回调接口)
- [依赖要求](#依赖要求)
- [Advanced Usage](#advanced-usage)
  - [自定义事件监听器](#自定义事件监听器)
  - [订单状态管理](#订单状态管理)
  - [后付费和折扣配置](#后付费和折扣配置)
- [测试](#测试)
- [参考文档](#参考文档)
- [License](#license)

## Quick Start

### Installation

```bash
composer require tourze/wechat-pay-score-bundle
```

### 1. 注册 Bundle

在 `config/bundles.php` 中添加：

```php
return [
    // ...
    WechatPayScoreBundle\WechatPayScoreBundle::class => ['all' => true],
];
```

### 2. 配置数据库

运行迁移创建必要的数据表：

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### 3. 基本使用

```php
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Enum\ScoreOrderState;

// 创建支付分订单
$scoreOrder = new ScoreOrder();
$scoreOrder->setOutTradeNo('20241201001')
    ->setAppId('your_app_id')
    ->setServiceId('your_service_id')
    ->setServiceIntroduction('服务介绍')
    ->setRiskFundName('风险金')
    ->setRiskFundAmount(10000)
    ->setNotifyUrl('https://example.com/notify')
    ->setStartTime('20241201120000')
    ->setState(ScoreOrderState::CREATED);

$entityManager->persist($scoreOrder);
$entityManager->flush();
```

## 功能特性

- 🎯 **支付分订单管理** - 支持创建、查询、完成、撤销支付分订单
- 📊 **订单状态跟踪** - 完整的订单状态管理（已创建、进行中、已完成、取消服务、已失效）
- 💰 **费用处理** - 支持后付费和折扣信息管理
- 🔔 **回调处理** - 内置回调控制器处理微信支付分通知
- 📱 **小程序支持** - 支持跳转微信小程序完成支付分操作
- 🔒 **安全可靠** - 集成微信支付官方 SDK，确保交易安全

## 核心组件

### 实体类

- `ScoreOrder` - 支付分订单实体
- `PostPayment` - 后付费信息实体
- `PostDiscount` - 折扣信息实体

### 枚举

- `ScoreOrderState` - 订单状态枚举

### 服务

- `AttributeControllerLoader` - 属性控制器加载器
- `CallbackController` - 回调处理控制器

### 事件

- `ScoreOrderCallbackEvent` - 支付分订单回调事件
- `ScoreOrderListener` - 支付分订单事件监听器

## 配置示例

### 实体关系配置

```php
// 在你的用户实体中添加支付分订单关系
class User
{
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ScoreOrder::class)]
    private Collection $scoreOrders;
}
```

### 事件监听器配置

```php
// 监听支付分订单状态变化
class ScoreOrderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ScoreOrderCallbackEvent::class => 'onScoreOrderCallback',
        ];
    }

    public function onScoreOrderCallback(ScoreOrderCallbackEvent $event): void
    {
        $scoreOrder = $event->getScoreOrder();
        // 处理订单状态变化
    }
}
```

## API 参考

### 订单状态

| 状态 | 描述 |
|------|------|
| `CREATED` | 已创建 |
| `DOING` | 进行中 |
| `DONE` | 已完成 |
| `REVOKED` | 取消服务 |
| `EXPIRED` | 已失效 |

### 回调接口

系统会自动注册回调路由：`/wechat-pay-score/callback`

## 依赖要求

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- 微信支付官方 SDK

## Advanced Usage

### 自定义事件监听器

```php
use WechatPayScoreBundle\Event\ScoreOrderCallbackEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomScoreOrderListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ScoreOrderCallbackEvent::class => 'onScoreOrderCallback',
        ];
    }

    public function onScoreOrderCallback(ScoreOrderCallbackEvent $event): void
    {
        $scoreOrder = $event->getScoreOrder();
        $callbackData = $event->getCallbackData();
        
        // 自定义业务逻辑
        switch ($scoreOrder->getState()) {
            case ScoreOrderState::DONE:
                // 处理已完成订单
                break;
            case ScoreOrderState::REVOKED:
                // 处理已取消订单
                break;
        }
    }
}
```

### 订单状态管理

```php
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Enum\ScoreOrderState;

// 完成订单
$scoreOrder->setState(ScoreOrderState::DONE);
$scoreOrder->setEndTime(date('YmdHis'));
$scoreOrder->setTotalAmount(10000);
$entityManager->flush();

// 取消订单
$scoreOrder->setCancelReason('用户主动取消');
$entityManager->remove($scoreOrder);
$entityManager->flush();
```

### 后付费和折扣配置

```php
use WechatPayScoreBundle\Entity\PostPayment;
use WechatPayScoreBundle\Entity\PostDiscount;

// 添加后付费信息
$postPayment = new PostPayment();
$postPayment->setName('服务费')
    ->setAmount(5000)
    ->setDescription('基础服务费')
    ->setCount(1);
    
$scoreOrder->addPostPayment($postPayment);

// 添加折扣信息
$postDiscount = new PostDiscount();
$postDiscount->setName('新用户折扣')
    ->setAmount(1000)
    ->setDescription('首次使用折扣')
    ->setCount(1);
    
$scoreOrder->addPostDiscount($postDiscount);
```

## 测试

```bash
# 运行测试
./vendor/bin/phpunit packages/wechat-pay-score-bundle/tests

# 运行代码分析
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/wechat-pay-score-bundle
```

## 参考文档

- [微信支付分 API 文档]
  (https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter6_1_1.shtml)
- [微信支付分业务介绍]
  (https://pay.weixin.qq.com/wiki/doc/apiv3/open/pay/chapter2_8.shtml)
- [Symfony Bundle 开发指南]
  (https://symfony.com/doc/current/bundles.html)

## License

MIT License. 详情请参阅 [LICENSE](LICENSE) 文件。
