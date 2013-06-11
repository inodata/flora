<?php
namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

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
			->add('number')
			->add('order')
			->add('creator')
			->add('isCanceled')
			->add('comment')
			->add('canceledBy')
			//->add('createdAt')
			//->add('updatedAt')
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
			->add('number')
			->add('order')
			->add('creator')
			->add('isCanceled')
			->add('comment')
			->add('canceledBy')
			//->add('createdAt')
			//->add('updatedAt')
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
			->add('number')
			->add('order')
			->add('creator')
			->add('isCanceled')
			->add('comment')
			->add('canceledBy')
			//->add('createdAt')
			//->add('updatedAt')
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
			->add('number')
			->add('order')
			->add('creator')
			->add('isCanceled')
			->add('comment')
			->add('canceledBy')
			//->add('createdAt')
			//->add('updatedAt')
		;
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