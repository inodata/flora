<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PartnerAdmin extends Admin
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
            ->add('name', null, ['label' => 'label.name'])
            ->add('phone', null, ['label' => 'label.phone'])
            ->add('email', null, ['label' => 'label.email'])
            ->with('label.address', ['collapsed'=>false, 'label' => 'label.address'])
                ->add('address', 'inodata_address_form', ['label' => 'label.address'])
            ->end();
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
            ->add('name', null, ['label' => 'label.name'])
            ->add('phone', null, ['label' => 'label.phone'])
            ->add('email', null, ['label' => 'label.email'])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit'   => [],
                    'delete' => [],
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
            ->add('name', null, ['label' => 'label.name']);
    }
}
