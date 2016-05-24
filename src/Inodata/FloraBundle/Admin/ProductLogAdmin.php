<?php
namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ProductLogAdmin extends Admin
{
	/**
	 * @param Sonata\AdminBundle\Show\ShowMapper $showMapper
	 *
	 * @return void
	 */
	protected function configureShowField(ShowMapper $showMapper)
	{
		$showMapper
			->add('product', 'sonata_type_model')
			->add('lastStock', null, ['label' => 'label.last_stock'])
			->add('stock', null, ['label' => 'label.stock_added'])
			->add('comment', null, ['label' => 'label.comment'])
			->add('createdBy.fullName', null, ['label' => 'label.created_by'])
			->add('createdAt', 'datetime', ['label' => 'label.created_at', 'format' => 'd/M/Y H:i:s'])
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
			->add('product', 'sonata_type_model', ['label' => 'label.product'])
			->add('stock', null, ['label' => 'label.stock_added'])
			->add('comment', null, ['label' => 'label.comment'])
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
			->add('product', 'sonata_type_model', ['label' => 'label.product'])
			->add('stock', null, ['label' => 'label.stock_added'])
			->add('createdBy', null, ['label' => 'label.created_by'])
			->add('createdAt', 'date', ['label' => 'label.created_at', 'format' => 'd/M/Y'])
			->add('_action', 'actions', array(
				'actions' => array(
						'show' => [],
						'edit' => [],
						'delete' => [],
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
			->add('product', null, ['label' => 'label.product'])
			->add('createdBy', null, ['label' => 'label.created_by'])
			->add('createdAt', 'doctrine_orm_datetime_range',
					['label' => 'label.created_at'], "sonata_type_datetime_range",
					['widget' => 'single_text', 'attr' => ['class' => 'datepicker']])
		;
	}

	public function getFilterParameters()
	{
		if ($this->hasRequest()) {
			$filters = $this->request->query->get('filter', array());
			if (isset($filters['createdAt']) && !empty($filters['createdAt']['value']['end'])) {
				$createdAtValue = $filters['createdAt']['value'];
				$timeEnd = date("H:i:s", strtotime($createdAtValue['end']));
				if ($timeEnd == '00:00:00') {
					$createdAtValue['end'] .= " 23:59:59";

					$filters['createdAt']['value'] = $createdAtValue;
					$this->request->query->set("filter", $filters);
				}
			}
		}

		return parent::getFilterParameters();
	}

	public function prePersist($object){
		$object->setLastStock($object->getProduct()->getStock());
		//TODO: resvisar por que no funciona el Gedmo Blameable en el Entity
		$object->setCreatedBy($this->getConfigurationPool()->getContainer()->get("security.context")->getToken()->getUser());

		return $object;
	}

	public function postPersist($object){
		$product = $object->getProduct();

		$product->setStock($product->getStock() + $object->getStock());
		$this->getModelManager()->update($product);

		return $object;
	}
}
