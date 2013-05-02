<?php

namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations
//use Doctrine\ORM\EntityRepository;

/**
 * Order
 * @Gedmo\Loggable
 * @ORM\Table(name="ino_order")
 * @ORM\Entity
 */
class Order
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
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @Gedmo\Versioned
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="from_person", type="string", length=255, nullable=true)
     */
    private $from;

    /**
     * @var string
     *
     * @ORM\Column(name="to_person", type="string", length=255, nullable=true)
     */
    private $to;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var \Address
     *
     * @ORM\ManyToOne(targetEntity="Address", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="shipping_address_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $shippingAddress;

    /**
     * @var \Customer
     *
     * @ORM\ManyToOne(targetEntity="Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * })
     */
    private $customer;

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
     * @var \Employee
     *
     * @ORM\ManyToOne(targetEntity="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="collector_id", referencedColumnName="id")
     * })
     */
    private $collector;

    /**
     * @var \PaymentContact
     *
     * @ORM\ManyToOne(targetEntity="PaymentContact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="payment_contact_id", referencedColumnName="id")
     * })
     */
    private $paymentContact;

    /**
    * @var string
    * @ORM\Column(type="string", columnDefinition="ENUM('open','intransit','delivered','partiallypayment','closed')") 
    */
    private $status;
    
    /**
     * @var string
     *
     * @ORM\Column(name="payment_condition", type="string", length=255, nullable=true)
     */
    private $paymentCondition;

    /**
     * @var string
     *
     * @ORM\Column(name="order_notes", type="string", length=255, nullable=true)
     */
    private $order_notes;
    
    /**
     * @var string
     *
     * @ORM\Column(name="invoice_comment", type="string", length=255, nullable=true)
     */
    private $invoiceComment;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="has_invoice", type="boolean", nullable=true)
     */
    private $hasInvoice;


    /**
     * Constructor
     */
    public function __construct()
    {
        
    	$this->hasInvoice = false;
        $this->deliveryDate = new \DateTime("NOW");
    }
    
    /**
    *@return string
    */
    public function __toString()
    {
        $orderId = $this->id;
        if(empty($this->id))
        {
            $orderId = "";
        }
        
        $product = $this->getFirstProduct();
        
        if( $product != "")
        {
        	return ''.$orderId.' '.$product;
        }
        
        return ''.$orderId;
    }
    
    public function getFirstProduct(){
    	global $kernel;
    	
    	if ('AppCache' == get_class($kernel)) {
    		$kernel = $kernel->getKernel();
    	}
    	
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('InodataFloraBundle:OrderProduct');
        $orderProduct = $repository->findOneByOrder($this->getId());
        
        if( $orderProduct ){
        	return $orderProduct->getProduct();
        }
        
        return "";
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
     * Function only for resolve a problem with edit
     *
     * @return Order
     */
    public function setId()
    {
    	return $this;
    }

    /**
     * Set deliveryDate
     *
     * @param \DateTime $deliveryDate
     * @return Order
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
     * @return Order
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
     * @return Order
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
     * @return Order
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
     * @return Order
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
     * @return Order
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
     * @return Order
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
     * @return Order
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
     * @return Order
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
     * @return Order
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
     * @return Order
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
     * Set shippingAddress
     *
     * @param \Inodata\FloraBundle\Entity\Address $shippingAddress
     * @return Order
     */
    public function setShippingAddress(\Inodata\FloraBundle\Entity\Address $shippingAddress = null)
    {
        $this->shippingAddress = $shippingAddress;
    
        return $this;
    }

    /**
     * Get shippingAddress
     *
     * @return \Inodata\FloraBundle\Entity\Address 
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Set customer
     *
     * @param \Inodata\FloraBundle\Entity\Customer $customer
     * @return Order
     */
    public function setCustomer(\Inodata\FloraBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;
    
        return $this;
    }

    /**
     * Get customer
     *
     * @return \Inodata\FloraBundle\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set messenger
     *
     * @param \Inodata\FloraBundle\Entity\Employee $messenger
     * @return Order
     */
    public function setMessenger(\Inodata\FloraBundle\Entity\Employee $messenger = null)
    {
        $this->messenger = $messenger;
    
        return $this;
    }

    /**
     * Get messenger
     *
     * @return \Inodata\FloraBundle\Entity\Employee 
     */
    public function getMessenger()
    {
        return $this->messenger;
    }

    /**
     * Set collector
     *
     * @param \Inodata\FloraBundle\Entity\Employee $collector
     * @return Order
     */
    public function setCollector(\Inodata\FloraBundle\Entity\Employee $collector = null)
    {
        $this->collector = $collector;
    
        return $this;
    }

    /**
     * Get collector
     *
     * @return \Inodata\FloraBundle\Entity\Employee 
     */
    public function getCollector()
    {
        return $this->collector;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Order
     */
    public function setStatus($status = null)
    {
    	if (empty($status)){
    		$status = 'open';
    	}
    	
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

    /**
     * Set paymentContact
     *
     * @param \Inodata\FloraBundle\Entity\PaymentContact $paymentContact
     * @return Order
     */
    public function setPaymentContact(\Inodata\FloraBundle\Entity\PaymentContact $paymentContact = null)
    {
        $this->paymentContact = $paymentContact;
    
        return $this;
    }

    /**
     * Get paymentContact
     *
     * @return \Inodata\FloraBundle\Entity\PaymentContact 
     */
    public function getPaymentContact()
    {
        return $this->paymentContact;
    }

    /**
     * Set paymentCondition
     *
     * @param string $paymentCondition
     * @return Order
     */
    public function setPaymentCondition($paymentCondition)
    {
        $this->paymentCondition = $paymentCondition;
    
        return $this;
    }

    /**
     * Get paymentCondition
     *
     * @return string 
     */
    public function getPaymentCondition()
    {
    	if (!$this->paymentCondition && $this->getId()){
    		return $this->customer->getPaymentCondition();
    	}
    	
        return $this->paymentCondition;
    }

    /**
     * Set invoiceComment
     *
     * @param string $invoiceComment
     * @return Order
     */
    public function setInvoiceComment($invoiceComment)
    {
        $this->invoiceComment = $invoiceComment;
    
        return $this;
    }

    /**
     * Get invoiceComment
     *
     * @return string 
     */
    public function getInvoiceComment()
    {
        return $this->invoiceComment;
    }

    /**
     * Set order_notes
     *
     * @param string $orderNotes
     * @return Order
     */
    public function setOrderNotes($orderNotes)
    {
        $this->order_notes = $orderNotes;
    
        return $this;
    }

    /**
     * Get order_notes
     *
     * @return string 
     */
    public function getOrderNotes()
    {
        return $this->order_notes;
    }

    /**
     * Set hasInvoice
     *
     * @param boolean $hasInvoice
     * @return Order
     */
    public function setHasInvoice($hasInvoice)
    {
        $this->hasInvoice = $hasInvoice;
    
        return $this;
    }

    /**
     * Get hasInvoice
     *
     * @return boolean 
     */
    public function getHasInvoice()
    {
        return $this->hasInvoice;
    }
}
