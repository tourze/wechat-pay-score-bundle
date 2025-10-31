<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use WechatPayScoreBundle\Entity\PostDiscount;
use WechatPayScoreBundle\Entity\ScoreOrder;

class PostDiscountFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const POST_DISCOUNT_REFERENCE_PREFIX = 'post_discount_';
    public const POST_DISCOUNT_COUNT = 25;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::POST_DISCOUNT_COUNT; ++$i) {
            $postDiscount = $this->createPostDiscount();
            $manager->persist($postDiscount);
            $this->addReference(self::POST_DISCOUNT_REFERENCE_PREFIX . $i, $postDiscount);
        }

        $manager->flush();
    }

    private function createPostDiscount(): PostDiscount
    {
        $scoreOrderIndex = $this->faker->numberBetween(0, ScoreOrderFixtures::SCORE_ORDER_COUNT - 1);
        $scoreOrder = $this->getReference(ScoreOrderFixtures::SCORE_ORDER_REFERENCE_PREFIX . $scoreOrderIndex, ScoreOrder::class);

        $postDiscount = new PostDiscount();
        $postDiscount->setScoreOrder($scoreOrder);
        $postDiscount->setName($this->faker->sentence(2));
        $postDiscount->setDescription($this->faker->text(30));

        if ($this->faker->boolean(70)) {
            $postDiscount->setCount($this->faker->numberBetween(1, 5));
        }

        $createTime = $this->faker->dateTimeBetween('-30 days', 'now');
        $postDiscount->setCreateTime(\DateTimeImmutable::createFromMutable($createTime));

        return $postDiscount;
    }

    public function getDependencies(): array
    {
        return [
            ScoreOrderFixtures::class,
        ];
    }
}
