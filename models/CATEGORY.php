<?php

namespace UserData;

use Phalcon\Mvc\Model;

class CATEGORY extends Model{

    public $categoryid;

    public $category_name;

    public function initialize()
    {
        $this->setSource('CATEGORY');
        $this->hasMany(
            "categoryid",
            "UserData\PRODUCT",
            "categoryid"
        );
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
    public function getCategoryName()
    {
        return $this->category_name;
    }

    /**
     * @param mixed $category_name
     */
    public function setCategoryName($category_name): void
    {
        $this->category_name = $category_name;
    }
}