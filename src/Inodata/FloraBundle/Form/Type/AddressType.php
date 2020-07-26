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
            ->add('street', null, ['label' => 'label.street'])
            ->add('noExt', null, ['label' => 'label.exterior'])
            ->add('noInt', null, ['label' => 'label.interior'])
            ->add('reference', null, ['label' => 'label.reference'])
            ->add('neighborhood', 'ajax_autocomplete', ['label' => 'label.neighborhood',
                'attr'                                          => [
                    'placeholder' => 'label.select_neighborhood',
                    'class'       => 'ajax-autocomplete shipping_neighborhood',
                    'entity'      => 'InodataFloraBundle:GuiaRoji',
                    'column'      => 'neighborhood', ],
            ])
            ->add('city', null, ['label' => 'label.city', 'attr' => ['class' => 'shipping_city']])
            ->add(
                'state',
                'inodata_mx_states_type',
                ['label'   => 'label.state',
                    'attr' => ['class'=>'mx_state'],
                ]
            )
            ->add('postalCode', null, ['label' => 'label.postal_code', 'attr' => ['class' => 'shipping_postal_code']])
            ->add('phone', null, ['label' => 'label.delivery_phone']);
    }

    public function getName()
    {
        return 'inodata_address_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'Inodata\FloraBundle\Entity\Address',
            'translation_domain' => 'InodataFloraBundle',
            //TODO: Implementar el tipo de dirección como etiqueta.
            'label' => false,
        ]);
    }
}
