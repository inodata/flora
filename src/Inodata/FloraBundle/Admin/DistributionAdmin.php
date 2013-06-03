<?php

namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use Symfony\Component\Form\AbstractType;

class DistributionAdmin extends Admin
{
	protected $baseRouteName = 'distribution';
	protected $baseRoutePattern = 'distribution';
	
	protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'messenger'
    );
	
	
	/**
	 * @param Sonata\AdminBundle\Form\FormMapper $formMapper
	 * 
	 * @return void
	 */
	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->add('id')
			->add('messenger', 'sonata_type_model', array( 
					'class' => 'Inodata\FloraBundle\Entity\Employee',
					'attr' => array(
						'class' => 'inodata_messenger span5'
					)
				))
			->add('delivery_date', 'date', array(
						'label'=> 'label.delivery_date'
				))	
		;
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
			->addIdentifier('id', null, array(
					'label' => 'label.distribution_id',
				))
			->add('firstProduct', null, array(
					'label' => 'label.distribution_product',
				))
			->add('deliveryDate', null, array(
					'label' => 'label.delivery_date'
				))
			->add('status', null, array(
					'label' => 'label.distribution_status',
				))
			->add('_action', 'actions', array(
					'label' => 'label.distribution_actions',
					'actions' => array(
						'delivered' => array('template' => 'InodataFloraBundle:Distribution:_delivered_action.html.twig'),
						'closed'  => array('template' => 'InodataFloraBundle:Distribution:_closed_action.html.twig' )
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
			->add('messenger', null, array(
					'label' => 'label.distribution_messenger',
				), null, array(
					'query_builder' => function(	 $er) {
						return $er->createQueryBuilder('u')
								  ->where('u.jobPosition = :type ')
								  ->setParameter("type", "Messenger");
					}	
				))
			->add('id', null, array(
					'label' => 'label.distribution_id'
				))
			->add('deliveryDate', 'doctrine_orm_string', array(
					'label' => 'label.delivery_date'
				))
			->add('status', null, array(
					'label' => 'label.distribution_status',
				),  'choice', array( 'translation_domain' => 'InodataFloraBundle', 'expanded' => false, 'multiple' => false,
						'choices' => array( 'open' => 'label.distribution_delivery_status_open', 
											'intransit' => 'label.distribution_delivery_status_intransit',
											'delivered' => 'label.distribution_delivery_status_delivered',
											'closed' => 'label.distribution_delivery_status_closed') ) )
		;		
	}
	
	protected function configureRoutes(RouteCollection $collection)
	{
		$collection->add('delivered');
		$collection->add('closed');
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
		
		if($this->hasRoute('edit') && $this->isGranted('EDIT') && $this->hasRoute('delete') && $this->isGranted('DELETE')){
			$actions['deliveredAll'] = array(
					'label'            => 'Entregados',
					'ask_confirmation' => true 
			);
		}
		
		return $actions;
	}
	
	
	
	
} 