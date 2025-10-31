<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use WechatPayBundle\Entity\Merchant;
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Enum\ScoreOrderState;

/**
 * @extends AbstractCrudController<ScoreOrder>
 */
#[AdminCrud(routePath: '/wechat-pay-score/score-order', routeName: 'wechat_pay_score_score_order')]
final class ScoreOrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ScoreOrder::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('支付分订单')
            ->setEntityLabelInPlural('支付分订单')
            ->setPageTitle('index', '支付分订单列表')
            ->setPageTitle('detail', '支付分订单详情')
            ->setPageTitle('new', '创建支付分订单')
            ->setPageTitle('edit', '编辑支付分订单')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
        ;

        yield AssociationField::new('merchant', '商户')
            ->setRequired(true)
            ->setHelp('选择关联的商户号')
        ;

        yield TextField::new('outTradeNo', '商户订单号')
            ->setMaxLength(32)
            ->setRequired(true)
            ->setHelp('商户系统内部订单号，要求32个字符内')
        ;

        yield TextField::new('appId', '应用ID')
            ->setMaxLength(32)
            ->setRequired(true)
            ->setHelp('微信公众号或小程序等应用ID')
        ;

        yield TextField::new('serviceId', '服务ID')
            ->setMaxLength(32)
            ->setRequired(true)
            ->setHelp('微信支付服务分配的服务ID')
        ;

        yield TextField::new('serviceIntroduction', '服务信息')
            ->setMaxLength(20)
            ->setRequired(true)
            ->setHelp('对本笔交易的描述，最多20个字符')
        ;

        yield TextField::new('startTime', '服务开始时间')
            ->setMaxLength(14)
            ->setRequired(true)
            ->setHelp('格式为yyyyMMddHHmmss')
        ;

        yield TextField::new('startTimeRemark', '开始时间备注')
            ->setMaxLength(20)
            ->hideOnIndex()
            ->setHelp('服务开始时间备注说明')
        ;

        yield TextField::new('endTime', '服务结束时间')
            ->setMaxLength(14)
            ->hideOnIndex()
            ->setHelp('格式为yyyyMMddHHmmss')
        ;

        yield TextField::new('endTimeRemark', '结束时间备注')
            ->setMaxLength(20)
            ->hideOnIndex()
            ->setHelp('服务结束时间备注说明')
        ;

        yield TextField::new('startLocation', '服务开始地点')
            ->setMaxLength(50)
            ->hideOnIndex()
            ->setHelp('服务开始地点，最多50个字符')
        ;

        yield TextField::new('endLocation', '预计结束位置')
            ->setMaxLength(50)
            ->hideOnIndex()
            ->setHelp('预计服务结束位置，最多50个字符')
        ;

        yield TextField::new('riskFundName', '风险金名称')
            ->setMaxLength(64)
            ->setRequired(true)
            ->hideOnIndex()
            ->setHelp('风险金名称，最多64个字符')
        ;

        yield IntegerField::new('riskFundAmount', '风险金额')
            ->setRequired(true)
            ->setHelp('风险金额，单位为分')
        ;

        yield TextField::new('riskFundDescription', '风险说明')
            ->setMaxLength(30)
            ->hideOnIndex()
            ->setHelp('风险说明，最多30个字符')
        ;

        yield TextareaField::new('attach', '商户数据包')
            ->setMaxLength(256)
            ->hideOnIndex()
            ->setHelp('附加数据，在查询API和支付通知中原样返回')
        ;

        yield UrlField::new('notifyUrl', '回调地址')
            ->setFormTypeOption('attr', ['maxlength' => 255])
            ->setRequired(true)
            ->hideOnIndex()
            ->setHelp('接收微信支付异步通知的回调地址')
        ;

        yield TextField::new('openId', '用户标识')
            ->setMaxLength(128)
            ->hideOnIndex()
            ->setHelp('微信用户在商户对应appid下的唯一标识')
        ;

        yield BooleanField::new('needUserConfirm', '需要用户确认')
            ->hideOnIndex()
            ->setHelp('是否需要用户确认才能继续后续流程')
        ;

        $stateField = EnumField::new('state', '订单状态');
        $stateField->setEnumCases(ScoreOrderState::cases());
        $stateField->setHelp('当前支付分订单状态');
        yield $stateField;

        yield TextField::new('stateDescription', '状态说明')
            ->setMaxLength(32)
            ->hideOnIndex()
            ->setHelp('订单状态的详细说明')
        ;

        yield TextField::new('orderId', '微信支付订单号')
            ->setMaxLength(64)
            ->hideOnIndex()
            ->setHelp('微信支付服务订单号')
        ;

        yield TextareaField::new('package', '小程序数据包')
            ->setMaxLength(300)
            ->hideOnIndex()
            ->setHelp('跳转微信侧小程序订单数据')
        ;

        yield IntegerField::new('totalAmount', '收款总金额')
            ->hideOnIndex()
            ->setHelp('商户收款总金额，单位为分')
        ;

        yield BooleanField::new('needCollection', '需要收款')
            ->hideOnIndex()
            ->setHelp('是否需要进行收款')
        ;

        yield TextField::new('cancelReason', '取消原因')
            ->setMaxLength(30)
            ->hideOnIndex()
            ->setHelp('订单取消的原因')
        ;

        yield TextField::new('modifyPriceReason', '修改金额原因')
            ->setMaxLength(50)
            ->hideOnIndex()
            ->setHelp('修改订单金额的原因')
        ;

        yield AssociationField::new('postPayments', '后支付项目')
            ->hideOnForm()
            ->setHelp('关联的后支付项目列表')
        ;

        yield AssociationField::new('postDiscounts', '优惠项目')
            ->hideOnForm()
            ->setHelp('关联的优惠项目列表')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('merchant', '商户'))
            ->add(TextFilter::new('outTradeNo', '商户订单号'))
            ->add(TextFilter::new('appId', '应用ID'))
            ->add(TextFilter::new('serviceId', '服务ID'))
            ->add(ChoiceFilter::new('state', '订单状态')
                ->setChoices(array_combine(
                    array_map(fn (ScoreOrderState $state) => $state->getLabel(), ScoreOrderState::cases()),
                    array_map(fn (ScoreOrderState $state) => $state->value, ScoreOrderState::cases())
                )))
            ->add(BooleanFilter::new('needUserConfirm', '需要用户确认'))
            ->add(BooleanFilter::new('needCollection', '需要收款'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
