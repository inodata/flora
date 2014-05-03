<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('street', null, array('label' => 'label.street'))
			->add('noExt', null, array('label' => 'label.exterior'))
			->add('noInt', null, array('label' => 'label.interior'))
			->add('reference', null, array('label' => 'label.reference'))
			->add('neighborhood', 'ajax_autocomplete', array('label' => 'label.neighborhood',
                    'attr' => array(
                        'placeholder' => "label.select_neighborhood",
                        "class" => 'ajax-autocomplete shipping_neighborhood', 
                        'entity' => "InodataFloraBundle:GuiaRoji",
                        "column" => "neighborhood")
                ))
			->add('city', null, array('label' => 'label.city','attr' => array("class" => 'shipping_city')))
			->add('state', 'inodata_mx_states_type',
				array('label' => 'label.state',
					'attr' => array('class'=>'mx_state')
				)
			)
            ->add('postalCode', null, array('label' => 'label.postal_code', 'attr' => array('class' => 'shipping_postal_code')))
			->add('phone', null, array('label' => 'label.delivery_phone'))
		;
	}

	public function getName()
	{
		return 'inodata_address_form';
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Inodata\FloraBundle\Entity\Address',
			'translation_domain' => 'InodataFloraBundle',
			//TODO: Implementar el tipo de dirección como etiqueta.
			'label' => false
		));
	}
}