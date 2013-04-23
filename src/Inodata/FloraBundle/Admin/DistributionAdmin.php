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
	
	/**
	 * @param Sonata\AdminBundle\Form\FormMapper $formMapper
	 * 
	 * @return void
	 */
	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->add('id')
			->add('messenger', 'genemu_jqueryselect2_entity', array( 
					'class' => 'Inodata\FloraBundle\Entity\Employee'
					))
			->add('delivery_date', 'date', array(
						'label'=> 'label.delivery_date',
						'widget' => 'single_text',
						'attr' => array(
								'class' => 'inodata_dalivery_date'
						)
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
			->add('messenger', null, array(
					'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('u')
								  ->where('u.messenger IS NULL');
					}
				))
			->addIdentifier('id')
			->add('product')
			->add('deliveryDate')
			->add('status')
			->add('_action', 'actions', array(
				'actions' => array( 
						'Delivered' => array() 
				)
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
			->add('messenger')
			->add('id')
			->add('deliveryDate')
			->add('status')
		;		
	}
	
	
	public function getTemplate($name)
	{
		switch ($name) {
			case 'list':
				return 'InodataFloraBundle:Distribution:list.html.twig';
				break;
			default:
				return parent::getTemplate($name);
				break;
		}
	}
	
	protected function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
	{
		if (!$childAdmin && !in_array($action, array('edit'))) {
			return;
		}
		
		$admin = $this->isChild() ? $this->getParent() : $this;
		$id = $admin->getRequest()->get('id');
		$menu->addChild('delivered', array('uri' => 'http://google.com?id=' . $id));
	}
	
	
} 