<?php

namespace UserData;

use Phalcon\Mvc\Model;

class PRODUCT extends Model{

    public $prodid;

    public $prod_name;

    public $categoryid;

    public $brandname;

    public function initialize()
    {
        $this->setSource('PRODUCT');
        $this->belongsTo(
            "categoryid",
            "UserData\CATEGORY",
            "categoryid"
        );
        $this->hasMany(
            "prodid",
            "UserData\PRODUCT_BY_STORE",
            "prodid"
        );
        $this->hasMany(
            "prodid",
            "UserData\CART",
            "prodid"
        );
        $this->hasMany(
            "prodid",
            "UserData\PRODUCT_BY_ORDER",
            "prodid"
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
    public function getProdName()
    {
        return $this->prod_name;
    }

    /**
     * @param mixed $prod_name
     */
    public function setProdName($prod_name): void
    {
        $this->prod_name = $prod_name;
    }

    /**
     * @return mixed
     */
    public function getCategoryid()
    {
        return $this->categoryid;
    }

    /**
     * @param mixed $categoryid
     */
    public function setCategoryid($categoryid): void
    {
        $this->categoryid = $categoryid;
    }

    /**
     * @return mixed
     */
    public function getBrandname()
    {
        return $this->brandname;
    }

    /**
     * @param mixed $brandname
     */
    public function setBrandname($brandname): void
    {
        $this->brandname = $brandname;
    }

}