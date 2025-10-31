# wechat-pay-score-bundle

[![Packagist Version](https://img.shields.io/packagist/v/tourze/wechat-pay-score-bundle.svg)](https://packagist.org/packages/tourze/wechat-pay-score-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-pay-score-bundle.svg)](https://github.com/tourze/wechat-pay-score-bundle/blob/master/LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-pay-score-bundle.svg)](https://packagist.org/packages/tourze/wechat-pay-score-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/wechat-pay-score-bundle.svg)](https://packagist.org/packages/tourze/wechat-pay-score-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/wechat-pay-score-bundle/ci.yml?branch=master)](https://github.com/tourze/wechat-pay-score-bundle/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/wechat-pay-score-bundle)](https://codecov.io/gh/tourze/wechat-pay-score-bundle)

[English](README.md) | [中文](README.zh-CN.md)

WeChat Pay Score Symfony Bundle for integrating WeChat Pay Score services, 
supporting create, query, complete, and cancel Pay Score orders.

## Table of Contents

- [Quick Start](#quick-start)
  - [Installation](#installation)
  - [1. Register Bundle](#1-register-bundle)
  - [2. Database Configuration](#2-database-configuration)
  - [3. Basic Usage](#3-basic-usage)
- [Features](#features)
- [Core Components](#core-components)
  - [Entities](#entities)
  - [Enums](#enums)
  - [Services](#services)
  - [Events](#events)
- [Configuration Examples](#configuration-examples)
  - [Entity Relationship Configuration](#entity-relationship-configuration)
  - [Event Listener Configuration](#event-listener-configuration)
- [API Reference](#api-reference)
  - [Order States](#order-states)
  - [Callback Interface](#callback-interface)
- [Requirements](#requirements)
- [Advanced Usage](#advanced-usage)
  - [Custom Event Listeners](#custom-event-listeners)
  - [Order State Management](#order-state-management)
  - [Post-Payment and Discount Configuration](#post-payment-and-discount-configuration)
- [Testing](#testing)
- [Documentation](#documentation)
- [License](#license)

## Quick Start

### Installation

```bash
composer require tourze/wechat-pay-score-bundle
```

### 1. Register Bundle

Add to `config/bundles.php`:

```php
return [
    // ...
    WechatPayScoreBundle\WechatPayScoreBundle::class => ['all' => true],
];
```

### 2. Database Configuration

Run migrations to create necessary tables:

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### 3. Basic Usage

```php
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Enum\ScoreOrderState;

// Create Pay Score order
$scoreOrder = new ScoreOrder();
$scoreOrder->setOutTradeNo('20241201001')
    ->setAppId('your_app_id')
    ->setServiceId('your_service_id')
    ->setServiceIntroduction('Service description')
    ->setRiskFundName('Risk fund')
    ->setRiskFundAmount(10000)
    ->setNotifyUrl('https://example.com/notify')
    ->setStartTime('20241201120000')
    ->setState(ScoreOrderState::CREATED);

$entityManager->persist($scoreOrder);
$entityManager->flush();
```

## Features

- 🎯 **Pay Score Order Management** - Support create, query, complete, and cancel Pay Score orders
- 📊 **Order Status Tracking** - Complete order status management (Created, Doing, Done, Revoked, Expired)
- 💰 **Payment Handling** - Support post-payment and discount information management
- 🔔 **Callback Processing** - Built-in callback controller for WeChat Pay Score notifications
- 📱 **Mini Program Support** - Support jumping to WeChat Mini Program for Pay Score operations
- 🔒 **Security & Reliability** - Integrated with WeChat Pay official SDK for secure transactions

## Core Components

### Entities

- `ScoreOrder` - Pay Score order entity
- `PostPayment` - Post-payment information entity
- `PostDiscount` - Discount information entity

### Enums

- `ScoreOrderState` - Order state enumeration

### Services

- `AttributeControllerLoader` - Attribute controller loader
- `CallbackController` - Callback processing controller

### Events

- `ScoreOrderCallbackEvent` - Pay Score order callback event
- `ScoreOrderListener` - Pay Score order event listener

## Configuration Examples

### Entity Relationship Configuration

```php
// Add Pay Score order relationship in your user entity
class User
{
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ScoreOrder::class)]
    private Collection $scoreOrders;
}
```

### Event Listener Configuration

```php
// Listen to Pay Score order status changes
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
        // Handle order status changes
    }
}
```

## API Reference

### Order States

| State | Description |
|-------|-------------|
| `CREATED` | Created |
| `DOING` | In Progress |
| `DONE` | Completed |
| `REVOKED` | Cancelled |
| `EXPIRED` | Expired |

### Callback Interface

The system automatically registers callback route: `/wechat-pay-score/callback`

## Requirements

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- WeChat Pay Official SDK

## Advanced Usage

### Custom Event Listeners

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
        
        // Custom business logic
        switch ($scoreOrder->getState()) {
            case ScoreOrderState::DONE:
                // Handle completed order
                break;
            case ScoreOrderState::REVOKED:
                // Handle cancelled order
                break;
        }
    }
}
```

### Order State Management

```php
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Enum\ScoreOrderState;

// Complete an order
$scoreOrder->setState(ScoreOrderState::DONE);
$scoreOrder->setEndTime(date('YmdHis'));
$scoreOrder->setTotalAmount(10000);
$entityManager->flush();

// Cancel an order
$scoreOrder->setCancelReason('User requested cancellation');
$entityManager->remove($scoreOrder);
$entityManager->flush();
```

### Post-Payment and Discount Configuration

```php
use WechatPayScoreBundle\Entity\PostPayment;
use WechatPayScoreBundle\Entity\PostDiscount;

// Add post-payment information
$postPayment = new PostPayment();
$postPayment->setName('Service Fee')
    ->setAmount(5000)
    ->setDescription('Basic service fee')
    ->setCount(1);
    
$scoreOrder->addPostPayment($postPayment);

// Add discount information
$postDiscount = new PostDiscount();
$postDiscount->setName('New User Discount')
    ->setAmount(1000)
    ->setDescription('First-time user discount')
    ->setCount(1);
    
$scoreOrder->addPostDiscount($postDiscount);
```

## Testing

```bash
# Run tests
./vendor/bin/phpunit packages/wechat-pay-score-bundle/tests

# Run code analysis
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/wechat-pay-score-bundle
```

## Documentation

- [WeChat Pay Score API Documentation]
  (https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter6_1_1.shtml)
- [WeChat Pay Score Business Introduction]
  (https://pay.weixin.qq.com/wiki/doc/apiv3/open/pay/chapter2_8.shtml)
- [Symfony Bundle Development Guide]
  (https://symfony.com/doc/current/bundles.html)

## License

MIT License. See [LICENSE](LICENSE) file for details.
