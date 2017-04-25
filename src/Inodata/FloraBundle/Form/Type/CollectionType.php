<?php

namespace Inodata\FloraBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'ajax_entity', [
                'required'      => false,
                'class'         => 'InodataFloraBundle:Order',
                'empty_value'   => '',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.status = \'delivered\'')
                        ->andWhere('u.collector IS NULL');
                },
                'attr'          => [
                    'class'       => 'inodata_id_list span',
                    'entity'      => 'InodataFloraBundle:Order', 'columns' => 'id',
                    'placeholder' => 'Selecciona una órden',
                    'allowClear'  => 'true',
                ],
            ]);
    }

    public function getName()
    {
        return 'inodata_collection_type_form';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'Inodata\FloraBundle\Entity\Order',
            'translation_domain' => 'InodataFloraBundle',
        ]);
    }
}
