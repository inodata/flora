<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class AddressAdmin extends Admin
{
    /**
     * @param Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('addressType', 'inodata_address_type_type', ['label' => 'label.address_type', 'attr' => ['class' => 'span5']])
            ->add('street', null, ['label' => 'label.street'])
            ->add('noExt', null, ['label' => 'label.exterior'])
            ->add('noInt', null, ['label' => 'label.interior'])
            ->add('reference', null, ['label' => 'label.reference'])
            ->add('postalCode', null, ['label' => 'label.postal_code'])
            ->add('neighborhood', null, ['label' => 'label.neighborhood'])
            ->add('city', null, ['label' => 'label.city'])
            ->add('state', 'inodata_mx_states_type', ['label' => 'label.state']);
    }

    /**
     * @param Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('street', null, ['label' => 'label.street'])
            ->add('no_int', null, ['label' => 'label.interior'])
            ->add('no_ext', null, ['label' => 'label.exterior'])
            ->add('state', null, ['label' => 'label.state'])
            ->add('city', null, ['label' => 'label.city'])
            ->add('postal_code', null, ['label' => 'label.postal_code'])
            ->add('neighborhood', null, ['label' => 'label.neighborhood'])
            ->add('_action', 'actions', [
                'label'   => 'label.action',
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
            ->add('postalCode', null, ['label' => 'label.postal_code'])
            ->add('state', null, ['label' => 'label.state'])
            ->add('city', null, ['label' => 'label.city']);
    }
}
