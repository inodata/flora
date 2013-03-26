<?php

namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InoOrder
 *
 * @ORM\Table(name="ino_order")
 * @ORM\Entity
 */
class InoOrder
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
     * @var \DateTime
     *
     * @ORM\Column(name="delivery_date", type="datetime", nullable=true)
     */
    private $deliveryDate;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_number", type="string", length=15, nullable=true)
     */
    private $invoiceNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_external", type="boolean", nullable=true)
     */
    private $isExternal;

    /**
     * @var float
     *
     * @ORM\Column(name="shipping", type="float", nullable=true)
     */
    private $shipping;

    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float", nullable=true)
     */
    private $discount;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * })
     */
    private $creator;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="from", type="string", length=255, nullable=true)
     */
    private $from;

    /**
     * @var string
     *
     * @ORM\Column(name="to", type="string", length=255, nullable=true)
     */
    private $to;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="InoProduct", inversedBy="order")
     * @ORM\JoinTable(name="ino_order_product",
     *   joinColumns={
     *     @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     *   }
     * )
     */
    private $products;

    /**
     * @var \InoAddress
     *
     * @ORM\ManyToOne(targetEntity="InoAddress")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="shipping_address_id", referencedColumnName="id")
     * })
     */
    private $shippingAddress;

    /**
     * @var \InoCustomer
     *
     * @ORM\ManyToOne(targetEntity="InoCustomer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * })
     */
    private $customer;

    /**
     * @var \InoEmployee
     *
     * @ORM\ManyToOne(targetEntity="InoEmployee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="messenger_id", referencedColumnName="id")
     * })
     */
    private $messenger;

    /**
     * @var \InoEmployee
     *
     * @ORM\ManyToOne(targetEntity="InoEmployee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="collector_id", referencedColumnName="id")
     * })
     */
    private $collector;

    /**
     * @var \InoPaymentContact
     *
     * @ORM\ManyToOne(targetEntity="InoPaymentContact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="payment_contact_id", referencedColumnName="id")
     * })
     */
    private $paymentContact;

    /**
    * @var string
    * @ORM\Column(type="string", columnDefinition="ENUM('open', 'intransit', 'delivered','partiallypayment','closed')") 
    */
    private $status;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }
    

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
     * Set deliveryDate
     *
     * @param \DateTime $deliveryDate
     * @return InoOrder
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
    
        return $this;
    }

    /**
     * Get deliveryDate
     *
     * @return \DateTime 
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * Set invoiceNumber
     *
     * @param string $invoiceNumber
     * @return InoOrder
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;
    
        return $this;
    }

    /**
     * Get invoiceNumber
     *
     * @return string 
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * Set isExternal
     *
     * @param boolean $isExternal
     * @return InoOrder
     */
    public function setIsExternal($isExternal)
    {
        $this->isExternal = $isExternal;
    
        return $this;
    }

    /**
     * Get isExternal
     *
     * @return boolean 
     */
    public function getIsExternal()
    {
        return $this->isExternal;
    }

    /**
     * Set shipping
     *
     * @param float $shipping
     * @return InoOrder
     */
    public function setShipping($shipping)
    {
        $this->shipping = $shipping;
    
        return $this;
    }

    /**
     * Get shipping
     *
     * @return float 
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * Set discount
     *
     * @param float $discount
     * @return InoOrder
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
     * Set creator
     *
     * @param \Application\Sonata\UserBundle\Entity\User $creator
     * @return InoOrder
     */
    public function setCreator(\Application\Sonata\UserBundle\Entity\User $creator )
    {
        $this->creator = $creator;
    
        return $this;
    }

    /**
     * Get creator
     *
     * @return \Application\Sonata\UserBundle\Entity\User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return InoOrder
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return InoOrder
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set from
     *
     * @param string $from
     * @return InoOrder
     */
    public function setFrom($from)
    {
        $this->from = $from;
    
        return $this;
    }

    /**
     * Get from
     *
     * @return string 
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param string $to
     * @return InoOrder
     */
    public function setTo($to)
    {
        $this->to = $to;
    
        return $this;
    }

    /**
     * Get to
     *
     * @return string 
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return InoOrder
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Add product
     *
     * @param \Inodata\FloraBundle\Entity\InoProduct $product
     * @return InoOrder
     */
    public function addProduct(\Inodata\FloraBundle\Entity\InoProduct $product)
    {
        $this->product[] = $product;
    
        return $this;
    }

    /**
     * Remove Product
     *
     * @param \Inodata\FloraBundle\Entity\InoProduct $product
     */
    public function removeProduct(\Inodata\FloraBundle\Entity\InoProduct $product)
    {
        $this->product->removeElement($product);
    }

    /**
     * Get product
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set shippingAddress
     *
     * @param \Inodata\FloraBundle\Entity\InoAddress $shippingAddress
     * @return InoOrder
     */
    public function setShippingAddress(\Inodata\FloraBundle\Entity\InoAddress $shippingAddress = null)
    {
        $this->shippingAddress = $shippingAddress;
    
        return $this;
    }

    /**
     * Get shippingAddress
     *
     * @return \Inodata\FloraBundle\Entity\InoAddress 
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Set customer
     *
     * @param \Inodata\FloraBundle\Entity\InoCustomer $customer
     * @return InoOrder
     */
    public function setCustomer(\Inodata\FloraBundle\Entity\InoCustomer $customer = null)
    {
        $this->customer = $customer;
    
        return $this;
    }

    /**
     * Get customer
     *
     * @return \Inodata\FloraBundle\Entity\InoCustomer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set messenger
     *
     * @param \Inodata\FloraBundle\Entity\InoEmployee $messenger
     * @return InoOrder
     */
    public function setMessenger(\Inodata\FloraBundle\Entity\InoEmployee $messenger = null)
    {
        $this->messenger = $messenger;
    
        return $this;
    }

    /**
     * Get messenger
     *
     * @return \Inodata\FloraBundle\Entity\InoEmployee 
     */
    public function getMessenger()
    {
        return $this->messenger;
    }

    /**
     * Set collector
     *
     * @param \Inodata\FloraBundle\Entity\InoEmployee $collector
     * @return InoOrder
     */
    public function setCollector(\Inodata\FloraBundle\Entity\InoEmployee $collector = null)
    {
        $this->collector = $collector;
    
        return $this;
    }

    /**
     * Get collector
     *
     * @return \Inodata\FloraBundle\Entity\InoEmployee 
     */
    public function getCollector()
    {
        return $this->collector;
    }

    /**
     * Set paymentContact
     *
     * @param \Inodata\FloraBundle\Entity\InoPaymentContact $paymentContact
     * @return InoOrder
     */
    public function setPaymentContact(\Inodata\FloraBundle\Entity\InoPaymentContact $paymentContact = null)
    {
        $this->paymentContact = $paymentContact;
    
        return $this;
    }

    /**
     * Get paymentContact
     *
     * @return \Inodata\FloraBundle\Entity\InoPaymentContact 
     */
    public function getPaymentContact()
    {
        return $this->paymentContact;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return InoOrder
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }
}