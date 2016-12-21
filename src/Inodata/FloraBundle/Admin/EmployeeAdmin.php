<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class EmployeeAdmin extends Admin
{
    /**
     * @param Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('code', null, ['label'=>'label.code'])
            ->add('name', null, ['label'=>'label.name'])
            ->add('lastname', null, ['label'=>'label.lastname'])
            ->add('phone', null, ['label'=>'label.phone'])
            ->add('job_position', null, ['label'=>'label.job_position']);
    }

    /**
     * @param Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('code', null, ['label'=>'label.code'])
            ->add('name', null, ['label'=>'label.name'])
            ->add('lastname', null, ['label'=>'label.lastname'])
            ->add('phone', null, ['label'=>'label.phone'])
            ->add('job_position', 'inodata_emp_positions_type', ['label'=>'label.job_position']);
    }

    /**
     * @param Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('code', null, ['label'=>'label.code'])
            ->add('name', null, ['label'=>'label.name'])
            ->add('lastname', null, ['label'=>'label.lastname'])
            ->add('phone', null, ['label'=>'label.phone'])
            ->add('job_position', 'inodata_emp_positions_type', ['label'=>'label.job_position'])
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
            ->add('name', null, ['label'=>'label.name'])
            ->add('lastname', null, ['label'=>'label.lastname'])
            ->add('jobPosition', 'doctrine_orm_choice', ['label' => 'label.job_position',
                    'field_options'                              => [
                        'required'           => false,
                        'choices'            => ['Messenger' => 'Messenger', 'Collector' => 'Collector'],
                        'translation_domain' => 'InodataFloraBundle',
                    ],
                    'field_type' => 'choice',
                ]);
    }
}
