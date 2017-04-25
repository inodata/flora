<?php

namespace Inodata\FloraBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DistributionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //TODO: Probar con sonata_type_model, al hacer el request de ajax no respeta el query builder
        $builder
            ->add('id', 'ajax_entity', [
                'required'      => false,
                'class'         => 'InodataFloraBundle:Order',
                'empty_value'   => '',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.status = \'open\'')
                        ->andWhere('u.messenger IS NULL');
                },
                'attr'          => [
                    'class'       => 'inodata_id_list span5',
                    'entity'      => 'InodataFloraBundle:Order', 'columns' => 'id',
                    'placeholder' => 'Selecciona una órden',
                    'allowClear'  => 'true',
                ],
            ])
            ->add('messenger', 'genemu_jqueryselect2_entity', [
                'required'      => false,
                'empty_value'   => '',
                'class'         => 'Inodata\FloraBundle\Entity\Employee',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.jobPosition = \'Messenger\'');
                },
                'attr'          => [
                    'class'       => 'inodata_messenger_list span5',
                    'placeholder' => 'Selecciona un repartidor',
                    'enabled'     => 'enabled',
                ],
            ]);
    }

    public function getName()
    {
        return 'inodata_distribution_type_form';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'Inodata\FloraBundle\Entity\Order',
            'translation_domain' => 'InodataFloraBundle',
        ]);
    }
}
