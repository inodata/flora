<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CustomerAdmin extends Admin
{
	/**
	 * @param Sonata\AdminBundle\Form\FormMapper $formMapper
	 * 
	 * @return void
	 */
	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->with('Datos Generales', array('expanded' => true,))
				->add('companyName')
				->add('businessName')
				->add('rfc')
				->add('discount')
			->end()
			->with('Dirección Fiscal', array('expanded' => true,))
				->add('fiscalAddress', 'inodata_address_form', array('label'=>false))
			->end()
			->with('Dirección De Pago', array('expanded' => true,))
				->add('paymentAddress', 'inodata_address_form', array('label'=>false))
			->end();
		;
	}

	/**
	 * @param Sonata\AdminBundle\Show\ShowMapper $showMapper
	 * 
	 * @return void
	 */
	protected function configureShowField(ShowMapper $showMapper)
	{
		$showMapper
			->add('company_name')
			->add('business_name')
			->add('rfc')
			->add('discount')
			->add('fiscal_address')
			->add('payment_address')
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
			->add('companyName')
			->add('businessName')
			->add('rfc')
			->add('discount')
			->add('_action', 'actions', array(
				'actions' => array(
					'view' => array(),
					'edit' => array(),
					'delete' => array(),
				)
			))
		;
	}
	
	/**
	 * @param Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
	 * 
	 * @return void
	 */
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('companyName')
			->add('businessName')
			->add('rfc')
		;		
	}
	
} 