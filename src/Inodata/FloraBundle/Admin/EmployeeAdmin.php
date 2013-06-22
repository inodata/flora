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
			->add('code', null, array('label'=>'label.code'))
			->add('name', null, array('label'=>'label.name'))
			->add('lastname', null, array('label'=>'label.lastname'))
			->add('phone', null, array('label'=>'label.phone'))
			->add('job_position', null, array('label'=>'label.job_position'))
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
			->add('code', null, array('label'=>'label.code'))
			->add('name', null, array('label'=>'label.name'))
			->add('lastname', null, array('label'=>'label.lastname'))
			->add('phone', null, array('label'=>'label.phone'))
			->add('job_position', 'inodata_emp_positions_type', array('label'=>'label.job_position'))
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
			->add('code', null, array('label'=>'label.code'))
			->add('name', null, array('label'=>'label.name'))
			->add('lastname', null, array('label'=>'label.lastname'))
			->add('phone', null, array('label'=>'label.phone'))
			->add('job_position', 'inodata_emp_positions_type', array('label'=>'label.job_position'))
			->add('_action', 'actions', array(
				'label' => 'label.actions',
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
			->add('name', null, array('label'=>'label.name'))
			->add('lastname', null, array('label'=>'label.lastname'))
			->add('jobPosition', 'doctrine_orm_choice', array('label' => 'label.job_position',
                    'field_options' => array(
                        'required' => false,
                        'choices' => array("Messenger" => "Messenger", "Collector" => "Collector")
                    ),
                    'field_type' => 'choice'
                ));
	}
}