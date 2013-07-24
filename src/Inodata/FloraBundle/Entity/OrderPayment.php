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
	 * @ORM\Column(name="deposit", type="decimal", nullable=false)
	 */
	private $deposit;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="has_invoice", type="boolean", nullable=true)
	 */
	private $isClosed;
	
	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="delivery_date", type="datetime", nullable=true)
	 */
	private $closeDate;
	
	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="created_at", type="datetime")
	 * @Gedmo\Timestampable(on="create")
	 */
	private $createdAt;

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
     * Set isClosed
     *
     * @param boolean $isClosed
     * @return OrderPayment
     */
    public function setIsClosed($isClosed)
    {
        $this->isClosed = $isClosed;
    
        return $this;
    }

    /**
     * Get isClosed
     *
     * @return boolean 
     */
    public function getIsClosed()
    {
        return $this->isClosed;
    }

    /**
     * Set closeDate
     *
     * @param \DateTime $closeDate
     * @return OrderPayment
     */
    public function setCloseDate($closeDate)
    {
        $this->closeDate = $closeDate;
    
        return $this;
    }

    /**
     * Get closeDate
     *
     * @return \DateTime 
     */
    public function getCloseDate()
    {
        return $this->closeDate;
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