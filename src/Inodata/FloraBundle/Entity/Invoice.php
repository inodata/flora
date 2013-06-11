<?php

namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Invoice
 *
 * @ORM\Table(name="ino_invoice")
 * @ORM\Entity
 */
class Invoice
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
	 * @ORM\Column(name="number", type="string", length=20, nullable=true)
	 */
	private $number;
	
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
	 * @var integer
	 *
	 * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\User")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
	 * })
	 */
	private $creator;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="is_canceled", type="boolean", nullable=false)
	 */
	private $isCanceled;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="comment", type="string", length=255, nullable=true)
	 */
	private $comment;
	
	/**
	 * @var integer
	 *
	 * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\User")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="canceled_by", referencedColumnName="id")
	 * })
	 */
	private $canceledBy;
	
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
	 */
	private $updatedAt;
	
	public function __construct()
	{
		$this->isCanceled = false;
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
     * Set number
     *
     * @param string $number
     * @return Invoice
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set isCanceled
     *
     * @param boolean $isCanceled
     * @return Invoice
     */
    public function setIsCanceled($isCanceled)
    {
        $this->isCanceled = $isCanceled;

        return $this;
    }

    /**
     * Get isCanceled
     *
     * @return boolean 
     */
    public function getIsCanceled()
    {
        return $this->isCanceled;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Invoice
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Invoice
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
     * @return Invoice
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
     * Set order
     *
     * @param \Inodata\FloraBundle\Entity\Order $order
     * @return Invoice
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

    /**
     * Set creator
     *
     * @param \Application\Sonata\UserBundle\Entity\User $creator
     * @return Invoice
     */
    public function setCreator(\Application\Sonata\UserBundle\Entity\User $creator = null)
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
     * Set canceledBy
     *
     * @param \Application\Sonata\UserBundle\Entity\User $canceledBy
     * @return Invoice
     */
    public function setCanceledBy(\Application\Sonata\UserBundle\Entity\User $canceledBy = null)
    {
        $this->canceledBy = $canceledBy;

        return $this;
    }

    /**
     * Get canceledBy
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getCanceledBy()
    {
        return $this->canceledBy;
    }
}
