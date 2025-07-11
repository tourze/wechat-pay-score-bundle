<?php

namespace WechatPayScoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use WechatPayScoreBundle\Repository\PostDiscountRepository;

/**
 * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter6_1_14.shtml
 */
#[ORM\Entity(repositoryClass: PostDiscountRepository::class)]
#[ORM\Table(name: 'wechat_pay_score_post_discount', options: ['comment' => '微信支付积分贴折扣'])]
class PostDiscount implements PlainArrayInterface
, \Stringable{

    #[ORM\ManyToOne(inversedBy: 'postDiscounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ScoreOrder $scoreOrder = null;

    #[ORM\Column(length: 20, options: ['comment' => '优惠名称'])]
    private ?string $name = null;

    #[ORM\Column(length: 30, options: ['comment' => '优惠说明'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true, options: ['comment' => '优惠数量', 'default' => 1])]
    private ?int $count = null;

    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;


    public function getScoreOrder(): ?ScoreOrder
    {
        return $this->scoreOrder;
    }

    public function setScoreOrder(?ScoreOrder $scoreOrder): static
    {
        $this->scoreOrder = $scoreOrder;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function retrievePlainArray(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'count' => intval($this->getCount()),
        ];
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
