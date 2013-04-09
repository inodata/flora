<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
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
			->with('Cliente')
				->add('id', 'hidden', array(
						'attr' => array('class' => "order-id")
						))
				->add('customer', 'genemu_jqueryselect2_entity', array(
					'class' => 'Inodata\FloraBundle\Entity\Customer',
					'attr' => array(
						'class' => 'inodata_customer span5',
						'placeholder' => 'Selecciona un cliente'
						)
					)
				)
				->add('paymentContact', 'inodata_payment_contact_form')
			->end()
			->with('Tarjeta')
				->add('from')
				->add('to')
				->add('message', 'ckeditor', array('config_name' => 'inodata_editor'))
			->end()
			->with('Entrega')
				->add('delivery_date', 'date')				
				->add('to')
				->add('shippingAddress', 'inodata_address_form')
			->end()
			->with('Productos')
				->add('productos', 'genemu_jqueryselect2_entity', array(
					'class' => 'Inodata\FloraBundle\Entity\Product',
					'mapped' => false,
					'required' => false,
					'empty_value' => '',
					'attr' => array('class'=>'inodata_product span5')))
				->add('products', null, array(
						'attr' => array(
								'class' => 'products-to-buy span5'
								//'style' => 'display:none'
								)
					)
				)
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
			)
		);
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

	public function getTemplate($name)
	{
		switch ($name) {
	        case 'edit':
	            	return 'InodataFloraBundle:Order:edit.html.twig';
	            break;
	        default:
	            	return parent::getTemplate($name);
	            break;
		}
	}
} 
