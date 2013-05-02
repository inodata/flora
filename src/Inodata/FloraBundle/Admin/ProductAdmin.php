<?php
namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ProductAdmin extends Admin
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
			->add('description', null, array('label' => 'label.description'))
			->add('price', null, array('label' => 'label.price'))
			->add('stock', null, array('label' => 'label.stock'))
			->add('categories', null, array('label' => 'label.categories'))
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
		->add('description',  null, array('label' => 'label.description'))
		->add('price', null, array('label' => 'label.price'))
		->add('stock', null, array('label' => 'label.stock'))
		->add('_action', 'actions', array(
				'actions' => array(
						'edit' => array(),
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
			->add('description', null, array('label' => 'label.description'))
			->add('categories', null, array('label' => 'label.categories'))
			->add('price', null, array('label' => 'label.price'))
		;
	}
}