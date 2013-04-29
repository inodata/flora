<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

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
			->add('category', 'sonata_type_model', array('label' => 'label.message_category'))
			->add('code', null, array('label' => 'label.message_code'))						
            ->add('message', 'ckeditor', array(
            	'config_name' => 'inodata_editor',
            	'label' => 'label.message',
            	))
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
			->addIdentifier('message')
			->add('category')
			->add('_action', 'actions', array(
				'actions' => array(
					'view' => array(),
					'edit' => array(),
					'delete' => array(),
				)
			));
	}
	
	/**
	 * @param Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
	 * 
	 * @return void
	 */	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('message')
			->add('category')
		;		
	}
	
} 