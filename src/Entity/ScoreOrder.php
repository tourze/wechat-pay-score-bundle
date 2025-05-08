<?php

namespace WechatPayScoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use WechatPayBundle\Entity\Merchant;
use WechatPayScoreBundle\Enum\ScoreOrderState;
use WechatPayScoreBundle\Repository\ScoreOrderRepository;

/**
 * 支付分订单
 *
 * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter6_1_14.shtml
 */
#[Deletable]
#[Editable]
#[ORM\Entity(repositoryClass: ScoreOrderRepository::class)]
#[ORM\Table(name: 'wechat_pay_score_order', options: ['comment' => '支付分订单'])]
class ScoreOrder
{
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
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

    /**
     * 商户系统内部服务订单号（不是交易单号），要求此参数只能由数字、大小写字母_-|*组成，且在同一个商户号下唯一。详见[商户订单号]。
     * 示例值：1234323JKHDFE1243252
     */
    #[ORM\Column(length: 32, unique: true, options: ['comment' => '商户服务订单号'])]
    private ?string $outTradeNo = null;

    /**
     * 微信为调用商户分配的公众账号ID，接口传入的appid应该为公众号的appid和小程序的appid（在mp.weixin.qq.com申请的）或APP的appid（在open.weixin.qq.com申请的）。
     * 校验规则：
     * 1、该appid需要与调用接口的商户号（即请求头中的商户号）有绑定关系，若未绑定，可参考该指引完成绑定（商家商户号与AppID账号关联管理）；
     * 2、该appid需要在支付分系统中先进行配置
     * 示例值：wxd678efh567hg6787
     */
    #[ORM\Column(length: 32, options: ['comment' => '应用ID'])]
    private ?string $appId = null;

    /**
     * 该服务ID有本接口对应产品的权限。
     * 示例值：500001
     */
    #[ORM\Column(length: 32, options: ['comment' => '服务ID'])]
    private ?string $serviceId = null;

    #[ORM\Column(length: 20)]
    private ?string $serviceIntroduction = null;

    #[ORM\OneToMany(mappedBy: 'scoreOrder', targetEntity: PostPayment::class)]
    private Collection $postPayments;

    #[ORM\OneToMany(mappedBy: 'scoreOrder', targetEntity: PostDiscount::class)]
    private Collection $postDiscounts;

    #[ORM\Column(length: 14)]
    private ?string $startTime = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $startTimeRemark = null;

    #[ORM\Column(length: 14, nullable: true)]
    private ?string $endTime = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $endTimeRemark = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '服务开始地点'])]
    private ?string $startLocation = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '预计服务结束位置'])]
    private ?string $endLocation = null;

    #[ORM\Column(length: 64)]
    private ?string $riskFundName = null;

    #[ORM\Column]
    private ?int $riskFundAmount = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $riskFundDescription = null;

    #[ORM\Column(length: 256, nullable: true)]
    private ?string $attach = null;

    #[ORM\Column(length: 255, options: ['comment' => '回调地址'])]
    private ?string $notifyUrl = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $openId = null;

    #[ORM\Column(nullable: true)]
    private ?bool $needUserConfirm = null;

    #[ORM\Column(length: 32, nullable: true, enumType: ScoreOrderState::class)]
    private ?ScoreOrderState $state = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $stateDescription = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $orderId = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $package = null;

    /**
     * 总金额，大于等于0的数字，单位为分，只能为整数，详见支付金额。
     * 此参数需满足：总金额=后付费项目金额之和-后付费商户优惠项目金额之和，且小于等于订单风险金额。取消订单时，该字段必须为0。
     * 示例值：40000
     */
    #[ORM\Column(nullable: true, options: ['comment' => '商户收款总金额'])]
    private ?int $totalAmount = null;

    /**
     * 是否需要收款，非0元完结后返回
     * true：微信支付分代收款
     * false：无需微信支付分代收款
     */
    #[ORM\Column(nullable: true, options: ['comment' => '是否需要收款'])]
    private ?bool $needCollection = null;

    /**
     * 收款信息，非0元完结后返回
     */
    #[ORM\Column(nullable: true, options: ['comment' => '收款信息'])]
    private ?array $collection = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $cancelReason = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $modifyPriceReason = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

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
}
