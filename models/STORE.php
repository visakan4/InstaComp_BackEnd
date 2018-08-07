<?php

namespace UserData;

use Phalcon\Mvc\Model;

class STORE extends Model{

    public $storeid;

    public $store_name;

    public $store_lat;

    public $store_long;

    public function initialize()
    {
        $this->setSource('STORE');
        $this->hasMany(
            "storeid",
            "UserData\PRODUCT_BY_STORE",
            "storeid"
        );
        $this->hasMany(
            "storeid",
            "UserData\CART",
            "storeid"
        );
        $this->hasMany(
            "storeid",
            "UserData\PRODUCT_BY_ORDER",
            "storeid"
        );
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
    public function getStoreName()
    {
        return $this->store_name;
    }

    /**
     * @param mixed $store_name
     */
    public function setStoreName($store_name): void
    {
        $this->store_name = $store_name;
    }

    /**
     * @return mixed
     */
    public function getStoreLat()
    {
        return $this->store_lat;
    }

    /**
     * @param mixed $store_lat
     */
    public function setStoreLat($store_lat): void
    {
        $this->store_lat = $store_lat;
    }

    /**
     * @return mixed
     */
    public function getStoreLong()
    {
        return $this->store_long;
    }

    /**
     * @param mixed $store_long
     */
    public function setStoreLong($store_long): void
    {
        $this->store_long = $store_long;
    }
}