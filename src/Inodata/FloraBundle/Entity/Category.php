<?php

namespace Inodata\FloraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="ino_category")
 * @ORM\Entity
 */
class Category
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
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="category")
     */
    private $product;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->product = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
    *@return string
    */
    public function __toString()
    {
        $description = $this->description;
        if(empty($this->description))
        {
            $description = "";
        }
        return $description;
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
     * @return Category
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
     * Add product
     *
     * @param \Inodata\FloraBundle\Entity\Product $product
     * @return Category
     */
    public function addProduct(\Inodata\FloraBundle\Entity\Product $product)
    {
        $this->product[] = $product;
    
        return $this;
    }

    /**
     * Remove product
     *
     * @param \Inodata\FloraBundle\Entity\Product $product
     */
    public function removeProduct(\Inodata\FloraBundle\Entity\Product $product)
    {
        $this->product->removeElement($product);
    }

    /**
     * Get product
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProduct()
    {
        return $this->product;
    }
}