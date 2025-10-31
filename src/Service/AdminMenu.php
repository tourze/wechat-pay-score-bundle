<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatPayScoreBundle\Entity\PostDiscount;
use WechatPayScoreBundle\Entity\PostPayment;
use WechatPayScoreBundle\Entity\ScoreOrder;

/**
 * 微信支付分管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('支付管理')) {
            $item->addChild('支付管理');
        }

        $paymentMenu = $item->getChild('支付管理');
        if (null === $paymentMenu) {
            return;
        }

        // 添加微信支付分管理子菜单
        if (null === $paymentMenu->getChild('微信支付分')) {
            $paymentMenu->addChild('微信支付分')
                ->setAttribute('icon', 'fas fa-star')
            ;
        }

        $scoreMenu = $paymentMenu->getChild('微信支付分');
        if (null === $scoreMenu) {
            return;
        }

        $scoreMenu->addChild('支付分订单')
            ->setUri($this->linkGenerator->getCurdListPage(ScoreOrder::class))
            ->setAttribute('icon', 'fas fa-receipt')
        ;

        $scoreMenu->addChild('后支付项目')
            ->setUri($this->linkGenerator->getCurdListPage(PostPayment::class))
            ->setAttribute('icon', 'fas fa-credit-card')
        ;

        $scoreMenu->addChild('优惠项目')
            ->setUri($this->linkGenerator->getCurdListPage(PostDiscount::class))
            ->setAttribute('icon', 'fas fa-tag')
        ;
    }
}
