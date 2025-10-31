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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use WechatPayScoreBundle\Entity\PostPayment;

/**
 * @extends AbstractCrudController<PostPayment>
 */
#[AdminCrud(routePath: '/wechat-pay-score/post-payment', routeName: 'wechat_pay_score_post_payment')]
final class PostPaymentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PostPayment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('后支付项目')
            ->setEntityLabelInPlural('后支付项目')
            ->setPageTitle('index', '后支付项目列表')
            ->setPageTitle('detail', '后支付项目详情')
            ->setPageTitle('new', '创建后支付项目')
            ->setPageTitle('edit', '编辑后支付项目')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
        ;

        yield AssociationField::new('scoreOrder', '支付分订单')
            ->setRequired(true)
            ->setHelp('选择关联的支付分订单')
        ;

        yield TextField::new('name', '付费项目名称')
            ->setMaxLength(20)
            ->setHelp('付费项目名称，最多20个字符')
        ;

        yield IntegerField::new('amount', '金额')
            ->setRequired(true)
            ->setHelp('付费金额，单位为分，默认为1')
            ->setFormTypeOption('attr', ['min' => 0])
        ;

        yield TextField::new('description', '计费说明')
            ->setMaxLength(30)
            ->setHelp('计费详细说明，最多30个字符')
        ;

        yield IntegerField::new('count', '付费数量')
            ->setHelp('付费的数量')
            ->setFormTypeOption('attr', ['min' => 0])
        ;

        yield TextField::new('createdBy', '创建人')
            ->hideOnForm()
            ->setHelp('记录创建人')
        ;

        yield TextField::new('updatedBy', '更新人')
            ->hideOnForm()
            ->setHelp('记录最后更新人')
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
            ->add(EntityFilter::new('scoreOrder', '支付分订单'))
            ->add(TextFilter::new('name', '付费项目名称'))
            ->add(NumericFilter::new('amount', '金额'))
            ->add(TextFilter::new('description', '计费说明'))
            ->add(NumericFilter::new('count', '付费数量'))
            ->add(TextFilter::new('createdBy', '创建人'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
