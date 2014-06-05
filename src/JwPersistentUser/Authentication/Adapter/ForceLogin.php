<?php

namespace JwPersistentUser\Authentication\Adapter;

use ZfcUser\Entity\UserInterface;

use Zend\Authentication\Result,
    Zend\Authentication\Adapter\AdapterInterface;

class ForceLogin implements AdapterInterface
{
    /**
     * @var UserInterface|int
     */
    protected $user;

    /**
     * @param UserInterface|int $user
     */
    function __construct($user)
    {
        $this->user = $user;
    }

    public function authenticate()
    {
        return new Result(
            Result::SUCCESS,
            is_int($this->getUser()) ? $this->getUser() : $this->getUser()->getId()
        );
    }

    /**
     * @return UserInterface|int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface|int $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
}
