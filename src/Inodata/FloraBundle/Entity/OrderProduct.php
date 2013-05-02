<?php

namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OderProduct
 *
 * @ORM\Table(name="ino_order_product")
 * @ORM\Entity
 */
class OrderProduct
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
	 * @var \Order
	 *
	 * @ORM\ManyToOne(targetEntity="Order")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="order_id", referencedColumnName="id")
	 * })
	 */
	private $order;
	
	/**
	 * @var \Product
	 *
	 * @ORM\ManyToOne(targetEntity="Product")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
	 * })
	 */
	private $product;
	
	/**
	 *@var integer
	 *@ORM\Column(name="quantity", type="integer", nullable=false)
	 */
	private $quantity;
	
	/**
     * @var float
     *
     * @ORM\Column(name="product_price", type="decimal", nullable=false)
     */
    private $productPrice;

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
     * Set quantity
     *
     * @param integer $quantity
     * @return OrderProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    
        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set order
     *
     * @param \Inodata\FloraBundle\Entity\Order $order
     * @return OrderProduct
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
     * Set product
     *
     * @param \Inodata\FloraBundle\Entity\Product $product
     * @return OrderProduct
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
     * Set productPrice
     *
     * @param float $productPrice
     * @return OrderProduct
     */
    public function setProductPrice($productPrice)
    {
        $this->productPrice = $productPrice;
    
        return $this;
    }

    /**
     * Get productPrice
     *
     * @return float 
     */
    public function getProductPrice()
    {
        return $this->productPrice;
    }
}