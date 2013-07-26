<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

use Symfony\Component\Form\AbstractType;

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
			->add('street', null, array('label' => 'label.street'))
			->add('noInt', null, array('label' => 'label.interior'))
			->add('noExt', null, array('label' => 'label.exterior'))
			->add('neighborhood', null, array('label' => 'label.neighborhood'))
			->add('state', 'inodata_mx_states_type', array('label' => 'label.state'))
			->add('city', null, array('label' => 'label.city'))
			->add('postalCode', null, array('label' => 'label.postal_code'))
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
			->addIdentifier('id')
			->add('street', null, array('label' => 'label.street'))
			->add('no_int', null, array('label' => 'label.interior'))
			->add('no_ext', null, array('label' => 'label.exterior'))
			->add('state', null, array('label' => 'label.state'))
			->add('city', null, array('label' => 'label.city'))
			->add('postal_code', null, array('label' => 'label.postal_code'))
			->add('neighborhood', null, array('label' => 'label.neighborhood'))
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
			->add('postalCode', null, array('label' => 'label.postal_code'))
			->add('state', null, array('label' => 'label.state'))
			->add('city', null, array('label' => 'label.city'))
		;		
	}
	
} 