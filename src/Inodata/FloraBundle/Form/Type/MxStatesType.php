<?php

namespace Inodata\FloraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MxStatesType extends AbstractType
{
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'choices' => array(
				"MX-DIF" => "Distrito Federal",
				"MX-AGS" =>	"Aguascalientes",
				"MX-BCN" =>	"Baja California",
				"MX-BCS" =>	"Baja California Sur",
				"MX-CAM" =>	"Campeche",
				"MX-CHP" =>	"Chiapas",
				"MX-CHI" =>	"Chihuahua",
				"MX-COA" =>	"Coahuila de Zaragoza",
				"MX-COL" =>	"Colima",
				"MX-DUR" =>	"Durango",
				"MX-GTO" =>	"Guanajuato",
				"MX-GRO" =>	"Guerrero",
				"MX-HGO" =>	"Hidalgo",
				"MX-JAL" =>	"Jalisco",
				"MX-MEX" =>	"Estado de México",
				"MX-MIC" =>	"Michoacán de Ocampo",
				"MX-MOR" =>	"Morelos",
				"MX-NAY" =>	"Nayarit",
				"MX-NLE" =>	"Nuevo León",
				"MX-OAX" =>	"Oaxaca",
				"MX-PUE" =>	"Puebla",
				"MX-QRO" =>	"Querétaro",
				"MX-ROO" =>	"Quintana Roo",
				"MX-SLP" =>	"San Luis Potosí",
				"MX-SIN" =>	"Sinaloa",
				"MX-SON" =>	"Sonora",
				"MX-TAB" =>	"Tabasco",
				"MX-TAM" =>	"Tamaulipas",
				"MX-TLX" =>	"Tlaxcala",
				"MX-VER" =>	"Veracruz de Ignacio de la Llave",
				"MX-YUC" =>	"Yucatán",
				"MX-ZAC" =>	"Zacatecas"
			)
		));
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