<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddressType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('street')
			->add('noExt')
			->add('noInt')			
			->add('postalCode')
			->add('neighborhood')
			->add('city')
			->add('state', 'inodata_mx_states_type');
	}

	public function getName()
	{
		return 'inodata_address_form';
	}

	public function getDefaultOptions(array $options)
	{
		return array('data_class' => 'Inodata\FloraBundle\Entity\Address');
	}
}