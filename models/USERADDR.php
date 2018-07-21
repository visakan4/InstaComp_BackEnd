<?php

namespace UserData;

use Phalcon\Mvc\Model;


Class USERADDR extends Model{

    public $addressid;

    public $addr_line1;

    public $addr_line2;

    public $city;

    public $province;

    public $postal_code;

    public $userid;

    public function initialize()
    {
        $this->setSource('USERADDR');
        $this->belongsTo(
            "userid",
            "UserData\USER",
            "userid"
        );
    }

    /**
     * @return mixed
     */
    public function getAddressId()
    {
        return $this->addressid;
    }

    /**
     * @param mixed $addressid
     */
    public function setAddressId($addressid): void
    {
        $this->addressid = $addressid;
    }

    /**
     * @return mixed
     */
    public function getAddrLine1()
    {
        return $this->addr_line1;
    }

    /**
     * @param mixed $addr_line1
     */
    public function setAddrLine1($addr_line1): void
    {
        $this->addr_line1 = $addr_line1;
    }

    /**
     * @return mixed
     */
    public function getAddrLine2()
    {
        return $this->addr_line2;
    }

    /**
     * @param mixed $addr_line2
     */
    public function setAddrLine2($addr_line2): void
    {
        $this->addr_line2 = $addr_line2;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @param mixed $province
     */
    public function setProvince($province): void
    {
        $this->province = $province;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    /**
     * @param mixed $postal_code
     */
    public function setPostalCode($postal_code): void
    {
        $this->postal_code = $postal_code;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userid;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($userid): void
    {
        $this->userid = $userid;
    }
}