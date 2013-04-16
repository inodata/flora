<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddressType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('street', null, array(
				'label' => 'Calle', )
			)
			->add('noExt', null, array(
				'label' => 'No. Exterior',)
			)
			->add('noInt', null, array(
				'label' => 'No. Interior',)
			)			
			->add('postalCode', null, array(
				'label' => 'Código Postal',)
			)
			->add('neighborhood', null, array(
				'label' => 'Colonia',)
			)
			->add('city', null, array(
				'label' => 'Ciudad',)
			)
			->add('state', 'inodata_mx_states_type', array(
				'label' => 'Estado',)
			)
		;
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