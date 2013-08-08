<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressTypeType extends AbstractType
{
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'choices' => array(
				"Delivery" => "Delivery",
				"Fiscal" => "Fiscal",
				"Shipping" => "Shipping",
				"Billing" => "Billing",
			),
			'translation_domain' => 'InodataFloraBundle'
		));
	}

	public function getParent()
	{
		return 'choice';
	}

	public function getName()
	{
		return 'inodata_address_type_type';
	}
}