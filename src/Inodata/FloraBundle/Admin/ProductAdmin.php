<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ProductAdmin extends Admin
{
    /**
     * @param Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('code', null, ['label' => 'label.code'])
            ->add('description', null, ['label' => 'label.description'])
            ->add('price', null, ['label' => 'label.price'])
            ->add('stock', null, ['label' => 'label.stock'])
            ->add('categories', null, ['label' => 'label.categories']);
    }

    /**
     * @param Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
        ->add('code', null, ['label' => 'label.code'])
        ->add('description', null, ['label' => 'label.description'])
        ->add('price', null, ['label' => 'label.price'])
        ->add('stock', null, ['label' => 'label.stock'])
        ->add('_action', 'actions', ['label' => 'label.action',
            'actions'                        => [
                'edit' => [],
            ],
        ]);
    }

    /**
     * @param Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('code', null, ['label' => 'label.code'])
            ->add('description', null, ['label' => 'label.description'])
            ->add('categories', null, ['label' => 'label.categories'])
            ->add('price', null, ['label' => 'label.price']);
    }
}
