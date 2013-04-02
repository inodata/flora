<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('member', 'genemu_jqueryautocompleter_entity', array(
            'class' => 'Genemu\Bundle\EntityBundle\Entity\Member',
        );
	}

	public function getParent()
	{
		return 'text';
	}

	public function getName()
	{
		return 'inodata_customer_type';
	}
}