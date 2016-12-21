<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of AjaxAutcompleteType.
 *
 * @author heri
 */
class AjaxAutocompleteType extends AbstractType
{
    //@Overriden
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['class' => 'ajax-autocomplete'],
        ]);
    }

    //@Overriden
    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'ajax_autocomplete';
    }
}
