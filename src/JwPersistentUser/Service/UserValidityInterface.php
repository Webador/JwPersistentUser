<?php

namespace JwPersistentUser\Service;

interface UserValidityInterface
{
    public function getValidUntilForUser($userId);
}
