<?php

/**
 * Created by PhpStorm.
 * User: SM
 * Date: 7/29/18
 * Time: 11:15 AM
 */

namespace UserData;

use Phalcon\Mvc\Model;

class ORDERS extends Model
{
    public $orderid;
    public $userid;
    public $addressid;
    public $rderprice;
    public $order_status;
    public $orderdate;
    public $cardid;

    public function initialize(){
        $this->setSource('ORDERS');
        $this->belongsTo(
            "userid",
            "UserData\USER",
            "userid"
        );
        $this->belongsTo(
            "cardid",
            "UserData\USERCARD",
            "cardid"
        );
        $this->belongsTo(
            "addressid",
            "UserData\USERADDR",
            "addressid"
        );
        $this->hasMany(
            "orderid",
            "UserData\PRODUCT_BY_STORE",
            "orderid"
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
    public function getAddressid()
    {
        return $this->addressid;
    }

    /**
     * @param mixed $addressid
     */
    public function setAddressid($addressid): void
    {
        $this->addressid = $addressid;
    }

    /**
     * @return mixed
     */
    public function getRderprice()
    {
        return $this->rderprice;
    }

    /**
     * @param mixed $rderprice
     */
    public function setRderprice($rderprice): void
    {
        $this->rderprice = $rderprice;
    }

    /**
     * @return mixed
     */
    public function getOrder_status()
    {
        return $this->order_status;
    }

    /**
     * @param mixed $order_status
     */

    public function setOrder_status($order_status): void
    {
        $this->order_status = $order_status;
    }

    /**
     * @return mixed
     */
    public function getOrderdate()
    {
        return $this->orderdate;
    }

    /**
     * @param mixed $orderdate
     */
    public function setOrderdate($orderdate): void
    {
        $this->orderdate = $orderdate;
    }

    /**
     * @return mixed
     */
    public function getCardid()
    {
        return $this->cardid;
    }

    /**
     * @param mixed $cardid
     */
    public function setCardid($cardid): void
    {
        $this->cardid = $cardid;
    }
}