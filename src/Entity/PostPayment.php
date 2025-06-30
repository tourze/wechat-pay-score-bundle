<?php

namespace WechatPayScoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use WechatPayScoreBundle\Repository\PostPaymentRepository;

/**
 * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter6_1_14.shtml
 */
#[ORM\Entity(repositoryClass: PostPaymentRepository::class)]
#[ORM\Table(name: 'wechat_pay_score_post_payment', options: ['comment' => '微信支付记分后支付'])]
class PostPayment implements PlainArrayInterface, \Stringable
{

    #[ORM\ManyToOne(inversedBy: 'postPayments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ScoreOrder $scoreOrder = null;

    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '付费项目名称'])]
    private ?string $name = null;

    #[ORM\Column(options: ['comment' => '金额', 'default' => 1])]
    private ?int $amount = null;

    #[ORM\Column(length: 30, nullable: true, options: ['comment' => '计费说明'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true, options: ['comment' => '付费数量'])]
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

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
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
            'amount' => intval($this->getAmount()),
            'description' => strval($this->getDescription()),
            'count' => intval($this->getCount()),
        ];
    }

    public function __toString(): string
    {
        return (string)$this->getId();
    }
}
