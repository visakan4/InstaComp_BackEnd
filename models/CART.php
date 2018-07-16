<?php

namespace UserData;

use Phalcon\Mvc\Model;

class CART extends Model{

    public $cartid;

    public $userid;

    public $prodid;

    public $storeid;

    public $price;

    public $quantity;

    public function initialize()
    {
        $this->setSource('CART');
        $this->belongsTo(
            "userid",
            "UserData\USER",
            "userid"
        );
        $this->belongsTo(
            "prodid",
            "UserData\PRODUCT",
            "prodid"
        );
        $this->belongsTo(
            "storeid",
            "UserData\STORE",
            "storeid"
        );
    }

    /**
     * @return mixed
     */
    public function getCartid()
    {
        return $this->cartid;
    }

    /**
     * @param mixed $cartid
     */
    public function setCartid($cartid): void
    {
        $this->cartid = $cartid;
    }

    /**
     * @return mixed
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @param mixed $userid
     */
    public function setUserid($userid): void
    {
        $this->userid = $userid;
    }

    /**
     * @return mixed
     */
    public function getProdid()
    {
        return $this->prodid;
    }

    /**
     * @param mixed $prodid
     */
    public function setProdid($prodid): void
    {
        $this->prodid = $prodid;
    }

    /**
     * @return mixed
     */
    public function getStoreid()
    {
        return $this->storeid;
    }

    /**
     * @param mixed $storeid
     */
    public function setStoreid($storeid): void
    {
        $this->storeid = $storeid;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

}