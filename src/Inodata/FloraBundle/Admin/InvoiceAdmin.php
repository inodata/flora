<?php
namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class InvoiceAdmin extends Admin
{
	/**
	 * @param Sonata\AdminBundle\Show\ShowMapper $showMapper
	 *
	 * @return void
	 */
	protected function configureShowField(ShowMapper $showMapper)
	{
		$showMapper
			->add('number', null, array(
					'label' => 'label.number'))
			->add('order', null, array('label' => 'label.order'))
			->add('creator', null, array('label' => 'label.creator'))
			->add('createdAt', null, array('label' => 'label.created_at'))
			->add('isCanceled', null, array('label' => 'label.is_canceled'))
			->add('comment', 'text', array('label' => 'label.comment'))
			->add('canceledBy', null, array('label' => 'label.canceled_by'))
			->add('updatedAt', null, array('label' => 'label.canceled_at'))
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
			->add('isCanceled', null, array(
					'label' => 'label.is_canceled',
					'required'=>true,
					'attr' => array('checked' => 'checked')
			))
			->add('comment', 'textarea', array('label' => 'label.comment'));
	}
	
	/**
	 * @param Sonata\AdminBundle\Datagrid\ListMapper $listMapper
	 *
	 * @return void
	 */
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->add('number', null, array('label' => 'label.number'))
			->add('order', null, array('label' => 'label.order'))
			->add('creator', null, array('label' => 'label.creator'))
			->add('isCanceled', null, array('label' => 'label.is_canceled'))
			->add('comment', 'text', array('label' => 'label.comment'))
			->add('canceledBy', null, array('label' => 'label.canceled_by'))
			->add('createdAt', null, array('label' => 'label.created_at'))
			->add('updatedAt', null, array('label' => 'label.canceled_at'))
			->add('_action', 'actions', array(
					'actions' => array(
							'view' => array(),
							'edit' => array(),
					)
			));
		;
	}
	
	/**
	 * @param Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
	 *
	 * @return void
	 */
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('number', null, array('label' => 'label.number'))
			->add('order', null, array('label' => 'label.order'))
			->add('creator', null, array('label' => 'label.creator'))
			->add('isCanceled', null, array('label' => 'label.is_canceled'))
			->add('canceledBy', null, array('label' => 'label.canceled_by'))
		;
	}
	
	protected function configureRoutes(RouteCollection $collection)
	{
		$collection
			->remove('create')
			->remove('delete');
	
	}
	
	public function setSecurityContext($securityContext) {
		$this->securityContext = $securityContext;
	}
	
	public function getSecurityContext() {
		return $this->securityContext;
	}
	
	public function prePersist($order) {
		$user = $this->getSecurityContext()->getToken()->getUser();
		$order->setCreator($user);
	}
}