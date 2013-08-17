<?php
namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\EntityRepository;

class CollectionAdmin extends Admin {
	
	protected $baseRouteName = 'collection';
	protected $baseRoutePattern = 'collection';
	
	/**
	 * @param Sonata\AdminBundle\Form\FormMapper $formMapper
	 *
	 * @return void
	 */
	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
		->add('collector', 'genemu_jqueryselect2_entity', array(
				'required' => false,
				'empty_value' => '',	
            	'class' => 'Inodata\FloraBundle\Entity\Employee',
				'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('u')
								  ->where('u.jobPosition = \'Collector\'');
				},
				'attr' => array(
					'class' => 'inodata_collector_list span5',
					'placeholder' => 'Selecciona un repartidor',
					'enabled' => 'enabled')
		));
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
				'label' => 'label.order',
		))
		->add('customerAndContact', null, array(
				'label' => 'label.customer',
		))
		->add('collectionDate', null, array(
				'label' => 'label.collection_date',
				'format' => 'd/M/Y'
		))
		->add('orderTotals', null, array(
				'label'	=> 'label.order_total'
		))
		->add('_action', 'actions', array(
				'label' => 'label.distribution_actions',
				'actions' => array(
						'boxcut' => array('template' => 'InodataFloraBundle:Collection:_boxcut_action.html.twig'),)
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
		->add('collector', null, array(
				'label' => 'label.collection_collector',
		), null, array(
				'query_builder' => function(	 $er) {
				return $er->createQueryBuilder('u')
				->where('u.jobPosition = :type ')
				->setParameter("type", "Collector");
		}
		))
		->add('id', null, array(
				'label' => 'label.distribution_id'
		))
		->add('deliveryDate', 'doctrine_orm_date_range', array(
				'label' => 'label.delivery_date'), null,
				array('widget'=>'single_text', 'attr'=>array('class'=>'filter-deliver-date')))
		->add('status', null, array('label' => 'label.distribution_status',),
				  'choice', array( 
				  		'translation_domain' => 'InodataFloraBundle', 
				  		'expanded' => false, 
				  		'multiple' => false,
						'choices' => array( 
								'partiallypayment' => 'label.collection_status_pending',
								'closed' => 'label.collection_status_paid'))
				);
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