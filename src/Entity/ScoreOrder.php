<?php

namespace WechatPayScoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
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
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    /**
     * 关联商户号
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Merchant $merchant = null;

    #[ORM\Column(length: 32, unique: true, options: ['comment' => '商户服务订单号'])]
    private ?string $outTradeNo = null;

    #[ORM\Column(length: 32, options: ['comment' => '应用ID'])]
    private ?string $appId = null;

    #[ORM\Column(length: 32, options: ['comment' => '服务ID'])]
    private ?string $serviceId = null;

    #[ORM\Column(length: 20, options: ['comment' => '服务信息'])]
    private ?string $serviceIntroduction = null;

    #[ORM\OneToMany(mappedBy: 'scoreOrder', targetEntity: PostPayment::class)]
    private Collection $postPayments;

    #[ORM\OneToMany(mappedBy: 'scoreOrder', targetEntity: PostDiscount::class)]
    private Collection $postDiscounts;

    #[ORM\Column(length: 14, options: ['comment' => '服务开始时间'])]
    private ?string $startTime = null;

    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '服务开始时间备注'])]
    private ?string $startTimeRemark = null;

    #[ORM\Column(length: 14, nullable: true, options: ['comment' => '服务结束时间'])]
    private ?string $endTime = null;

    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '服务结束时间备注'])]
    private ?string $endTimeRemark = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '服务开始地点'])]
    private ?string $startLocation = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '预计服务结束位置'])]
    private ?string $endLocation = null;

    #[ORM\Column(length: 64, options: ['comment' => '风险金名称'])]
    private ?string $riskFundName = null;

    #[ORM\Column(options: ['comment' => '风险金额'])]
    private ?int $riskFundAmount = null;

    #[ORM\Column(length: 30, nullable: true, options: ['comment' => '风险说明'])]
    private ?string $riskFundDescription = null;

    #[ORM\Column(length: 256, nullable: true, options: ['comment' => '商户数据包'])]
    private ?string $attach = null;

    #[ORM\Column(length: 255, options: ['comment' => '回调地址'])]
    private ?string $notifyUrl = null;

    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '用户标识'])]
    private ?string $openId = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否需要用户确认'])]
    private ?bool $needUserConfirm = null;

    #[ORM\Column(length: 32, nullable: true, enumType: ScoreOrderState::class, options: ['comment' => '订单状态'])]
    private ?ScoreOrderState $state = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '状态说明'])]
    private ?string $stateDescription = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '微信支付服务订单号'])]
    private ?string $orderId = null;

    #[ORM\Column(length: 300, nullable: true, options: ['comment' => '跳转微信侧小程序订单数据'])]
    private ?string $package = null;

    #[ORM\Column(nullable: true, options: ['comment' => '商户收款总金额'])]
    private ?int $totalAmount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '是否需要收款'])]
    private ?bool $needCollection = null;

    #[ORM\Column(nullable: true, options: ['comment' => '收款信息'])]
    private ?array $collection = null;

    #[ORM\Column(length: 30, nullable: true, options: ['comment' => '取消原因'])]
    private ?string $cancelReason = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '修改金额原因'])]
    private ?string $modifyPriceReason = null;

    use TimestampableAware;

    public function __construct()
    {
        $this->postPayments = new ArrayCollection();
        $this->postDiscounts = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOutTradeNo(): ?string
    {
        return $this->outTradeNo;
    }

    public function setOutTradeNo(string $outTradeNo): static
    {
        $this->outTradeNo = $outTradeNo;

        return $this;
    }

    public function getAppId(): ?string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): static
    {
        $this->appId = $appId;

        return $this;
    }

    public function getServiceId(): ?string
    {
        return $this->serviceId;
    }

    public function setServiceId(string $serviceId): static
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    public function getServiceIntroduction(): ?string
    {
        return $this->serviceIntroduction;
    }

    public function setServiceIntroduction(string $serviceIntroduction): static
    {
        $this->serviceIntroduction = $serviceIntroduction;

        return $this;
    }

    /**
     * @return Collection<int, PostPayment>
     */
    public function getPostPayments(): Collection
    {
        return $this->postPayments;
    }

    public function addScorePostPayment(PostPayment $postPayment): static
    {
        if (!$this->postPayments->contains($postPayment)) {
            $this->postPayments->add($postPayment);
            $postPayment->setScoreOrder($this);
        }

        return $this;
    }

    public function removePostPayment(PostPayment $postPayment): static
    {
        if ($this->postPayments->removeElement($postPayment)) {
            // set the owning side to null (unless already changed)
            if ($postPayment->getScoreOrder() === $this) {
                $postPayment->setScoreOrder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PostDiscount>
     */
    public function getPostDiscounts(): Collection
    {
        return $this->postDiscounts;
    }

    public function addPostDiscount(PostDiscount $postDiscount): static
    {
        if (!$this->postDiscounts->contains($postDiscount)) {
            $this->postDiscounts->add($postDiscount);
            $postDiscount->setScoreOrder($this);
        }

        return $this;
    }

    public function removePostDiscount(PostDiscount $postDiscount): static
    {
        if ($this->postDiscounts->removeElement($postDiscount)) {
            // set the owning side to null (unless already changed)
            if ($postDiscount->getScoreOrder() === $this) {
                $postDiscount->setScoreOrder(null);
            }
        }

        return $this;
    }

    public function getStartTime(): ?string
    {
        return $this->startTime;
    }

    public function setStartTime(string $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getStartTimeRemark(): ?string
    {
        return $this->startTimeRemark;
    }

    public function setStartTimeRemark(?string $startTimeRemark): static
    {
        $this->startTimeRemark = $startTimeRemark;

        return $this;
    }

    public function getEndTime(): ?string
    {
        return $this->endTime;
    }

    public function setEndTime(?string $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getEndTimeRemark(): ?string
    {
        return $this->endTimeRemark;
    }

    public function setEndTimeRemark(?string $endTimeRemark): static
    {
        $this->endTimeRemark = $endTimeRemark;

        return $this;
    }

    public function getStartLocation(): ?string
    {
        return $this->startLocation;
    }

    public function setStartLocation(?string $startLocation): static
    {
        $this->startLocation = $startLocation;

        return $this;
    }

    public function getEndLocation(): ?string
    {
        return $this->endLocation;
    }

    public function setEndLocation(?string $endLocation): static
    {
        $this->endLocation = $endLocation;

        return $this;
    }

    public function getRiskFundName(): ?string
    {
        return $this->riskFundName;
    }

    public function setRiskFundName(string $riskFundName): static
    {
        $this->riskFundName = $riskFundName;

        return $this;
    }

    public function getRiskFundAmount(): ?int
    {
        return $this->riskFundAmount;
    }

    public function setRiskFundAmount(int $riskFundAmount): static
    {
        $this->riskFundAmount = $riskFundAmount;

        return $this;
    }

    public function getRiskFundDescription(): ?string
    {
        return $this->riskFundDescription;
    }

    public function setRiskFundDescription(?string $riskFundDescription): static
    {
        $this->riskFundDescription = $riskFundDescription;

        return $this;
    }

    public function getAttach(): ?string
    {
        return $this->attach;
    }

    public function setAttach(?string $attach): static
    {
        $this->attach = $attach;

        return $this;
    }

    public function getNotifyUrl(): ?string
    {
        return $this->notifyUrl;
    }

    public function setNotifyUrl(string $notifyUrl): static
    {
        $this->notifyUrl = $notifyUrl;

        return $this;
    }

    public function getOpenId(): ?string
    {
        return $this->openId;
    }

    public function setOpenId(?string $openId): static
    {
        $this->openId = $openId;

        return $this;
    }

    public function isNeedUserConfirm(): ?bool
    {
        return $this->needUserConfirm;
    }

    public function setNeedUserConfirm(?bool $needUserConfirm): static
    {
        $this->needUserConfirm = $needUserConfirm;

        return $this;
    }

    public function getState(): ?ScoreOrderState
    {
        return $this->state;
    }

    public function setState(?ScoreOrderState $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getStateDescription(): ?string
    {
        return $this->stateDescription;
    }

    public function setStateDescription(?string $stateDescription): static
    {
        $this->stateDescription = $stateDescription;

        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(?string $orderId): static
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getPackage(): ?string
    {
        return $this->package;
    }

    public function setPackage(?string $package): static
    {
        $this->package = $package;

        return $this;
    }

    public function getMerchant(): ?Merchant
    {
        return $this->merchant;
    }

    public function setMerchant(?Merchant $merchant): static
    {
        $this->merchant = $merchant;

        return $this;
    }

    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?int $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function isNeedCollection(): ?bool
    {
        return $this->needCollection;
    }

    public function setNeedCollection(?bool $needCollection): static
    {
        $this->needCollection = $needCollection;

        return $this;
    }

    public function getCollection(): ?array
    {
        return $this->collection;
    }

    public function setCollection(?array $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function getCancelReason(): ?string
    {
        return $this->cancelReason;
    }

    public function setCancelReason(?string $cancelReason): static
    {
        $this->cancelReason = $cancelReason;

        return $this;
    }

    public function getModifyPriceReason(): ?string
    {
        return $this->modifyPriceReason;
    }

    public function setModifyPriceReason(?string $modifyPriceReason): static
    {
        $this->modifyPriceReason = $modifyPriceReason;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
