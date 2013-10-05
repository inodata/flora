<?php
namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PartnerAdmin extends Admin
{
	/**
	 * @param Sonata\AdminBundle\Form\FormMapper $formMapper
	 *
	 * @return void
	 */
	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->add('code', null, array('label' => 'label.code'))
			->add('name', null, array('label' => 'label.name'))
			->add('phone', null, array('label' => 'label.phone'))
			->add('email', null, array('label' => 'label.email'))
			->with('label.address', array('collapsed'=>false, 'label' => 'label.address'))
				->add('address', 'inodata_address_form', array('label' => 'label.address'))
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
			->add('code', null, array('label' => 'label.code'))
			->add('name', null, array('label' => 'label.name'))
			->add('phone', null, array('label' => 'label.phone'))
			->add('email', null, array('label' => 'label.email'))
			->add('_action', 'actions', array(
				'actions' => array(
					'edit' => array(),
					'delete' => array(),
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
			->add('code', null, array('label' => 'label.code'))
			->add('name', null, array('label' => 'label.name'))
		;		
	}
}