<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class OrderAdmin extends Admin
{
	/**
	 * @param Sonata\AdminBundle\Form\FormMapper $formMapper
	 * 
	 * @return void
	 */
	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->with('Buscar cliente')
				->add('customer', 'sonata_type_model')
			->end()
			->with('Tarjeta')
				->add('from')
				->add('to')
				->add('message')
			->end()
			->with('Datos de entrega')
				->add('delivery_date', 'date')
				->add('paymentContact', 'sonata_type_model')
				->add('to')
				->add('shippingAddress', 'inodata_address_form')
			->end()
			->with('Lista de productos')
				->add('products')
			->end()
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
			->add('id')
		;		
	}
	
} 