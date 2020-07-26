<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class DistributionAdmin extends Admin
{
    protected $baseRouteName = 'distribution';
    protected $baseRoutePattern = 'inodata/flora/distribution';

    protected $datagridValues = [
        '_sort_order' => 'ASC',
        '_sort_by'    => 'messenger',
    ];

    /**
     * @param Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('messenger', 'sonata_type_model', [
                'class' => 'Inodata\FloraBundle\Entity\Employee',
                'attr'  => [
                    'class' => '	 span5', ],
            ])
            ->add('delivery_date', 'date', [
                'label' => 'label.delivery_date',
            ])
            ->add('status', null);
    }

    /**
     * @param Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {

        /* TODO: Agregar comentarios */
        $listMapper
            ->add('messenger', null, [
                'label' => 'label.messenger',
            ])
            ->addIdentifier('id', 'entity', [
                'label' => 'label.distribution_id', 'admin_code' => 'admin.order',
            ])
            ->add('firstProduct', null, [
                'label' => 'label.distribution_product',
            ])
            ->add('deliveryDate', null, [
                'label'  => 'label.delivery_date',
                'format' => 'd/M/Y',
            ])
            ->add('status', null, [
                'label' => 'label.distribution_status',
                'attr'  => ['class' => 'status'],
            ])
            ->add('_action', 'actions', [
                    'label'   => 'label.distribution_actions',
                    'actions' => [
                        'delivered' => ['template' => 'InodataFloraBundle:Distribution:_delivered_action.html.twig'],
                        'remove'    => ['template' => 'InodataFloraBundle:Distribution:_closed_action.html.twig'],
                    ],
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
            ->add('messenger', null, [
                'label' => 'label.distribution_messenger',
            ], null, [
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.jobPosition = :type ')
                        ->setParameter('type', 'Messenger');
                },
            ])
            ->add('id', null, [
                'label' => 'label.distribution_id',
            ])
            ->add('deliveryDate', 'doctrine_orm_string', [
                'label' => 'label.delivery_date',
            ])
            ->add('status', null, [
                'label'                        => 'label.distribution_status',
            ], 'choice', ['translation_domain' => 'InodataFloraBundle', 'expanded' => false, 'multiple' => false,
                          'choices'            => ['open'      => 'label.distribution_delivery_status_open',
                                                   'intransit' => 'label.distribution_delivery_status_intransit',
                                                   'delivered' => 'label.distribution_delivery_status_delivered',
                                                   'closed'    => 'label.distribution_delivery_status_closed', ], ]);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('delivered');
        $collection->add('open');
        $collection->add('print');
        $collection->remove('create');
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'list':
                return 'InodataFloraBundle:Distribution:list.html.twig';
                break;
            case 'print':
                return 'InodataFloraBundle:Distribution:print_distribution.html.twig';
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        if ($this->hasRoute('edit') && $this->isGranted('EDIT') && $this->hasRoute('delete') && $this->isGranted('DELETE')) {
            $actions['deliveredAll'] = [
                'label'            => 'Entregados',
                'ask_confirmation' => true,
            ];
        }

        return $actions;
    }
}
