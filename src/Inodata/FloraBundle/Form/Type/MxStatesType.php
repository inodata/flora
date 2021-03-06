<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MxStatesType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'DF'  => 'Distrito Federal',
                'AGS' => 'Aguascalientes',
                'BCN' => 'Baja California',
                'BCS' => 'Baja California Sur',
                'CAM' => 'Campeche',
                'CHP' => 'Chiapas',
                'CHI' => 'Chihuahua',
                'COA' => 'Coahuila de Zaragoza',
                'COL' => 'Colima',
                'DUR' => 'Durango',
                'GTO' => 'Guanajuato',
                'GRO' => 'Guerrero',
                'HGO' => 'Hidalgo',
                'JAL' => 'Jalisco',
                'MEX' => 'Estado de México',
                'MIC' => 'Michoacán de Ocampo',
                'MOR' => 'Morelos',
                'NAY' => 'Nayarit',
                'NL'  => 'Nuevo León',
                'OAX' => 'Oaxaca',
                'PUE' => 'Puebla',
                'QRO' => 'Querétaro',
                'ROO' => 'Quintana Roo',
                'SLP' => 'San Luis Potosí',
                'SIN' => 'Sinaloa',
                'SON' => 'Sonora',
                'TAB' => 'Tabasco',
                'TAM' => 'Tamaulipas',
                'TLX' => 'Tlaxcala',
                'VER' => 'Veracruz de Ignacio de la Llave',
                'YUC' => 'Yucatán',
                'ZAC' => 'Zacatecas',
            ],
            'preferred_choices' => ['NL'],
        ]);
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'inodata_mx_states_type';
    }
}
