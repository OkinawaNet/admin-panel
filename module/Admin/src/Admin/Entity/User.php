<?php

namespace Admin\Entity;

use ZfcUser\Entity\User as ZfcUserEntity;

class User extends ZfcUserEntity
{
    protected $firstName;

    protected $secondName;

    /**
     * @return the $firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return the $secondName
     */
    public function getSecondName()
    {
        return $this->secondName;
    }

    /**
     * @param unknown $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = (int)$firstName;
        return $this;
    }

    /**
     * @param field_type $secondName
     */
    public function setSecondName($secondName)
    {
        $this->secondName = (int)$secondName;
        return $this;
    }

}