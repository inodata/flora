<?php
namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Knp\Menu\ItemInterface as MenuItemInterface;

class InoAddressAdmin extends Admin
{
	/**
	 * @param Sonata\AdminBundle\Show\ShowMapper $showMapper
	 * 
	 * @return void
	 */
	protected function configureShowField(ShowMapper $showMapper)
	{
		$showMapper
			->add('street')
			->add('no_int')
			->add('no_ext')
			->add('state_id')
			->add('city')
			->add('postal_code')
			->add('neighborhood')
			;
	}
	
	/**
	 * @param Sonata\AdminBundle\Form\FormMapper $formMapper
	 * 
	 * @return void
	 */
	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
		->with('General')
			->add('street')
			->add('no_int')
		->end()	
	}
	
	/**
	 * @param Sonata\AdminBundle\Datagrid\ListMapper $listMapper
	 * 
	 * @return void
	 */
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->addIdentifier('Address')
			->add('street')
			->add('no_int')
			->add('no_ext')
			->add('state_id')
			->add('city')
			->add('postal_code')
			->add('neighborhood')
			->add('_action', 'actions', array(
				'actions' => array(
					'view' => array(),
					'edit' => array(),
					'delete' => array(),
				)
			))
	}
	
	/**
	 * @param Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
	 * 
	 * @return void
	 */
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		
	}
	
} 