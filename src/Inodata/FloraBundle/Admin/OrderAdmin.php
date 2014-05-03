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
			->add('delivery_date', 'date', array(
				'label'=> 'label.delivery_date',
				//'help' => 'help.delivery_date',
				'widget' => 'single_text',
				'format' => 'd/MM/y',
				'attr' => array(
					'class' => 'inodata_delivery_date',
                    'title' => $this->trans('help.delivery_date')
				)
			))
			->add('to', null, array(
				'label'=> 'label.to',
				'attr' => array(
					'class' => 'inodata_to',
                    'placeholder' => $this->trans('label.placeholder_to'),
                    'title' => $this->trans('help.to')
				)
			))
			->add('from', null, array(
				'label' => 'label.from',
				'attr' => array(
					'class' => 'inodata_from',
                    'placeholder' => $this->trans('label.placeholder_from'),
                    'title' => $this->trans('help.from')
				)
			))
            ->add('reporter', null, array(
				'label' => 'label.reporter',
                'attr' => array(
                    'placeholder' => $this->trans('label.placeholder_reporter'),
                    'title' => $this->trans('help.reporter')
                )
			))
            ->add('shippingAddress', 'inodata_address_form', array(
				'label'=>false,
				'attr' => array(
					'class' => 'inodata-shipping-address',
				)
			))
			->add('customer', 'sonata_type_model', array(
				'label' => 'label.customer',
				'empty_value' => '',
				'attr' => array(
					'class' => 'inodata_customer',
					'placeholder' => $this->trans("label.placeholder_customer"),
					'allowClear' => 'true',
                    'title' => $this->trans('help.customer'),
                    'style' => 'width:270px;'
				)
			))
			->add('purchaseOrder', null, array(
                'label' => 'label.purchase_order',
                'attr' => array(
                    'title' => $this->trans('help.purchase_order')
                )
			))
			->add('paymentContact', 'genemu_jqueryselect2_entity',array(
				'label' => 'label.payment_contact',
				'class' => 'Inodata\FloraBundle\Entity\PaymentContact',
				'empty_value' => '',
				'attr' => array(
					'class' => 'inodata_payment_contact',
					'placeholder' => $this->trans('label.contact_empty_list'),
                    'title' => $this->trans('help.payment_contact')
				)
			))
			->add('contact', 'inodata_payment_contact_form', array(
					'label'=>false, 
					'mapped' => false,
					'attr' => array('class' => 'payment_contact_form')
				)
			)
            ->add('message', 'ckeditor', array(
					'label'=> 'label.message',
					'config_name' => 'inodata_editor',
                    'config' => array(
                        'uiColor' => '#ffffff',
                        'height' => '50px'
                    ),
					'attr' => array(
                        'class' => 'inodata_message',
                        'title' => $this->trans('help.message')
                    )
			))
			->add('category', 'genemu_jqueryselect2_entity', array(
					'label' => 'label.message_category',
					'class' => 'Inodata\FloraBundle\Entity\Category',
					'empty_value' => '',
					'mapped' => false, 'required' => false,
					'attr' => array(
                        'class' => 'inodata_category_day',
                        'placeholder' => $this->trans('label.msg_category_empty_list'),
                        'title' => $this->trans('help.category')
					)
			))
            ->add('messages', 'genemu_jqueryselect2_entity', array(
					'label' => 'label.messages_list',
					'class' => 'Inodata\FloraBundle\Entity\Message',
					'empty_value' => '',
					'mapped' => false, 'required' => false,
					'attr' => array(
                        'class' => 'inodata_messages',
                        'placeholder' => $this->trans('Seleccionar un mensaje predefinido'),
                        'title' => $this->trans('help.messages_list')
					)
			))
			/**
		 	* Si se agrega un elemento antes de este item, actualizar main.css
			* para no romper el acomodo de acuerdo al index
			*/
			->add('products', 'ajax_entity', array(
				'label' => 'label.search_product',
				'class' => 'InodataFloraBundle:Product',
				'mapped' => false,
				'required' => false,
				'empty_value' => '',
				'attr' => array(
                    'placeholder' => $this->trans('label.product_empty_list'),
                    'class'=>'inodata_product',
                    'entity' => 'InodataFloraBundle:Product', 'columns' => 'id,description',
                    'title' => $this->trans('help.products_list')
                )
            ))
			->add('hasInvoice', 'checkbox', array(
				'label' => 'label.need_invoice',
				'required' => false,
				'attr'=>array(
                    'class' => 'inodata-has-invoice',
                    'title' => $this->trans('help.require_invoice')
                    )
			))
			->add('order_notes', null, array(
				'label' => 'label.order_notes',
				'attr' => array(
					'class' => 'inodata-order-notes',
					'style' => 'width:95%',
                    'title' => $this->trans('help.order_notes')
				)
			))
			->add('shipping', 'text', array(
				'label' => 'label.shipping',
				'attr' => array(
                    'class' => 'order-shipping',
                    'title' => $this->trans('help.shipping')
                    )		
			))
			->add('discount', 'text', array(
				'label' => 'label.discount',
				'attr' => array(
                    'class' => 'order-discount',
                    'title' => $this->trans('help.discount')
                )
			))
			->with('tab.invoice', array('description' => 'help.tab_invoice'))
				->add('invoiceNumber', 'text',array(
					'required' => false,
					'label' => 'label.invoice',
					'attr' => array(
						'class' => 'inodata-invoice-number',
						'style' => 'width:98%')
				))
				->add('paymentCondition', 'hidden', array(
						'required' => false,
						'label' => 'label.payment_condition',
						'attr' => array(
							'class' => 'inodata-payment-condition',
							'style' => 'width:98%'
						)
					))
				->add('invoiceComment', 'hidden', array(
						'required' => false,
						'label' => 'label.invoice_comment',
						'attr' => array(
							'class' => 'inodata-invoice-comment',
							'style' => 'width:100%'
						)
				))
				->add('invoice_date', 'date', array(
						'required' => false,
						'label'=> 'label.invoice_date',
						'widget' => 'single_text',
						'format' => 'd/MM/y',
						'attr' => array(
							'class' => 'inodata_invoice_date',
                            'title' => $this->trans('help.invoice_date')
						)
				))
			->end()
			->add('id', 'hidden', array(
					'attr' => array('class' => "order-id")
			))
			->add('status', 'hidden')
		;
	}

	/**
	* Determina el ordenamiento por default en el listado
	*/
	protected $datagridValues = array(
        '_page'       => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by' => 'id' // field name
    );
	
	/**
	 * @param Sonata\AdminBundle\Datagrid\ListMapper $listMapper
	 * 
	 * @return void
	 */
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->addIdentifier('id', null, array("label" => "label.order_number"))
			->add('createdAt', 'date', array(
				"label" => "label.created_at",
				"format" => "d/M/Y")
			)
			->add('customer', null, array('label'=>'label.customer'))
			->add('firstProduct', null, array("label" => "label.details"))
			->add('firstProductPrice', null, array('label'=>'label.price'))
			->add('shipping', null, array('label'=>'label.shipping'))			
			->add('deliveryDate', null, array(
					"label" => "label.delivery_date",
					"format" => "d/M/Y"))
			->add('paymentContact', null, array('label' => 'label.payment_contact'))
			->add('messenger', null, array('label' => 'label.messenger'))
			->add('collector', null, array('label' => 'label.collector'))
			->add('_action', 'actions', array(
					'label' => 'label.action',
					'actions' => array(
						'edit' => array())
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
			->add('id', null, array('label' => 'label.order_number'))
			->add('to', null, array('label' => 'label.to'))
			->add('from', null, array('label' => 'label.from'))
			->add('customer.companyName', null, array('label'=>'label.customer'))
			->add('purchaseOrder', null, array('label' => 'label.purchase_order'))
			->add('creator', null, array('label' => 'label.capturated'))
			->add('createdAt', 'doctrine_orm_string', array(
				'label' => 'label.created_at',
			))
			->add('deliveryDate', 'doctrine_orm_string', array(
					'label' => 'label.delivery_date',
			))
			->add('hasInvoice', null, array('label' => 'label.has_invoice'));
	}

	public function getTemplate($name)
	{
		switch ($name) {
	        case 'edit':
	            	return 'InodataFloraBundle:Order:edit.html.twig';
	            break;
	        case 'list':
	        	return 'InodataFloraBundle:Order:list.html.twig';
	        	break;
	        default:
	            	return parent::getTemplate($name);
	            break;
		}
	}
	
	public function setExportFields()
	{
		$results = $this->getModelManager()->getExportFields($this->getClass());
	
		// Need to add again our foreign key field here
		$results[] = 'id'; 
	
		return $results;
	}
	
	public function setSecurityContext($securityContext) {
		$this->securityContext = $securityContext;
	}
	
	public function getSecurityContext() {
		return $this->securityContext;
	}
	
	public function prePersist($order) {
		$user = $this->getSecurityContext()->getToken()->getUser();
		$order->setCreator($user);
	}
}
