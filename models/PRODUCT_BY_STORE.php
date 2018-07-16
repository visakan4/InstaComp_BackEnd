<?php

namespace UserData;

use Phalcon\Mvc\Model;

class PRODUCT_BY_STORE extends Model{

    public $prodid;

    public $storeid;

    public $price;

    public $quantity;

    public $store_type;

    public function initialize()
    {
        $this->setSource('PRODUCT_BY_STORE');
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

    /**
     * @return mixed
     */
    public function getStoreType()
    {
        return $this->store_type;
    }

    /**
     * @param mixed $store_type
     */
    public function setStoreType($store_type): void
    {
        $this->store_type = $store_type;
    }
}