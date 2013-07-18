<?php

namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GuiaRoji
 *
 * @ORM\Table(name="ino_guia_roji")
 * @ORM\Entity
 */
class GuiaRoji
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="neighborhood", type="string", length=255, nullable=true)
	 */
	private $neighborhood;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="city", type="string", length=255, nullable=true)
	 */
	private $city;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="postal_code", type="integer", nullable=true)
	 */
	private $postal_code;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="map", type="string", length=32, nullable=true)
	 */
	private $map;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="coordinate", type="string", length=32, nullable=true)
	 */
	private $coordinate;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set neighborhood
     *
     * @param string $neighborhood
     * @return GuiaRoji
     */
    public function setNeighborhood($neighborhood)
    {
        $this->neighborhood = $neighborhood;
    
        return $this;
    }

    /**
     * Get neighborhood
     *
     * @return string 
     */
    public function getNeighborhood()
    {
        return $this->neighborhood;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return GuiaRoji
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set postal_code
     *
     * @param integer $postalCode
     * @return GuiaRoji
     */
    public function setPostalCode($postalCode)
    {
        $this->postal_code = $postalCode;
    
        return $this;
    }

    /**
     * Get postal_code
     *
     * @return integer 
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    /**
     * Set map
     *
     * @param string $map
     * @return GuiaRoji
     */
    public function setMap($map)
    {
        $this->map = $map;
    
        return $this;
    }

    /**
     * Get map
     *
     * @return string 
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Set coordinate
     *
     * @param string $coordinate
     * @return GuiaRoji
     */
    public function setCoordinate($coordinate)
    {
        $this->coordinate = $coordinate;
    
        return $this;
    }

    /**
     * Get coordinate
     *
     * @return string 
     */
    public function getCoordinate()
    {
        return $this->coordinate;
    }
}