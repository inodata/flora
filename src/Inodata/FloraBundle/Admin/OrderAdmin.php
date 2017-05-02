<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

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
            ->add('delivery_date', 'date', [
                'label'  => 'label.delivery_date',
                //'help' => 'help.delivery_date',
                'widget' => 'single_text',
                'format' => 'd/MM/y',
                'attr'   => [
                    'class' => 'inodata_delivery_date',
                    'title' => $this->trans('help.delivery_date'),
                ],
            ])
            ->add('to', null, [
                'label' => 'label.to',
                'attr'  => [
                    'class'       => 'inodata_to',
                    'placeholder' => $this->trans('label.placeholder_to'),
                    'title'       => $this->trans('help.to'),
                ],
            ])
            ->add('from', null, [
                'label' => 'label.from',
                'attr'  => [
                    'class'       => 'inodata_from',
                    'placeholder' => $this->trans('label.placeholder_from'),
                    'title'       => $this->trans('help.from'),
                ],
            ])
            ->add('reporter', null, [
                'label' => 'label.reporter',
                'attr'  => [
                    'placeholder' => $this->trans('label.placeholder_reporter'),
                    'title'       => $this->trans('help.reporter'),
                ],
            ])
            ->add('shippingAddress', 'inodata_address_form', [
                'label' => false,
                'attr'  => [
                    'class' => 'inodata-shipping-address',
                ],
            ])
            /*
            ->add('customer', 'ajax_entity', [
                'label'       => 'label.customer',
                'class'       => 'InodataFloraBundle:Customer',
                'mapped'      => false,
                'empty_value' => '',
                'attr'        => [
                    'placeholder' => $this->trans('label.placeholder_customer'),
                    'allowClear'  => 'true',
                    'class'       => 'inodata_customer',
                    'entity'      => 'InodataFloraBundle:Customer', 'columns' => 'id,companyName',
                    'title'       => $this->trans('help.customer'),
                ],
            ])*/
            ->add('customer', 'sonata_type_model', [
                'label'       => 'label.customer',
                'empty_value' => '',
                'attr'        => [
                    'class'       => 'inodata_customer',
                    'placeholder' => $this->trans('label.placeholder_customer'),
                    'allowClear'  => 'true',
                    'title'       => $this->trans('help.customer'),
                    'style'       => 'width:270px;',
                ],
            ])
            ->add('purchaseOrder', null, [
                'label' => 'label.purchase_order',
                'attr'  => [
                    'title' => $this->trans('help.purchase_order'),
                ],
            ])
            /*
            ->add('paymentContact', 'ajax_entity', [
                'label'       => 'label.payment_contact',
                'class'       => 'InodataFloraBundle:PaymentContact',
                'empty_value' => '',
                'attr'        => [
                    'class'       => 'inodata_payment_contact',
                    'entity'      => 'InodataFloraBundle:PaymentContact', 'columns' => 'id,name',
                    'placeholder' => $this->trans('label.contact_empty_list'),
                    'allowClear'  => 'true',
                    'title'       => $this->trans('help.payment_contact'),
                ],
            ])*/
            ->add('paymentContact', 'genemu_jqueryselect2_entity', [
                'label'       => 'label.payment_contact',
                'class'       => 'Inodata\FloraBundle\Entity\PaymentContact',
                'empty_value' => '',
                'attr'        => [
                    'class'       => 'inodata_payment_contact',
                    'placeholder' => $this->trans('label.contact_empty_list'),
                    'title'       => $this->trans('help.payment_contact'),
                ],
            ])
            ->add('contact', 'inodata_payment_contact_form', [
                    'label'  => false,
                    'mapped' => false,
                    'attr'   => ['class' => 'payment_contact_form'],
                ]
            )
            ->add('message', 'ckeditor', [
                'label'       => 'label.message',
                'config_name' => 'inodata_editor',
                'config'      => [
                    'uiColor' => '#ffffff',
                    'height'  => '50px',
                ],
                'attr'        => [
                    'class' => 'inodata_message',
                    'title' => $this->trans('help.message'),
                ],
            ])
            ->add('category', 'genemu_jqueryselect2_entity', [
                'label'       => 'label.message_category',
                'class'       => 'Inodata\FloraBundle\Entity\Category',
                'empty_value' => '',
                'mapped'      => false, 'required' => false,
                'attr'        => [
                    'class'       => 'inodata_category_day',
                    'placeholder' => $this->trans('label.msg_category_empty_list'),
                    'title'       => $this->trans('help.category'),
                ],
            ])
            ->add('messages', 'genemu_jqueryselect2_entity', [
                'label'       => 'label.messages_list',
                'class'       => 'Inodata\FloraBundle\Entity\Message',
                'empty_value' => '',
                'mapped'      => false, 'required' => false,
                'attr'        => [
                    'class'       => 'inodata_messages',
                    'placeholder' => $this->trans('Seleccionar un mensaje predefinido'),
                    'title'       => $this->trans('help.messages_list'),
                ],
            ])
            /*
             * Si se agrega un elemento antes de este item, actualizar main.css
            * para no romper el acomodo de acuerdo al index
            */
            ->add('products', 'ajax_entity', [
                'label'       => 'label.search_product',
                'class'       => 'InodataFloraBundle:Product',
                'mapped'      => false,
                'required'    => false,
                'empty_value' => '',
                'attr'        => [
                    'placeholder' => $this->trans('label.product_empty_list'),
                    'class'       => 'inodata_product',
                    'entity'      => 'InodataFloraBundle:Product', 'columns' => 'id,description',
                    'title'       => $this->trans('help.products_list'),
                ],
            ])
            ->add('hasInvoice', 'checkbox', [
                'label'    => 'label.need_invoice',
                'required' => false,
                'attr'     => [
                    'class' => 'inodata-has-invoice',
                    'title' => $this->trans('help.require_invoice'),
                ],
            ])
            ->add('order_notes', null, [
                'label' => 'label.order_notes',
                'attr'  => [
                    'class' => 'inodata-order-notes',
                    'style' => 'width:95%',
                    'title' => $this->trans('help.order_notes'),
                ],
            ])
            ->add('shipping', 'text', [
                'label' => 'label.shipping',
                'attr'  => [
                    'class' => 'order-shipping',
                    'title' => $this->trans('help.shipping'),
                ],
            ])
            ->add('discount', 'text', [
                'label' => 'label.discount',
                'attr'  => [
                    'class' => 'order-discount',
                    'title' => $this->trans('help.discount'),
                ],
            ])->end()
            ->with('tab.invoice', ['description' => 'help.tab_invoice'])
            ->add('invoiceNumber', 'text', [
                'required' => false,
                'label'    => 'label.invoice',
                'attr'     => [
                    'class' => 'inodata-invoice-number',
                    'style' => 'width:98%',],
            ])
            ->add('paymentCondition', 'hidden', [
                'required' => false,
                'label'    => 'label.payment_condition',
                'attr'     => [
                    'class' => 'inodata-payment-condition',
                    'style' => 'width:98%',
                ],
            ])
            ->add('invoiceComment', 'hidden', [
                'required' => false,
                'label'    => 'label.invoice_comment',
                'attr'     => [
                    'class' => 'inodata-invoice-comment',
                    'style' => 'width:100%',
                ],
            ])
            ->add('invoice_date', 'date', [
                'required' => false,
                'label'    => 'label.invoice_date',
                'widget'   => 'single_text',
                'format'   => 'd/MM/y',
                'attr'     => [
                    'class' => 'inodata_invoice_date',
                    'title' => $this->trans('help.invoice_date'),
                ],
            ])
            ->end()
            ->add('id', 'hidden', [
                'attr' => ['class' => 'order-id'],
            ])
            ->add('status', 'hidden');
    }

    /**
     * Determina el ordenamiento por default en el listado.
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by'    => 'id', // field name
    ];

    /**
     * @param Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'label.order_number'])
            ->add('createdAt', 'date', [
                    'label'  => 'label.created_at',
                    'format' => 'd/M/Y',]
            )
            ->add('customer', null, ['label' => 'label.customer'])
            ->add('firstProduct', null, ['label' => 'label.details'])
            ->add('firstProductPrice', null, ['label' => 'label.price'])
            ->add('shipping', null, ['label' => 'label.shipping'])
            ->add('deliveryDate', null, [
                'label'  => 'label.delivery_date',
                'format' => 'd/M/Y',])
            ->add('paymentContact', null, ['label' => 'label.payment_contact'])
            ->add('messenger', null, ['label' => 'label.messenger'])
            ->add('collector', null, ['label' => 'label.collector'])
            ->add('_action', 'actions', [
                    'label'   => 'label.action',
                    'actions' => [
                        'edit' => [],],
                ]
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
            ->add('id', null, ['label' => 'label.order_number'])
            ->add('to', null, ['label' => 'label.to'])
            ->add('from', null, ['label' => 'label.from'])
            ->add('customer.companyName', null, ['label' => 'label.customer'])
            ->add('purchaseOrder', null, ['label' => 'label.purchase_order'])
            ->add('creator', null, ['label' => 'label.capturated'])
            ->add('createdAt', 'doctrine_orm_string', [
                'label' => 'label.created_at',
            ])
            ->add('deliveryDate', 'doctrine_orm_string', [
                'label' => 'label.delivery_date',
            ])
            ->add('hasInvoice', null, ['label' => 'label.has_invoice']);
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

    public function setSecurityContext($securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function getSecurityContext()
    {
        return $this->securityContext;
    }

    public function prePersist($order)
    {
        $user = $this->getSecurityContext()->getToken()->getUser();
        $order->setCreator($user);
    }
}