<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of AjaxEntityType.
 *
 * @author heri
 */
class AjaxEntityType extends AbstractType
{
    //@Overriden
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['class' => 'ajax-entity'],
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
