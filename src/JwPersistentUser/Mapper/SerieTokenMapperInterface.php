<?php

namespace JwPersistentUser\Mapper;

use JwPersistentUser\Model\SerieToken;

interface SerieTokenMapperInterface
{
    /**
     * @param SerieToken $token
     */
    public function persist(SerieToken $token);

    /**
     * @param int $userId
     * @param string $serieId
     * @return SerieToken
     */
    public function find($userId, $serieId);

    /**
     * @param SerieToken $token
     */
    public function remove(SerieToken $token);
}
