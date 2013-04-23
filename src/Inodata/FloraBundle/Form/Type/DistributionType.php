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
            	'class' => 'Inodata\FloraBundle\Entity\Employee',
				'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('u')
								  ->where('u.jobPosition = \'Messenger\'');
					}
					,
				'attr' => array(
					'class' => 'inodata_messenger_list span5 select2-offscreen',
				)
			))
			->add('id', 'genemu_jqueryselect2_entity', array(
				'required' => false,
				'class' => 'Inodata\FloraBundle\Entity\Order',
				'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('u')
								  ->where('u.status = \'open\'')
								  ->andWhere('u.messenger IS NULL');
					}
					,
				'attr' => array(
					'class' => 'select2-container inodata_id_list span5',
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