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
			->add('department')
			->add('employeeNumber')
			->add('phone')
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