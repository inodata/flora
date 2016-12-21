<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
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
            ->with('label.general', ['expanded' => true])
                ->add('businessName', null, ['label' => 'label.business_name'])
                ->add('companyName', null, ['label' => 'label.company_name'])
                ->add('rfc', null, ['label' => 'label.rfc'])
                ->add('discount', null, ['label' => 'label.discount'])
                ->add('paymentCondition', 'text', ['label' => 'label.payment_condition',
                            'required'                     => false,
                        ]
                )
            ->end()
            ->with('label.fiscal_address', ['expanded' => false])
                ->add('usePasymentAddress', 'checkbox', [
                    'label'    => 'label.use_payment_address',
                    'mapped'   => false,
                    'required' => false,
                    'attr'     => ['class' => 'use-payment-address'],
                ])
                ->add('fiscalAddress', 'inodata_address_form', ['label'=>false])
            ->end()
            ->with('label.payment_address', ['expanded' => true])
                ->add('useFiscalAddress', 'checkbox', [
                        'label'    => 'label.use_fiscal_address',
                        'mapped'   => false,
                        'required' => false,
                        'attr'     => ['class'=>'use-fiscal-address'], ])
                ->add('paymentAddress', 'inodata_address_form', ['label'=>false])
            ->end()
            ->with('label.more_addresses')
                ->add('addresses', 'sonata_type_collection',
                    [
                        'label'        => 'label.extra_addresses',
                        'required'     => false,
                        'by_reference' => false,
                    ],
                    [
                        'edit'         => 'inline',
                        'inline'       => 'table',
                        'allow_delete' => true,
                    ]
                )
            ->end();
    }

    /**
     * @param Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('company_name', null, ['label' => 'label.company_name'])
            ->add('business_name', null, ['label' => 'label.business_name'])
            ->add('rfc', null, ['label' => 'label.rfc'])
            ->add('discount', null, ['label' => 'label.discount'])
            ->add('fiscal_address', null, ['label' => 'label.fiscal_address'])
            ->add('payment_address', null, ['label' => 'label.payment_address']);
    }

    /**
     * @param Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('companyName', null, ['label' => 'label.company_name'])
            ->add('businessName', null, ['label' => 'label.business_name'])
            ->add('rfc', null, ['label' => 'label.rfc'])
            ->add('discount', null, ['label' => 'label.discount'])
            ->add('_action', 'actions', ['label'=> 'label.action',
                'actions'                       => [
                    'edit'   => [],
                    'delete' => [],
                ],
            ]);
    }

    /**
     * @param Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('companyName', null, ['label' => 'label.company_name'])
            ->add('businessName', null, ['label' => 'label.business_name'])
            ->add('rfc', null, ['label' => 'label.rfc']);
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

    public function prePersist($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            $address->setCustomer($customer);
        }
    }

    public function preUpdate($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            $address->setCustomer($customer);
        }
    }
}
