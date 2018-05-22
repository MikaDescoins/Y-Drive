<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @MongoDB\Document
 */
class Product
{
    /**
     * @MongoDB\Id
     */
    protected $id;


    /**
     * @MongoDB\Field(type="integer")
     */
    protected $product_id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $product_name;

    /**
     * @MongoDB\Field(type="float")
     */
    protected $price;

    /**
     * @MongoDB\Field(type="integer")
     */
    protected $stock;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $description;

    

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set productId
     *
     * @param integer $productId
     * @return self
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;
        return $this;
    }

    /**
     * Get productId
     *
     * @return integer $productId
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set productName
     *
     * @param string $productName
     * @return self
     */
    public function setProductName($productName)
    {
        $this->product_name = $productName;
        return $this;
    }

    /**
     * Get productName
     *
     * @return string $productName
     */
    public function getProductName()
    {
        return $this->product_name;
    }

    /**
     * Set retailPrice
     *
     * @param float $retailPrice
     * @return self
     */
    public function setRetailPrice($retailPrice)
    {
        $this->retail_price = $retailPrice;
        return $this;
    }

    /**
     * Get retailPrice
     *
     * @return float $retailPrice
     */
    public function getRetailPrice()
    {
        return $this->retail_price;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     * @return self
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
        return $this;
    }

    /**
     * Get stock
     *
     * @return integer $stock
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return float $price
     */
    public function getPrice()
    {
        return $this->price;
    }





}
