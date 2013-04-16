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
				->add('customer', 'sonata_type_model', array(
						'label' => 'Nombre Cliente',
						'empty_value' => '',
						'attr' => array(
								'class' => 'inodata_customer span5',
								'placeholder' => 'Buscar Cliente')
						))
				->add('paymentContact', 'genemu_jqueryselect2_entity',array(
						'label' => 'Contacto de pago',
						'class' => 'Inodata\FloraBundle\Entity\PaymentContact',
						'empty_value' => '',
						'attr' => array(
								'class' => 'inodata_payment_contact span5',
								'placeholder' => 'Seleccionar nombre de contacto'
						)
				))
				->add('Contacto', 'inodata_payment_contact_form', array(
						'label'=>false, 
						'mapped' => false,
						'attr' => array('class' => 'payment_contact_form')
					)
				)
			->end()
			->with('Tarjeta')
				->add('from')
				->add('to')
				->add('category', 'genemu_jqueryselect2_entity', array(
						'class' => 'Inodata\FloraBundle\Entity\Category',
						'empty_value' => '',
						'mapped' => false, 'required' => false,
						'attr' => array(
								'class' => 'inodata_category_day span5',
								'placeholder' => 'Selecciona una fecha festiva'
						)
				))
				->add('messages', 'genemu_jqueryselect2_entity', array(
						'class' => 'Inodata\FloraBundle\Entity\Message',
						'empty_value' => '',
						'mapped' => false, 'required' => false,
						'attr' => array(
								'class' => 'inodata_messages span5',
								'placeholder' => 'Selecciona un mensaje predefinido'
						)
				))
				->add('message', 'ckeditor', array('config_name' => 'inodata_editor',
							'attr' => array('class' => 'inodata_message span5')
						))
			->end()
			->with('Entrega')
				->add('delivery_date', 'date', array(
						'widget' => 'single_text',
						'attr' => array(
								'class' => 'inodata_dalivery_date'
						)
				))				
				->add('to')
				->add('from', null, array(
					'label'=> 'De',)
				)
				->add('to', null, array(
					'label'=> 'Para', 
					)
				)
				->add('message', 'ckeditor', array(
					'label'=> 'Mensaje', 
					'config_name' => 'inodata_editor')
				)
			->end()
			->with('Entrega')
				->add('delivery_date', 'date', array(
					'label'=> 'Fecha de entrega', )
				)				
				->add('to', null, array(
					'label'=> 'Para', )
				)
				->add('shippingAddress', 'inodata_address_form', array('label'=>false))
			->end()
			->with('Productos')
				->add('productos', 'genemu_jqueryselect2_entity', array(
					'label' => 'Buscar producto',
					'class' => 'Inodata\FloraBundle\Entity\Product',
					'mapped' => false,
					'required' => false,
					'empty_value' => '',
					'attr' => array('class'=>'inodata_product span5')))
				->add('products', null, array(
						'attr' => array(
								'class' => 'products-to-buy span5'
						)
				))
				->add('shipping', 'text', array(
					'attr' => array(
						'class' => 'order-shipping'
					)		
				))
				->add('discount', 'text', array(
					'attr' => array(
							'class' => 'order-discount'
					)
				))
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
