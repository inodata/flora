<?php

namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InoCustomer
 *
 * @ORM\Table(name="ino_customer")
 * @ORM\Entity
 */
class InoCustomer
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
     * @ORM\Column(name="rfc", type="string", length=20, nullable=true)
     */
    private $rfc;

    /**
     * @var string
     *
     * @ORM\Column(name="business_name", type="string", length=255, nullable=true)
     */
    private $businessName;

    /**
     * @var string
     *
     * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
     */
    private $companyName;

    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float", nullable=true)
     */
    private $discount;

    /**
     * @var \InoAddress
     *
     * @ORM\ManyToOne(targetEntity="InoAddress")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fiscal_address_id", referencedColumnName="id")
     * })
     */
    private $fiscalAddress;

    /**
     * @var \InoAddress
     *
     * @ORM\ManyToOne(targetEntity="InoAddress")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="payment_address_id", referencedColumnName="id")
     * })
     */
    private $paymentAddress;



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
     * Set rfc
     *
     * @param string $rfc
     * @return InoCustomer
     */
    public function setRfc($rfc)
    {
        $this->rfc = $rfc;
    
        return $this;
    }

    /**
     * Get rfc
     *
     * @return string 
     */
    public function getRfc()
    {
        return $this->rfc;
    }

    /**
     * Set businessName
     *
     * @param string $businessName
     * @return InoCustomer
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;
    
        return $this;
    }

    /**
     * Get businessName
     *
     * @return string 
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     * @return InoCustomer
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    
        return $this;
    }

    /**
     * Get companyName
     *
     * @return string 
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set discount
     *
     * @param float $discount
     * @return InoCustomer
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    
        return $this;
    }

    /**
     * Get discount
     *
     * @return float 
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set fiscalAddress
     *
     * @param \Inodata\FloraBundle\Entity\InoAddress $fiscalAddress
     * @return InoCustomer
     */
    public function setFiscalAddress(\Inodata\FloraBundle\Entity\InoAddress $fiscalAddress = null)
    {
        $this->fiscalAddress = $fiscalAddress;
    
        return $this;
    }

    /**
     * Get fiscalAddress
     *
     * @return \Inodata\FloraBundle\Entity\InoAddress 
     */
    public function getFiscalAddress()
    {
        return $this->fiscalAddress;
    }

    /**
     * Set paymentAddress
     *
     * @param \Inodata\FloraBundle\Entity\InoAddress $paymentAddress
     * @return InoCustomer
     */
    public function setPaymentAddress(\Inodata\FloraBundle\Entity\InoAddress $paymentAddress = null)
    {
        $this->paymentAddress = $paymentAddress;
    
        return $this;
    }

    /**
     * Get paymentAddress
     *
     * @return \Inodata\FloraBundle\Entity\InoAddress 
     */
    public function getPaymentAddress()
    {
        return $this->paymentAddress;
    }
}