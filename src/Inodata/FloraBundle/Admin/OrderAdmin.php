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
						'label' => 'label.customer',
						'empty_value' => '',
						'attr' => array(
								'class' => 'inodata_customer span5',
								'placeholder' => 'Buscar Cliente')
						))
				->add('paymentContact', 'genemu_jqueryselect2_entity',array(
						'label' => 'label.payment_contact',
						'class' => 'Inodata\FloraBundle\Entity\PaymentContact',
						'empty_value' => '',
						'attr' => array(
								'class' => 'inodata_payment_contact span5',
								'placeholder' => $this->trans('label.contact_empty_list')
						)
				))
				->add('Contacto', 'inodata_payment_contact_form', array(
						'label'=>false, 
						'mapped' => false,
						'attr' => array('class' => 'payment_contact_form')
					)
				)
				->add('status', 'hidden')
			->end()
			->with('Tarjeta')
				->add('from', null, array(
					'label' => 'label.from')
				)
				->add('to', null, array(
					'label'=> 'label.to',)
				)
				->add('category', 'genemu_jqueryselect2_entity', array(
						'label' => 'label.message_category',
						'class' => 'Inodata\FloraBundle\Entity\Category',
						'empty_value' => '',
						'mapped' => false, 'required' => false,
						'attr' => array(
								'class' => 'inodata_category_day span5',
								'placeholder' => $this->trans('label.msg_category_empty_list')
						)
				))
				->add('messages', 'genemu_jqueryselect2_entity', array(
						'label' => 'label.messages_list',
						'class' => 'Inodata\FloraBundle\Entity\Message',
						'empty_value' => '',
						'mapped' => false, 'required' => false,
						'attr' => array(
								'class' => 'inodata_messages span5',
								'placeholder' => $this->trans('Seleccionar un mensaje predefinido')
						)
				))
				->add('message', 'ckeditor', array(
						'label'=> 'label.message',
						'config_name' => 'inodata_editor',
						'attr' => array('class' => 'inodata_message span5')
				))
			->end()
			->with('Entrega')
				->add('delivery_date', 'date', array(
						'label'=> 'label.delivery_date',
						'widget' => 'single_text',
						'attr' => array(
								'class' => 'inodata_dalivery_date'
						)
				))				
				->add('shippingAddress', 'inodata_address_form', array('label'=>false))
			->end()
			->with('Productos')
				->add('invoiceNumber', 'text',array(
					'required' => false,
					'label' => 'label.invoice',
					'attr' => array(
						'class' => 'inodata-invoice-number',
						'style' => 'width:8%;')
				))
				->add('productos', 'genemu_jqueryselect2_entity', array(
					'label' => 'label.search_product',
					'class' => 'Inodata\FloraBundle\Entity\Product',
					'mapped' => false,
					'required' => false,
					'empty_value' => '',
					'attr' => array(
							'placeholder' => $this->trans('label.product_empty_list'),
							'class'=>'inodata_product span5')))
				->add('products', null, array(
						'label' => 'label.product_list',
						'attr' => array(
								'class' => 'products-to-buy span5'
						)
				))
				->add('shipping', 'text', array(
					'label' => 'label.shipping',
					'attr' => array(
						'class' => 'order-shipping'
					)		
				))
				->add('discount', null, array(
					'label' => 'label.discount',
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
			->addIdentifier('id', null, array("label" => "label.order_number"))
			->add('product')
			->add('createdAt', 'date', array(
				"label" => "label.created_at",
				"format" => "dd/mm/yyyy")
			)
			->add('updatedAt', null, array("label" => "label.updated_at"))
			->add('_action', 'actions', array(
				'actions' => array(
					'view' => array(),
					'edit' => array(),
					//'delete' => array(),
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
