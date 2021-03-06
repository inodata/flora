<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PaymentContactAdmin extends Admin
{
    /**
     * @param Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('customer', 'sonata_type_model', ['label' => 'label.customer'])
            ->add('name', null, ['label' => 'label.name'])
            ->add('department', null, ['label' => 'label.department'])
            ->add('employeeNumber', null, ['label' => 'label.employee_number'])
            ->add('phone', null, ['label' => 'label.phone'])
            ->add('extension', null, ['label' => 'label.payment_contact.extension'])
            ->add('email', null, ['label' => 'label.email']);
    }

    /**
     * @param Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('employee_number', null, ['label' => 'label.employee_number'])
            ->add('name', null, ['label' => 'label.name'])
            ->add('phone', null, ['label' => 'label.phone'])
            ->add('email', null, ['label' => 'label.email'])
            ->add('department', null, ['label' => 'label.department'])
            ->add('customer', null, ['label' => 'label.customer'])
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
            ->add('employeeNumber', null, ['label' => 'label.employee_number'])
            ->add('customer', null, ['label' => 'label.customer'])
            ->add('name', null, ['label' => 'label.name'])
            ->add('email', null, ['label' => 'label.email'])
            ->add('department', null, ['label' => 'label.department']);
    }
}
