<?php

namespace Inodata\FloraBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CollectionAdmin extends Admin
{
    protected $baseRouteName = 'collection';
    protected $baseRoutePattern = 'inodata/flora/collection';

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('collector', 'genemu_jqueryselect2_entity', [
                'required'      => false,
                'empty_value'   => '',
                'class'         => 'Inodata\FloraBundle\Entity\Employee',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.jobPosition = \'Collector\'');
                },
                'attr'          => [
                    'class'       => 'inodata_collector_list span5',
                    'placeholder' => 'Selecciona un cobrador',
                    'enabled'     => 'enabled', ],
            ]);
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
            ->addIdentifier('id', null, [
                'label' => 'label.order',
            ])
            ->add('customerAndContact', null, [
                'label' => 'label.customer',
            ])
            ->add('collectionDate', null, [
                'label'  => 'label.collection_date',
                'format' => 'd/M/Y',
            ])
            ->add('orderTotals', null, [
                'label' => 'label.order_total',
            ])
            ->add('_action', 'actions', [
                'label'   => 'label.distribution_actions',
                'actions' => [],
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
            ->add('collector', null, [
                'label' => 'label.collection_collector',
            ], null, [
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.jobPosition = :type ')
                        ->setParameter('type', 'Collector');
                },
            ])
            ->add('id', null, [
                'label' => 'label.distribution_id',
            ])
            ->add('deliveryDate', 'doctrine_orm_date_range', [
                'label' => 'label.collection_filter_date', ], null,
                ['widget' => 'single_text', 'attr' => ['class' => 'filter-deliver-date']])
            ->add('status', null, ['label' => 'label.distribution_status'],
                'choice', [
                    'translation_domain' => 'InodataFloraBundle',
                    'expanded'           => false,
                    'multiple'           => false,
                    'choices'            => [
                        'partiallypayment' => 'label.collection_status_pending',
                        'closed'           => 'label.collection_status_paid', ], ]
            );
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'list':
                return 'InodataFloraBundle:Collection:list.html.twig';
                break;
            //case 'print':
            //	return 'InodataFloraBundle:Collection:print_distribution.html.twig';
            default:
                return parent::getTemplate($name);
                break;
        }
    }
}
