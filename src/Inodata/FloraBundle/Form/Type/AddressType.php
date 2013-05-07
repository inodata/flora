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
				'label' => 'label.street', )
			)
			->add('noExt', null, array(
				'label' => 'label.exterior',)
			)
			->add('noInt', null, array(
				'label' => 'label.interior',)
			)
			->add('reference', null, array('label' => 'label.reference'))
			->add('postalCode', null, array(
				'label' => 'label.postal_code',)
			)
			->add('neighborhood', null, array(
				'label' => 'label.neighborhood',)
			)
			->add('city', null, array(
				'label' => 'label.city',)
			)
			->add('state', 'inodata_mx_states_type', array(
				'label' => 'label.state',
				'attr' => array('class'=>'mx_state'))
			)
			->add('phone', null, array(
				'label' => 'label.delivery_phone',)
			)
		;
	}

	public function getName()
	{
		return 'inodata_address_form';
	}

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Inodata\FloraBundle\Entity\Address',
			'translation_domain' => 'InodataFloraBundle'
		);
	}
}