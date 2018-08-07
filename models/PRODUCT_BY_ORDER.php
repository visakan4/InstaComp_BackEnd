<?php
/**
 * Created by PhpStorm.
 * User: visak
 * Date: 2018-08-07
 * Time: 6:24 PM
 */

namespace UserData;
use Phalcon\Mvc\Model;

class PRODUCT_BY_ORDER extends Model
{

    public $orderid;

    public $prodid;

    public $storeid;

    public $price;

    public $quantity;


    public function initialize(){
        $this->setSource('PRODUCT_BY_ORDER');
        $this->belongsTo(
            "orderid",
            "UserData\ORDERS",
            "orderid"
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
    public function getOrderid()
    {
        return $this->orderid;
    }

    /**
     * @param mixed $orderid
     */
    public function setOrderid($orderid): void
    {
        $this->orderid = $orderid;
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