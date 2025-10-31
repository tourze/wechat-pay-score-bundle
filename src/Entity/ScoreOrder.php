<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatPayBundle\Entity\Merchant;
use WechatPayScoreBundle\Enum\ScoreOrderState;
use WechatPayScoreBundle\Repository\ScoreOrderRepository;

/**
 * 支付分订单
 *
 * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter6_1_14.shtml
 */
#[ORM\Entity(repositoryClass: ScoreOrderRepository::class)]
#[ORM\Table(name: 'wechat_pay_score_order', options: ['comment' => '支付分订单'])]
class ScoreOrder implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;

    /**
     * 关联商户号
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Merchant $merchant = null;

    #[ORM\Column(length: 32, unique: true, options: ['comment' => '商户服务订单号'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    private ?string $outTradeNo = null;

    #[ORM\Column(length: 32, options: ['comment' => '应用ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    private ?string $appId = null;

    #[ORM\Column(length: 32, options: ['comment' => '服务ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    private ?string $serviceId = null;

    #[ORM\Column(length: 20, options: ['comment' => '服务信息'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private ?string $serviceIntroduction = null;

    /**
     * @var Collection<int, PostPayment>
     */
    #[ORM\OneToMany(mappedBy: 'scoreOrder', targetEntity: PostPayment::class)]
    private Collection $postPayments;

    /**
     * @var Collection<int, PostDiscount>
     */
    #[ORM\OneToMany(mappedBy: 'scoreOrder', targetEntity: PostDiscount::class)]
    private Collection $postDiscounts;

    #[ORM\Column(length: 14, options: ['comment' => '服务开始时间'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 14)]
    private ?string $startTime = null;

    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '服务开始时间备注'])]
    #[Assert\Length(max: 20)]
    private ?string $startTimeRemark = null;

    #[ORM\Column(length: 14, nullable: true, options: ['comment' => '服务结束时间'])]
    #[Assert\Length(max: 14)]
    private ?string $endTime = null;

    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '服务结束时间备注'])]
    #[Assert\Length(max: 20)]
    private ?string $endTimeRemark = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '服务开始地点'])]
    #[Assert\Length(max: 50)]
    private ?string $startLocation = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '预计服务结束位置'])]
    #[Assert\Length(max: 50)]
    private ?string $endLocation = null;

    #[ORM\Column(length: 64, options: ['comment' => '风险金名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private ?string $riskFundName = null;

    #[ORM\Column(options: ['comment' => '风险金额'])]
    #[Assert\PositiveOrZero]
    private ?int $riskFundAmount = null;

    #[ORM\Column(length: 30, nullable: true, options: ['comment' => '风险说明'])]
    #[Assert\Length(max: 30)]
    private ?string $riskFundDescription = null;

    #[ORM\Column(length: 256, nullable: true, options: ['comment' => '商户数据包'])]
    #[Assert\Length(max: 256)]
    private ?string $attach = null;

    #[ORM\Column(length: 255, options: ['comment' => '回调地址'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    private ?string $notifyUrl = null;

    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '用户标识'])]
    #[Assert\Length(max: 128)]
    private ?string $openId = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否需要用户确认'])]
    #[Assert\Type(type: 'bool')]
    private ?bool $needUserConfirm = null;

    #[ORM\Column(length: 32, nullable: true, enumType: ScoreOrderState::class, options: ['comment' => '订单状态'])]
    #[Assert\Choice(callback: [ScoreOrderState::class, 'cases'])]
    private ?ScoreOrderState $state = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '状态说明'])]
    #[Assert\Length(max: 32)]
    private ?string $stateDescription = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '微信支付服务订单号'])]
    #[Assert\Length(max: 64)]
    private ?string $orderId = null;

    #[ORM\Column(length: 300, nullable: true, options: ['comment' => '跳转微信侧小程序订单数据'])]
    #[Assert\Length(max: 300)]
    private ?string $package = null;

    #[ORM\Column(nullable: true, options: ['comment' => '商户收款总金额'])]
    #[Assert\PositiveOrZero]
    private ?int $totalAmount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否需要收款'])]
    #[Assert\Type(type: 'bool')]
    private ?bool $needCollection = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(nullable: true, options: ['comment' => '收款信息'])]
    #[Assert\Type(type: 'array')]
    private ?array $collection = null;

    #[ORM\Column(length: 30, nullable: true, options: ['comment' => '取消原因'])]
    #[Assert\Length(max: 30)]
    private ?string $cancelReason = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '修改金额原因'])]
    #[Assert\Length(max: 50)]
    private ?string $modifyPriceReason = null;

    public function __construct()
    {
        $this->postPayments = new ArrayCollection();
        $this->postDiscounts = new ArrayCollection();
    }

    /**
     * 批量设置基础属性
     */
    public function setBasicAttributes(
        string $outTradeNo,
        string $appId,
        string $serviceId,
        string $serviceIntroduction,
        string $startTime,
        string $riskFundName,
        int $riskFundAmount,
        string $notifyUrl,
        Merchant $merchant,
    ): void {
        $this->outTradeNo = $outTradeNo;
        $this->appId = $appId;
        $this->serviceId = $serviceId;
        $this->serviceIntroduction = $serviceIntroduction;
        $this->startTime = $startTime;
        $this->riskFundName = $riskFundName;
        $this->riskFundAmount = $riskFundAmount;
        $this->notifyUrl = $notifyUrl;
        $this->merchant = $merchant;
    }

    public function getOutTradeNo(): ?string
    {
        return $this->outTradeNo;
    }

    public function setOutTradeNo(string $outTradeNo): void
    {
        $this->outTradeNo = $outTradeNo;
    }

    public function getAppId(): ?string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    public function getServiceId(): ?string
    {
        return $this->serviceId;
    }

    public function setServiceId(string $serviceId): void
    {
        $this->serviceId = $serviceId;
    }

    public function getServiceIntroduction(): ?string
    {
        return $this->serviceIntroduction;
    }

    public function setServiceIntroduction(string $serviceIntroduction): void
    {
        $this->serviceIntroduction = $serviceIntroduction;
    }

    /**
     * @return Collection<int, PostPayment>
     */
    public function getPostPayments(): Collection
    {
        return $this->postPayments;
    }

    public function addScorePostPayment(PostPayment $postPayment): void
    {
        if (!$this->postPayments->contains($postPayment)) {
            $this->postPayments->add($postPayment);
            $postPayment->setScoreOrder($this);
        }
    }

    public function removePostPayment(PostPayment $postPayment): void
    {
        if ($this->postPayments->removeElement($postPayment)) {
            // set the owning side to null (unless already changed)
            if ($postPayment->getScoreOrder() === $this) {
                $postPayment->setScoreOrder(null);
            }
        }
    }

    /**
     * @return Collection<int, PostDiscount>
     */
    public function getPostDiscounts(): Collection
    {
        return $this->postDiscounts;
    }

    public function addPostDiscount(PostDiscount $postDiscount): void
    {
        if (!$this->postDiscounts->contains($postDiscount)) {
            $this->postDiscounts->add($postDiscount);
            $postDiscount->setScoreOrder($this);
        }
    }

    public function removePostDiscount(PostDiscount $postDiscount): void
    {
        if ($this->postDiscounts->removeElement($postDiscount)) {
            // set the owning side to null (unless already changed)
            if ($postDiscount->getScoreOrder() === $this) {
                $postDiscount->setScoreOrder(null);
            }
        }
    }

    public function getStartTime(): ?string
    {
        return $this->startTime;
    }

    public function setStartTime(string $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getStartTimeRemark(): ?string
    {
        return $this->startTimeRemark;
    }

    public function setStartTimeRemark(?string $startTimeRemark): void
    {
        $this->startTimeRemark = $startTimeRemark;
    }

    public function getEndTime(): ?string
    {
        return $this->endTime;
    }

    public function setEndTime(?string $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function getEndTimeRemark(): ?string
    {
        return $this->endTimeRemark;
    }

    public function setEndTimeRemark(?string $endTimeRemark): void
    {
        $this->endTimeRemark = $endTimeRemark;
    }

    public function getStartLocation(): ?string
    {
        return $this->startLocation;
    }

    public function setStartLocation(?string $startLocation): void
    {
        $this->startLocation = $startLocation;
    }

    public function getEndLocation(): ?string
    {
        return $this->endLocation;
    }

    public function setEndLocation(?string $endLocation): void
    {
        $this->endLocation = $endLocation;
    }

    public function getRiskFundName(): ?string
    {
        return $this->riskFundName;
    }

    public function setRiskFundName(string $riskFundName): void
    {
        $this->riskFundName = $riskFundName;
    }

    public function getRiskFundAmount(): ?int
    {
        return $this->riskFundAmount;
    }

    public function setRiskFundAmount(int $riskFundAmount): void
    {
        $this->riskFundAmount = $riskFundAmount;
    }

    public function getRiskFundDescription(): ?string
    {
        return $this->riskFundDescription;
    }

    public function setRiskFundDescription(?string $riskFundDescription): void
    {
        $this->riskFundDescription = $riskFundDescription;
    }

    public function getAttach(): ?string
    {
        return $this->attach;
    }

    public function setAttach(?string $attach): void
    {
        $this->attach = $attach;
    }

    public function getNotifyUrl(): ?string
    {
        return $this->notifyUrl;
    }

    public function setNotifyUrl(string $notifyUrl): void
    {
        $this->notifyUrl = $notifyUrl;
    }

    public function getOpenId(): ?string
    {
        return $this->openId;
    }

    public function setOpenId(?string $openId): void
    {
        $this->openId = $openId;
    }

    public function isNeedUserConfirm(): ?bool
    {
        return $this->needUserConfirm;
    }

    public function setNeedUserConfirm(?bool $needUserConfirm): void
    {
        $this->needUserConfirm = $needUserConfirm;
    }

    public function getState(): ?ScoreOrderState
    {
        return $this->state;
    }

    public function setState(?ScoreOrderState $state): void
    {
        $this->state = $state;
    }

    public function getStateDescription(): ?string
    {
        return $this->stateDescription;
    }

    public function setStateDescription(?string $stateDescription): void
    {
        $this->stateDescription = $stateDescription;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(?string $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getPackage(): ?string
    {
        return $this->package;
    }

    public function setPackage(?string $package): void
    {
        $this->package = $package;
    }

    public function getMerchant(): ?Merchant
    {
        return $this->merchant;
    }

    public function setMerchant(?Merchant $merchant): void
    {
        $this->merchant = $merchant;
    }

    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?int $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function isNeedCollection(): ?bool
    {
        return $this->needCollection;
    }

    public function setNeedCollection(?bool $needCollection): void
    {
        $this->needCollection = $needCollection;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getCollection(): ?array
    {
        return $this->collection;
    }

    /**
     * @param array<string, mixed>|null $collection
     */
    public function setCollection(?array $collection): void
    {
        $this->collection = $collection;
    }

    public function getCancelReason(): ?string
    {
        return $this->cancelReason;
    }

    public function setCancelReason(?string $cancelReason): void
    {
        $this->cancelReason = $cancelReason;
    }

    public function getModifyPriceReason(): ?string
    {
        return $this->modifyPriceReason;
    }

    public function setModifyPriceReason(?string $modifyPriceReason): void
    {
        $this->modifyPriceReason = $modifyPriceReason;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
