<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PaymentContactType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', 'hidden')
			->add('department', null, array(
				'label' => 'label.department',)
			)
			->add('employeeNumber', null, array(
				'label' => 'label.employee_number',)
			)
			->add('phone', null, array(
				'label' => 'label.phone',)
			)
			->add('email', null, array(
				'label' => 'label.email',
				)
			)
		;
	}

	public function getName()
	{
		return 'inodata_payment_contact_form';
	}

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Inodata\FloraBundle\Entity\PaymentContact',
			'translation_domain' => 'InodataFloraBundle'
		);
	}
}