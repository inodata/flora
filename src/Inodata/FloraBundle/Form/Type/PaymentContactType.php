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
				'label' => 'Departamento',)
			)
			->add('employeeNumber', null, array(
				'label' => 'No. Empleado',)
			)
			->add('phone', null, array(
				'label' => 'Teléfono',)
			)
			->add('email')
		;
	}

	public function getName()
	{
		return 'inodata_payment_contact_form';
	}

	public function getDefaultOptions(array $options)
	{
		return array('data_class' => 'Inodata\FloraBundle\Entity\PaymentContact');
	}
}