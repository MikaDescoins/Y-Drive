<?php


namespace AppBundle\Document;


use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Order
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /** @MongoDB\ReferenceOne(targetDocument="User") */

    protected $user;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Product")
     */
    protected $products = array();

    /**
     * @MongoDB\Field(type="float")
     */
    protected $total;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $dateOrder;

    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Set user
     *
     * @param AppBundle\Document\User $user
     * @return self
     */
    public function setUser(\AppBundle\Document\User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return AppBundle\Document\User $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add product
     *
     * @param AppBundle\Document\Product $product
     */
    public function addProduct(\AppBundle\Document\Product $product)
    {
        $this->products[] = $product;
    }

    /**
     * Remove product
     *
     * @param AppBundle\Document\Product $product
     */
    public function removeProduct(\AppBundle\Document\Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection $products
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set total
     *
     * @param float $total
     * @return self
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * Get total
     *
     * @return float $total
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set dateOrder
     *
     * @param date $dateOrder
     * @return self
     */
    public function setDateOrder($dateOrder)
    {
        $this->dateOrder = $dateOrder;
        return $this;
    }

    /**
     * Get dateOrder
     *
     * @return date $dateOrder
     */
    public function getDateOrder()
    {
        return $this->dateOrder;
    }
}
