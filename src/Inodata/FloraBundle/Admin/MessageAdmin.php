<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class MessageAdmin extends Admin
{
    /**
     * @var Pool
     */
    protected $formatterPool;

    protected $commentManager;

    /**
     * @param Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('category', 'sonata_type_model', ['label' => 'label.message_category'])
            ->add('code', null, ['label' => 'label.message_code'])
            ->add('message', 'ckeditor', [
                'config_name' => 'inodata_editor',
                'label'       => 'label.message',
                ])
            //->add('message2', 'ckeditor', array('mapped' => false))
;
    }

    /**
     * @param Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('message', null, ['label' => 'label.message'])
            ->add('category', null, ['label' => 'label.message_category'])
            ->add('_action', 'actions', [
                'label'   => 'label.action',
                'actions' => [
                    'show'   => [],
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
            ->add('message', null, ['label' => 'label.message'])
            ->add('category', null, ['label' => 'label.message_category']);
    }
}
