<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
abstract class AppFixtures extends Fixture
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('zh_CN');
    }

    abstract public function load(ObjectManager $manager): void;

    protected function generateWechatMerchantId(): string
    {
        return $this->faker->numerify('16#######');
    }

    protected function generateAppId(): string
    {
        return 'wx' . $this->faker->regexify('[a-z0-9]{16}');
    }

    protected function generateServiceId(): string
    {
        return $this->faker->numerify('5#########');
    }

    protected function generateOutTradeNo(): string
    {
        return $this->faker->date('Ymd') . $this->faker->numerify('########');
    }

    protected function generateWechatTimeFormat(): string
    {
        return $this->faker->dateTimeBetween('-30 days', '+30 days')->format('YmdHis');
    }

    protected function generateWechatOrderId(): string
    {
        return $this->faker->numerify('5#########');
    }

    protected function generateOpenId(): string
    {
        return 'o' . $this->faker->regexify('[a-zA-Z0-9]{26}');
    }

    protected function generateNotifyUrl(): string
    {
        return $this->faker->url() . '/notify/wechat-pay-score';
    }
}
