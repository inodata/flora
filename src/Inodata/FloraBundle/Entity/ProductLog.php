<?php

namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ProductLog
 *
 * @ORM\Table(name="ino_product_log")
 * @ORM\Entity
 */
class ProductLog
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
     * @var integer
     *
     * @ORM\Column(name="last_stock", type="integer", nullable=false)
     */
    private $lastStock;

    /**
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer", nullable=false)
     */
    private $stock;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var \Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="stockStory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $product;

    /**
     * @var Application\Sonata\UserBundle\Entity\User $creator
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;


    
    /**
     * @return string
     */
    public function __toString()
    {
    	if(!$this->id){
    		return "New";
    	}
    	
    	return ' '.$this->product;
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
     * Set lastStock
     *
     * @param integer $lastStock
     * @return ProductLog
     */
    public function setLastStock($lastStock)
    {
        $this->lastStock = $lastStock;
    
        return $this;
    }

    /**
     * Get lastStock
     *
     * @return integer 
     */
    public function getLastStock()
    {
        return $this->lastStock;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     * @return ProductLog
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    
        return $this;
    }

    /**
     * Get stock
     *
     * @return integer 
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return ProductLog
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
     * @return ProductLog
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
     * Set product
     *
     * @param \Inodata\FloraBundle\Entity\Product $product
     * @return ProductLog
     */
    public function setProduct(\Inodata\FloraBundle\Entity\Product $product = null)
    {
        $this->product = $product;
    
        return $this;
    }

    /**
     * Get product
     *
     * @return \Inodata\FloraBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Sonata\UserBundle\Entity\User $createdBy
     * @return ProductLog
     */
    public function setCreatedBy(\Application\Sonata\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;
    
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}