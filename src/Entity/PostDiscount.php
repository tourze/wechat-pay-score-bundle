<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use WechatPayScoreBundle\Repository\PostDiscountRepository;

/**
 * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter6_1_14.shtml
 *
 * @implements PlainArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: PostDiscountRepository::class)]
#[ORM\Table(name: 'wechat_pay_score_post_discount', options: ['comment' => '微信支付积分贴折扣'])]
class PostDiscount implements PlainArrayInterface, \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[ORM\ManyToOne(inversedBy: 'postDiscounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ScoreOrder $scoreOrder = null;

    #[ORM\Column(length: 20, options: ['comment' => '优惠名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private ?string $name = null;

    #[ORM\Column(length: 30, options: ['comment' => '优惠说明'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 30)]
    private ?string $description = null;

    #[ORM\Column(nullable: true, options: ['comment' => '优惠数量', 'default' => 1])]
    #[Assert\PositiveOrZero]
    private ?int $count = null;

    public function getScoreOrder(): ?ScoreOrder
    {
        return $this->scoreOrder;
    }

    public function setScoreOrder(?ScoreOrder $scoreOrder): void
    {
        $this->scoreOrder = $scoreOrder;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return array<string, mixed>
     */
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
