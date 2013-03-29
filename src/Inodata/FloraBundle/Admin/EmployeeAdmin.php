<?php
namespace Inodata\FloraBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class EmployeeAdmin extends Admin
{
	/**
	 * @param Sonata\AdminBundle\Show\ShowMapper $showMapper
	 *
	 * @return void
	 */
	protected function configureShowField(ShowMapper $showMapper)
	{
		$showMapper
			->add('code')
			->add('name')
			->add('lastname')
			->add('phone')
			->add('job_position')
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
			->add('code')
			->add('name')
			->add('lastname')
			->add('phone')
			->add('job_position', 'inodata_emp_positions_type')
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
			->add('code')
			->add('name')
			->add('lastname')
			->add('phone')
			->add('job_position')
			->add('_action', 'actions', array(
				'actions' => array(
						'view' => array(),
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
			->add('name')
			->add('lastname')
			->add('jobPosition', 'doctrine_orm_choice', array('label' => 'Job Position',
                    'field_options' => array(
                        'required' => false,
                        'choices' => array("Messenger" => "Messenger", "Collector" => "Collector")
                    ),
                    'field_type' => 'choice'
                ));
	}
}