<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use WechatPayScoreBundle\Entity\PostPayment;
use WechatPayScoreBundle\Entity\ScoreOrder;

class PostPaymentFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const POST_PAYMENT_REFERENCE_PREFIX = 'post_payment_';
    public const POST_PAYMENT_COUNT = 30;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::POST_PAYMENT_COUNT; ++$i) {
            $postPayment = $this->createPostPayment();
            $manager->persist($postPayment);
            $this->addReference(self::POST_PAYMENT_REFERENCE_PREFIX . $i, $postPayment);
        }

        $manager->flush();
    }

    private function createPostPayment(): PostPayment
    {
        $scoreOrderIndex = $this->faker->numberBetween(0, ScoreOrderFixtures::SCORE_ORDER_COUNT - 1);
        $scoreOrder = $this->getReference(ScoreOrderFixtures::SCORE_ORDER_REFERENCE_PREFIX . $scoreOrderIndex, ScoreOrder::class);

        $postPayment = new PostPayment();
        $postPayment->setScoreOrder($scoreOrder);
        $postPayment->setAmount($this->faker->numberBetween(100, 10000));

        if ($this->faker->boolean(80)) {
            $postPayment->setName($this->faker->sentence(2));
        }

        if ($this->faker->boolean(70)) {
            $postPayment->setDescription($this->faker->text(30));
        }

        if ($this->faker->boolean(60)) {
            $postPayment->setCount($this->faker->numberBetween(1, 10));
        }

        $createTime = $this->faker->dateTimeBetween('-30 days', 'now');
        $postPayment->setCreateTime(\DateTimeImmutable::createFromMutable($createTime));

        return $postPayment;
    }

    public function getDependencies(): array
    {
        return [
            ScoreOrderFixtures::class,
        ];
    }
}
