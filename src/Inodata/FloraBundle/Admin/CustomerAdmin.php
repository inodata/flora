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
			->with('label.general', array('expanded' => true,))
				->add('businessName', null, array('label' => 'label.business_name'))
				->add('companyName', null, array('label' => 'label.company_name'))
				->add('rfc', null, array('label' => 'label.rfc'))
				->add('discount', null, array('label' => 'label.discount'))
				->add('paymentCondition', 'text', array('label' => 'label.payment_condition',
							'required' => false
						)
				)
			->end()
			->with('label.fiscal_address', array('expanded' => false,))
				->add('usePasymentAddress', 'checkbox', array(
					'label' => 'label.use_payment_address',
					'mapped' => false,
					'required' => false,
					'attr' => array('class' => 'use-payment-address')	
				))
				->add('fiscalAddress', 'inodata_address_form', array('label'=>false))
			->end()
			->with('label.payment_address', array('expanded' => true,))
				->add('useFiscalAddress', 'checkbox', array(
						'label' => 'label.use_fiscal_address',
						'mapped' => false,
						'required' => false,
						'attr' => array('class'=>'use-fiscal-address')))
				->add('paymentAddress', 'inodata_address_form', array('label'=>false))
			->end()
			->with('label.more_addresses')
				/*TODO: Intentar implementar con sonata_type_collection
				->add('addresses','sonata_type_collection' ,array(
					'label' => 'label.extra_addresses',
					'required' => false,
					
					)					
				)*/
				->add('addresses','collection' ,array(
					'label' => 'label.extra_addresses',
					'required' => false,
					'type' => new \Inodata\FloraBundle\Form\Type\AddressType,
				    'allow_add' => true,
				    'allow_delete' => true,
				    'by_reference' => true,
				    /*'prototype_name' => 'label_address',
				    'prototype' => true,*/
					)
				)
			->end()
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
			->add('company_name', null, array('label' => 'label.company_name'))
			->add('business_name', null, array('label' => 'label.business_name'))
			->add('rfc', null, array('label' => 'label.rfc'))
			->add('discount', null, array('label' => 'label.discount'))
			->add('fiscal_address', null, array('label' => 'label.fiscal_address'))
			->add('payment_address', null, array('label' => 'label.payment_address'))
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
			->add('companyName', null, array('label' => 'label.company_name'))
			->add('businessName', null, array('label' => 'label.business_name'))
			->add('rfc', null, array('label' => 'label.rfc'))
			->add('discount', null, array('label' => 'label.discount'))
			->add('_action', 'actions', array('label'=> 'label.action',
				'actions' => array(
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
			->add('companyName', null, array('label' => 'label.company_name'))
			->add('businessName', null, array('label' => 'label.business_name'))
			->add('rfc', null, array('label' => 'label.rfc'))
		;		
	}
	
	public function getTemplate($name)
	{
		switch ($name) {
			case 'edit':
				return 'InodataFloraBundle:Customer:edit.html.twig';
				break;
			default:
				return parent::getTemplate($name);
				break;
		}
	}

	/*public function prePersist($customer)
	{
	    foreach ($customer->getAddresses() as $address) {
	        $customer->addAddress($address);
	    }
	}
	 
	public function preUpdate($customer)
	{
	    foreach ($customer->getAddresses() as $address) {
	        $address->setCustomer($customer);
	    }
	    $customer->setAddresses($customer->getAddresses());
	}*/
} 