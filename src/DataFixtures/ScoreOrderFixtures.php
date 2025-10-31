<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use WechatPayBundle\DataFixtures\MerchantFixtures;
use WechatPayBundle\Entity\Merchant;
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Enum\ScoreOrderState;

class ScoreOrderFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const SCORE_ORDER_REFERENCE_PREFIX = 'score_order_';
    public const SCORE_ORDER_COUNT = 20;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::SCORE_ORDER_COUNT; ++$i) {
            $scoreOrder = $this->createScoreOrder();
            $manager->persist($scoreOrder);
            $this->addReference(self::SCORE_ORDER_REFERENCE_PREFIX . $i, $scoreOrder);
        }

        $manager->flush();
    }

    private function createScoreOrder(): ScoreOrder
    {
        $scoreOrder = new ScoreOrder();

        $this->setBasicScoreOrderData($scoreOrder);
        $this->setOptionalTimeData($scoreOrder);
        $this->setOptionalLocationData($scoreOrder);
        $this->setOptionalPaymentData($scoreOrder);

        $createTime = $this->faker->dateTimeBetween('-30 days', 'now');
        $scoreOrder->setCreateTime(\DateTimeImmutable::createFromMutable($createTime));

        return $scoreOrder;
    }

    private function setBasicScoreOrderData(ScoreOrder $scoreOrder): void
    {
        $merchantReference = $this->faker->randomElement([
            MerchantFixtures::TEST_MERCHANT_REFERENCE,
            MerchantFixtures::DEMO_MERCHANT_REFERENCE,
        ]);
        assert(is_string($merchantReference), 'Merchant reference must be a string');
        $merchant = $this->getReference($merchantReference, Merchant::class);

        $scoreOrder->setMerchant($merchant);
        $scoreOrder->setOutTradeNo($this->generateOutTradeNo());
        $scoreOrder->setAppId($this->generateAppId());
        $scoreOrder->setServiceId($this->generateServiceId());
        $scoreOrder->setServiceIntroduction($this->faker->text(20));
        $scoreOrder->setStartTime($this->generateWechatTimeFormat());
        $scoreOrder->setRiskFundName($this->faker->word() . '风险保证金');
        $scoreOrder->setRiskFundAmount($this->faker->numberBetween(1000, 50000));
        $scoreOrder->setNotifyUrl($this->generateNotifyUrl());
        $state = $this->faker->randomElement(ScoreOrderState::cases());
        assert($state instanceof ScoreOrderState, 'State must be a ScoreOrderState instance');
        $scoreOrder->setState($state);
    }

    private function setOptionalTimeData(ScoreOrder $scoreOrder): void
    {
        if ($this->faker->boolean(70)) {
            $scoreOrder->setStartTimeRemark($this->faker->text(20));
        }

        if ($this->faker->boolean(60)) {
            $scoreOrder->setEndTime($this->generateWechatTimeFormat());
            $scoreOrder->setEndTimeRemark($this->faker->text(20));
        }
    }

    private function setOptionalLocationData(ScoreOrder $scoreOrder): void
    {
        if ($this->faker->boolean(80)) {
            $scoreOrder->setStartLocation($this->faker->address());
        }

        if ($this->faker->boolean(70)) {
            $scoreOrder->setEndLocation($this->faker->address());
        }

        if ($this->faker->boolean(60)) {
            $scoreOrder->setRiskFundDescription($this->faker->text(30));
        }

        if ($this->faker->boolean(50)) {
            $scoreOrder->setAttach($this->faker->text(100));
        }

        if ($this->faker->boolean(80)) {
            $scoreOrder->setOpenId($this->generateOpenId());
        }

        if ($this->faker->boolean(40)) {
            $scoreOrder->setNeedUserConfirm($this->faker->boolean());
        }
    }

    private function setOptionalPaymentData(ScoreOrder $scoreOrder): void
    {
        if ($this->faker->boolean(70)) {
            $scoreOrder->setStateDescription($this->faker->text(32));
        }

        if ($this->faker->boolean(60)) {
            $scoreOrder->setOrderId($this->generateWechatOrderId());
        }

        if ($this->faker->boolean(50)) {
            $scoreOrder->setPackage($this->faker->text(100));
        }

        if ($this->faker->boolean(70)) {
            $scoreOrder->setTotalAmount($this->faker->numberBetween(100, 10000));
        }

        if ($this->faker->boolean(60)) {
            $scoreOrder->setNeedCollection($this->faker->boolean());
        }

        if ($this->faker->boolean(30)) {
            $scoreOrder->setCollection([
                'name' => $this->faker->word(),
                'amount' => $this->faker->numberBetween(100, 5000),
                'description' => $this->faker->text(50),
            ]);
        }

        if ($this->faker->boolean(20)) {
            $scoreOrder->setCancelReason($this->faker->text(30));
        }

        if ($this->faker->boolean(15)) {
            $scoreOrder->setModifyPriceReason($this->faker->text(50));
        }
    }

    public function getDependencies(): array
    {
        return [
            MerchantFixtures::class,
        ];
    }
}
