<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmpPositionsType extends AbstractType
{
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'choices' => array(
				"Messenger" => "Messenger",
				"Collector" => "Collector",
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
		return 'inodata_emp_positions_type';
	}
}