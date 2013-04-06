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
	 * @var integer $orderId
	 *
	 * @ORM\Column(name="order_id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	private $orderId;
	
	/**
	 * @var integer $productId
	 *
	 * @ORM\Column(name="product_id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	private $productId;

    /**
     * Set orderId
     *
     * @param integer $orderId
     * @return OrderProduct
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    
        return $this;
    }

    /**
     * Get orderId
     *
     * @return integer 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set productId
     *
     * @param integer $productId
     * @return OrderProduct
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    
        return $this;
    }

    /**
     * Get productId
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->productId;
    }
}