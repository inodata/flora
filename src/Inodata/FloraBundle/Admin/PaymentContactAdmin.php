<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

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
			->add('customer', 'sonata_type_model', array('label' => 'label.customer'))
			->add('name', null, array('label' => 'label.name'))
			->add('department', null, array('label' => 'label.department'))
			->add('employeeNumber', null, array('label' => 'label.employee_number'))
			->add('phone', null, array('label' => 'label.phone'))
			->add('extension', null, array('label' => 'label.payment_contact.extension'))
			->add('email', null, array('label' => 'label.email'))
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
			->addIdentifier('employee_number', null, array('label' => 'label.employee_number'))
			->add('name', null, array('label' => 'label.name'))
			->add('phone', null, array('label' => 'label.phone'))
			->add('email', null, array('label' => 'label.email'))
			->add('department', null, array('label' => 'label.department'))
			->add('customer', null, array('label' => 'label.customer'))
			->add('_action', 'actions', array(
				'label' => 'label.action',
				'actions' => array(
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
			->add('employeeNumber', null, array('label' => 'label.employee_number'))
			->add('customer', null, array('label' => 'label.customer'))
			->add('name', null, array('label' => 'label.name'))
			->add('email', null, array('label' => 'label.email'))
			->add('department', null, array('label' => 'label.department'))
		;		
	}
	
} 