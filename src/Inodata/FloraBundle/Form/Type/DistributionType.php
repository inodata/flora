<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class DistributionType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('messenger', 'genemu_jqueryselect2_entity', array(
				'required' => false,
				'empty_value' => '',	
            	'class' => 'Inodata\FloraBundle\Entity\Employee',
				'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('u')
								  ->where('u.jobPosition = \'Messenger\'');
				},
				'attr' => array(
					'class' => 'inodata_messenger_list span5',
					'placeholder' => 'Selecciona un repartidor',
					'enabled' => 'enabled'
				)
			))
			->add('id', 'genemu_jqueryselect2_entity', array(
				'required' => false,
				'empty_value' => '',
				'class' => 'Inodata\FloraBundle\Entity\Order',
				'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('u')
								  ->where('u.status = \'open\'')
								  ->andWhere('u.messenger IS NULL');
					}
					,
				'attr' => array(
					'class' => 'inodata_id_list span5',
					'placeholder' => 'selecciona una orden'
				)
			))	
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