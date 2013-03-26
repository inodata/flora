<?php

namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InoAddress
 *
 * @ORM\Table(name="ino_address")
 * @ORM\Entity
 */
class InoAddress
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
     * @ORM\Column(name="street", type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="no_int", type="string", length=45, nullable=true)
     */
    private $noInt;

    /**
     * @var string
     *
     * @ORM\Column(name="no_ext", type="string", length=45, nullable=true)
     */
    private $noExt;

    /**
     * @var integer
     *
     * @ORM\Column(name="state_id", type="integer", nullable=true)
     */
    private $stateId;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var integer
     *
     * @ORM\Column(name="postal_code", type="integer", nullable=true)
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="neighborhood", type="string", length=255, nullable=true)
     */
    private $neighborhood;



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
     * Set street
     *
     * @param string $street
     * @return InoAddress
     */
    public function setStreet($street)
    {
        $this->street = $street;
    
        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set noInt
     *
     * @param string $noInt
     * @return InoAddress
     */
    public function setNoInt($noInt)
    {
        $this->noInt = $noInt;
    
        return $this;
    }

    /**
     * Get noInt
     *
     * @return string 
     */
    public function getNoInt()
    {
        return $this->noInt;
    }

    /**
     * Set noExt
     *
     * @param string $noExt
     * @return InoAddress
     */
    public function setNoExt($noExt)
    {
        $this->noExt = $noExt;
    
        return $this;
    }

    /**
     * Get noExt
     *
     * @return string 
     */
    public function getNoExt()
    {
        return $this->noExt;
    }

    /**
     * Set stateId
     *
     * @param integer $stateId
     * @return InoAddress
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;
    
        return $this;
    }

    /**
     * Get stateId
     *
     * @return integer 
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return InoAddress
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
     * Set postalCode
     *
     * @param integer $postalCode
     * @return InoAddress
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    
        return $this;
    }

    /**
     * Get postalCode
     *
     * @return integer 
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set neighborhood
     *
     * @param string $neighborhood
     * @return InoAddress
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
}