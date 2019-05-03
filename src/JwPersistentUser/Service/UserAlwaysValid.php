<?php

namespace JwPersistentUser\Service;

class UserAlwaysValid implements UserValidityInterface
{
    public function getValidUntilForUser($userId)
    {
        return null;
    }
}
