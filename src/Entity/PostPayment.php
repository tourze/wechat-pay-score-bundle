<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use WechatPayScoreBundle\Repository\PostPaymentRepository;

/**
 * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter6_1_14.shtml
 *
 * @implements PlainArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: PostPaymentRepository::class)]
#[ORM\Table(name: 'wechat_pay_score_post_payment', options: ['comment' => '微信支付记分后支付'])]
class PostPayment implements PlainArrayInterface, \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[ORM\ManyToOne(inversedBy: 'postPayments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ScoreOrder $scoreOrder = null;

    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '付费项目名称'])]
    #[Assert\Length(max: 20)]
    private ?string $name = null;

    #[ORM\Column(options: ['comment' => '金额', 'default' => 1])]
    #[Assert\PositiveOrZero]
    private ?int $amount = null;

    #[ORM\Column(length: 30, nullable: true, options: ['comment' => '计费说明'])]
    #[Assert\Length(max: 30)]
    private ?string $description = null;

    #[ORM\Column(nullable: true, options: ['comment' => '付费数量'])]
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

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
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
            'amount' => intval($this->getAmount()),
            'description' => strval($this->getDescription()),
            'count' => intval($this->getCount()),
        ];
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
