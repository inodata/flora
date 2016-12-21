<?php

namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Address.
 *
 * @ORM\Table(name="ino_address")
 * @ORM\Entity
 */
class Address
{
    /**
     * @var int
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
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=45, nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var int
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
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=45, nullable=true)
     */
    private $phone;

    /**
     * @var \Employee
     *
     * @ORM\ManyToOne(targetEntity="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="messenger_id", referencedColumnName="id")
     * })
     */
    private $messenger;

    /**
     * @var \Customer
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="addresses", cascade={"persist"})
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @var string
     * @ORM\Column(name="address_type", type="string", columnDefinition="ENUM('Delivery','Fiscal', 'Shipping', 'Billing')")
     */
    private $addressType;

    /**
     *@return string
     */
    //TODO: Hacer una funcion getFormatedAddress() para esta cadena, la funcion toString() se debe mantener simple.
    public function __toString()
    {
        $address = $this->street.' '.$this->noExt;
        if (!empty($this->noInt)) {
            $address .= ' Int. '.$this->noInt;
        }
        $address .= "\n".$this->neighborhood;
        $address .= "\n".$this->city.', '.$this->state.', '.$this->postalCode;

        return $address;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set street.
     *
     * @param string $street
     *
     * @return InoAddress
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street.
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set noInt.
     *
     * @param string $noInt
     *
     * @return InoAddress
     */
    public function setNoInt($noInt)
    {
        $this->noInt = $noInt;

        return $this;
    }

    /**
     * Get noInt.
     *
     * @return string
     */
    public function getNoInt()
    {
        return $this->noInt;
    }

    /**
     * Set noExt.
     *
     * @param string $noExt
     *
     * @return InoAddress
     */
    public function setNoExt($noExt)
    {
        $this->noExt = $noExt;

        return $this;
    }

    /**
     * Get noExt.
     *
     * @return string
     */
    public function getNoExt()
    {
        return $this->noExt;
    }

    /**
     * Set state.
     *
     * @param int $state
     *
     * @return InoAddress
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set city.
     *
     * @param string $city
     *
     * @return InoAddress
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set postalCode.
     *
     * @param int $postalCode
     *
     * @return InoAddress
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode.
     *
     * @return int
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set neighborhood.
     *
     * @param string $neighborhood
     *
     * @return InoAddress
     */
    public function setNeighborhood($neighborhood)
    {
        $this->neighborhood = $neighborhood;

        return $this;
    }

    /**
     * Get neighborhood.
     *
     * @return string
     */
    public function getNeighborhood()
    {
        return $this->neighborhood;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     *
     * @return Address
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set reference.
     *
     * @param string $reference
     *
     * @return Address
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference.
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set messenger.
     *
     * @param \Inodata\FloraBundle\Entity\Employee $messenger
     *
     * @return Address
     */
    public function setMessenger(\Inodata\FloraBundle\Entity\Employee $messenger = null)
    {
        $this->messenger = $messenger;

        return $this;
    }

    /**
     * Get messenger.
     *
     * @return \Inodata\FloraBundle\Entity\Employee
     */
    public function getMessenger()
    {
        return $this->messenger;
    }

    /**
     * Set customer.
     *
     * @param \Inodata\FloraBundle\Entity\Customer $customer
     *
     * @return Address
     */
    public function setCustomer(\Inodata\FloraBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer.
     *
     * @return \Inodata\FloraBundle\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set addressType.
     *
     * @param string $addressType
     *
     * @return Address
     */
    public function setAddressType($addressType)
    {
        $this->addressType = $addressType;

        return $this;
    }

    /**
     * Get addressType.
     *
     * @return string
     */
    public function getAddressType()
    {
        return $this->addressType;
    }
}
