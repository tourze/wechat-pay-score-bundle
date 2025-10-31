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
use WechatPayScoreBundle\Entity\PostDiscount;

/**
 * @extends AbstractCrudController<PostDiscount>
 */
#[AdminCrud(routePath: '/wechat-pay-score/post-discount', routeName: 'wechat_pay_score_post_discount')]
final class PostDiscountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PostDiscount::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('优惠项目')
            ->setEntityLabelInPlural('优惠项目')
            ->setPageTitle('index', '优惠项目列表')
            ->setPageTitle('detail', '优惠项目详情')
            ->setPageTitle('new', '创建优惠项目')
            ->setPageTitle('edit', '编辑优惠项目')
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

        yield TextField::new('name', '优惠名称')
            ->setMaxLength(20)
            ->setRequired(true)
            ->setHelp('优惠名称，最多20个字符')
        ;

        yield TextField::new('description', '优惠说明')
            ->setMaxLength(30)
            ->setRequired(true)
            ->setHelp('优惠详细说明，最多30个字符')
        ;

        yield IntegerField::new('count', '优惠数量')
            ->setHelp('优惠的数量，默认为1')
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
            ->add(TextFilter::new('name', '优惠名称'))
            ->add(TextFilter::new('description', '优惠说明'))
            ->add(NumericFilter::new('count', '优惠数量'))
            ->add(TextFilter::new('createdBy', '创建人'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
