<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of AjaxEntityType.
 *
 * @author heri
 */
class AjaxEntityType extends AbstractType
{
    //@Overriden
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'class' => 'ajax-entity',
                'query' => null,
            ],
        ]);
    }

    //@Overriden
    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'ajax_entity';
    }
}
