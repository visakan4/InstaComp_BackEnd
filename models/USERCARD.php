<?php
/**
 * Created by PhpStorm.
 * User: visak
 * Date: 2018-07-14
 * Time: 6:19 PM
 */

namespace UserData;

use Phalcon\Mvc\Model;

class USERCARD extends Model{

    public $cardid;

    public $cardno;

    public $expiry_dt;

    public $cvv;

    public $cardtype;

    public $userid;

    public function initialize()
    {
        $this->setSource('USERCARD');
        $this->belongsTo(
            "userid",
            "UserData\USER",
            "userid"
        );
    }

    /**
     * @return mixed
     */
    public function getCardId()
    {
        return $this->card_id;
    }

    /**
     * @param mixed $card_id
     */
    public function setCardId($card_id): void
    {
        $this->card_id = $card_id;
    }

    /**
     * @return mixed
     */
    public function getCardno()
    {
        return $this->cardno;
    }

    /**
     * @param mixed $cardno
     */
    public function setCardno($cardno): void
    {
        $this->cardno = $cardno;
    }

    /**
     * @return mixed
     */
    public function getExpiryDt()
    {
        return $this->expiry_dt;
    }

    /**
     * @param mixed $expiry_dt
     */
    public function setExpiryDt($expiry_dt): void
    {
        $this->expiry_dt = $expiry_dt;
    }

    /**
     * @return mixed
     */
    public function getCvv()
    {
        return $this->cvv;
    }

    /**
     * @param mixed $cvv
     */
    public function setCvv($cvv): void
    {
        $this->cvv = $cvv;
    }

    /**
     * @return mixed
     */
    public function getCardtype()
    {
        return $this->cardtype;
    }

    /**
     * @param mixed $cardtype
     */
    public function setCardtype($cardtype): void
    {
        $this->cardtype = $cardtype;
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
}