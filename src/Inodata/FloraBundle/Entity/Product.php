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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true, nullable=false)
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
     * @ORM\Column(name="price", type="decimal", nullable=false)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer", nullable=true)
     */
    private $stock;

    /**
     * @var \Customer
     *
     * @ORM\OneToMany(targetEntity="ProductLog", mappedBy="product")
     */
    private $stockStory;

    /**
     * @ORM\ManyToMany(targetEntity="Category", cascade={"persist"})
     * @ORM\JoinTable(name="ino_products_categories",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     * )
     */
    private $categories;
    
    /**
     * @return string
     */
    public function __toString()
    {
    	if(!empty($this->description)){
            if(substr($this->code, 0, 1) == 'X'){
                return $this->description;
            }else{
                return $this->code.' - '.$this->description;
            }
        }else{
            return ' ';
        }
    }

    /**
     * Constructor
     */
    public function __construct()
    {
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
    *@return string
    */
    public function getPriceInLetters(){
        $letters = array('A','B','C','D','E','F','G','H','I','J');
        $numbers = array('1','2','3','4','5','6','7','8','9','0');
        return str_replace($numbers, $letters, $this->price);
    }

    /**
     * Add categories
     *
     * @param \Inodata\FloraBundle\Entity\Category $categories
     * @return Product
     */
    public function addCategory(\Inodata\FloraBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Inodata\FloraBundle\Entity\Category $categories
     */
    public function removeCategory(\Inodata\FloraBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
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
     * Remove categories
     *
     * @param \Inodata\FloraBundle\Entity\Category $categories
     */
    public function removeCategorie(\Inodata\FloraBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Add stockStory
     *
     * @param \Inodata\FloraBundle\Entity\ProductLog $stockStory
     * @return Product
     */
    public function addStockStory(\Inodata\FloraBundle\Entity\ProductLog $stockStory)
    {
        $this->stockStory[] = $stockStory;
    
        return $this;
    }

    /**
     * Remove stockStory
     *
     * @param \Inodata\FloraBundle\Entity\ProductLog $stockStory
     */
    public function removeStockStory(\Inodata\FloraBundle\Entity\ProductLog $stockStory)
    {
        $this->stockStory->removeElement($stockStory);
    }

    /**
     * Get stockStory
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStockStory()
    {
        return $this->stockStory;
    }
}