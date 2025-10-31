<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatPayScoreBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // Setup for AdminMenu tests
    }

    public function testInvokeAddsMenuItems(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        // 验证菜单结构
        $paymentMenu = $rootItem->getChild('支付管理');
        self::assertNotNull($paymentMenu);

        $wechatMenu = $paymentMenu->getChild('微信支付分');
        self::assertNotNull($wechatMenu);

        // 验证子菜单项
        $scoreOrderMenu = $wechatMenu->getChild('支付分订单');
        self::assertNotNull($scoreOrderMenu);

        $postPaymentMenu = $wechatMenu->getChild('后支付项目');
        self::assertNotNull($postPaymentMenu);

        $postDiscountMenu = $wechatMenu->getChild('优惠项目');
        self::assertNotNull($postDiscountMenu);
    }

    public function testInvokeWithExistingPaymentMenu(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        // 预先创建支付管理菜单
        $rootItem->addChild('支付管理');

        $adminMenu->__invoke($rootItem);

        // 验证菜单仍然正确创建
        $paymentMenu = $rootItem->getChild('支付管理');
        self::assertNotNull($paymentMenu);

        $wechatMenu = $paymentMenu->getChild('微信支付分');
        self::assertNotNull($wechatMenu);

        // 验证微信支付分菜单图标
        self::assertEquals('fas fa-star', $wechatMenu->getAttribute('icon'));
    }

    public function testInvokeWithExistingWechatPayScoreMenu(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        // 预先创建支付管理和微信支付分菜单
        $paymentMenu = $rootItem->addChild('支付管理');
        $paymentMenu->addChild('微信支付分');

        $adminMenu->__invoke($rootItem);

        // 验证菜单仍然正确
        $wechatMenu = $paymentMenu->getChild('微信支付分');
        self::assertNotNull($wechatMenu);

        // 验证子菜单项是否正确添加
        $scoreOrderMenu = $wechatMenu->getChild('支付分订单');
        self::assertNotNull($scoreOrderMenu);
        self::assertEquals('fas fa-receipt', $scoreOrderMenu->getAttribute('icon'));

        $postPaymentMenu = $wechatMenu->getChild('后支付项目');
        self::assertNotNull($postPaymentMenu);
        self::assertEquals('fas fa-credit-card', $postPaymentMenu->getAttribute('icon'));

        $postDiscountMenu = $wechatMenu->getChild('优惠项目');
        self::assertNotNull($postDiscountMenu);
        self::assertEquals('fas fa-tag', $postDiscountMenu->getAttribute('icon'));
    }

    public function testMenuItemsHaveCorrectUris(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        $paymentMenu = $rootItem->getChild('支付管理');
        self::assertNotNull($paymentMenu);

        $wechatMenu = $paymentMenu->getChild('微信支付分');
        self::assertNotNull($wechatMenu);

        // 验证子菜单项有URI
        $scoreOrderMenu = $wechatMenu->getChild('支付分订单');
        self::assertNotNull($scoreOrderMenu);
        self::assertNotEmpty($scoreOrderMenu->getUri());

        $postPaymentMenu = $wechatMenu->getChild('后支付项目');
        self::assertNotNull($postPaymentMenu);
        self::assertNotEmpty($postPaymentMenu->getUri());

        $postDiscountMenu = $wechatMenu->getChild('优惠项目');
        self::assertNotNull($postDiscountMenu);
        self::assertNotEmpty($postDiscountMenu->getUri());
    }

    public function testMenuStructureIsCorrect(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        // 验证菜单层级结构
        $paymentMenu = $rootItem->getChild('支付管理');
        self::assertNotNull($paymentMenu);

        $wechatMenu = $paymentMenu->getChild('微信支付分');
        self::assertNotNull($wechatMenu);

        // 验证微信支付分菜单下有3个子项
        $children = $wechatMenu->getChildren();
        self::assertCount(3, $children);

        // 验证子菜单名称
        $childNames = array_keys($children);
        self::assertContains('支付分订单', $childNames);
        self::assertContains('后支付项目', $childNames);
        self::assertContains('优惠项目', $childNames);
    }
}
