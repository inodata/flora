<?php
namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OderProduct
 *
 * @ORM\Table(name="ino_order_payment")
 * @ORM\Entity
 */

class OrderPayment {
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @var \Order
	 *
	 * @ORM\ManyToOne(targetEntity="Order")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="order_id", referencedColumnName="id")
	 * })
	 */
	private $order;
	
	/**
	 * @var float
	 *
	 * @ORM\Column(name="deposit", type="float", nullable=false)
	 */
	private $deposit;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="is_paid", type="boolean", nullable=true)
	 */
	private $isPaid;
	
	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="paid_date", type="datetime", nullable=true)
	 */
	private $paidDate;
	
	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="created_at", type="datetime")
	 * @Gedmo\Timestampable(on="create")
	 */
	private $createdAt;
	
	public function __construct()
	{
		$this->isPaid = false;
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
     * Set deposit
     *
     * @param float $deposit
     * @return OrderPayment
     */
    public function setDeposit($deposit)
    {
        $this->deposit = $deposit;
    
        return $this;
    }

    /**
     * Get deposit
     *
     * @return float 
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * Set isPaid
     *
     * @param boolean $isPaid
     * @return OrderPayment
     */
    public function setIsPaid($isPaid)
    {
        $this->isPaid = $isPaid;
    
        return $this;
    }

    /**
     * Get isPaid
     *
     * @return boolean 
     */
    public function getIsPaid()
    {
        return $this->isPaid;
    }

    /**
     * Set paidDate
     *
     * @param \DateTime $paidDate
     * @return OrderPayment
     */
    public function setPaidDate($paidDate)
    {
        $this->paidDate = $paidDate;
    
        return $this;
    }

    /**
     * Get paidDate
     *
     * @return \DateTime 
     */
    public function getPaidDate()
    {
        return $this->paidDate;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return OrderPayment
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
     * Set order
     *
     * @param \Inodata\FloraBundle\Entity\Order $order
     * @return OrderPayment
     */
    public function setOrder(\Inodata\FloraBundle\Entity\Order $order = null)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return \Inodata\FloraBundle\Entity\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }
}