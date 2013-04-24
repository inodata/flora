<?php

namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="ino_product")
 * @ORM\Entity
 */
class Product
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
     * @ORM\Column(name="code", type="integer", nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="decimal", nullable=true)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer", nullable=false)
     */
    private $stock;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Order", mappedBy="products")
     */
    private $order;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="product", cascade={"persist"})
     * @ORM\JoinTable(name="ino_product_category",
     *   joinColumns={
     *     @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *   }
     * )
     */
    private $categories;
    
    /**
     * @return string
     */
    public function __toString()
    {
    	if(!$this->id){
    		return "New";
    	}
    	
    	return $this->description.' ('.$this->code.')';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->order = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     * @return Product
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
     * Add category
     *
     * @param \Inodata\FloraBundle\Entity\Category $category
     * @return Product
     */
    public function addCategory(\Inodata\FloraBundle\Entity\Category $category)
    {
        $this->categories[] = $category;
    
        return $this;
    }

    /**
     * Remove category
     *
     * @param \Inodata\FloraBundle\Entity\Category $category
     */
    public function removeCategory(\Inodata\FloraBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add categories
     *
     * @param \Inodata\FloraBundle\Entity\Category $categories
     * @return Product
     */
    public function addCategories(\Inodata\FloraBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;
    
        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Inodata\FloraBundle\Entity\Category $categories
     */
    public function removeCategorie(\Inodata\FloraBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Set code
     *
     * @param integer $code
     * @return Product
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return integer 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add categories
     *
     * @param \Inodata\FloraBundle\Entity\Category $categories
     * @return Product
     */
    public function addCategorie(\Inodata\FloraBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;
    
        return $this;
    }

    /**
     * Add order
     *
     * @param \Inodata\FloraBundle\Entity\Order $order
     * @return Product
     */
    public function addOrder(\Inodata\FloraBundle\Entity\Order $order)
    {
        $this->order[] = $order;
    
        return $this;
    }

    /**
     * Remove order
     *
     * @param \Inodata\FloraBundle\Entity\Order $order
     */
    public function removeOrder(\Inodata\FloraBundle\Entity\Order $order)
    {
        $this->order->removeElement($order);
    }

    /**
     * Get order
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
    *@return string
    */
    public function getPriceInLetters(){
        $letters = array('A','B','C','D','E','F','G','H','I','J');
        $numbers = array('1','2','3','4','5','6','7','8','9','0');
        return str_replace($numbers, $letters, $this->price);
    }
}