<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'hidden')
            ->add('department', null, [
                'label' => 'label.department', ]
            )
            ->add('employeeNumber', null, [
                'label' => 'label.employee_number', ]
            )
            ->add('phone', null, [
                'label' => 'label.phone', ]
            )
            ->add('extension', null, [
                'label' => 'label.extension', ]
            )
            ->add('email', null, [
                'label' => 'label.email',
                ]
            );
    }

    public function getName()
    {
        return 'inodata_payment_contact_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
                'data_class'         => 'Inodata\FloraBundle\Entity\PaymentContact',
                'translation_domain' => 'InodataFloraBundle',
        ]);
    }
}
