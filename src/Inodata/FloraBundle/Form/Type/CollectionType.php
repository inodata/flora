<?php

namespace Inodata\FloraBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'genemu_jqueryselect2_entity', [
                'required'      => false,
                'empty_value'   => '',
                'class'         => 'Inodata\FloraBundle\Entity\Order',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                                  ->where('u.status = \'delivered\'')
                                  ->andWhere('u.collector IS NULL');
                },
                'attr' => [
                    'class'       => 'inodata_id_list span5',
                    'placeholder' => 'selecciona una orden',
                ],
            ]);
    }

    public function getName()
    {
        return 'inodata_collection_type_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
                'data_class'         => 'Inodata\FloraBundle\Entity\Order',
                'translation_domain' => 'InodataFloraBundle',
        ]);
    }
}
