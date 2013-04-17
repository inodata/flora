<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

class DistributionType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('messenger', 'genemu_jqueryselect2_entity', array(
            	'class' => 'Inodata\FloraBundle\Entity\Employee'))
			->add('id')	
		;
	}

	public function getName()
	{
		return 'inodata_distribution_type_form';
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'Inodata\FloraBundle\Entity\Order',
				'translation_domain' => 'InodataFloraBundle'
		));
	}
}