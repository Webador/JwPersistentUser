<?php

namespace JwPersistentUser\Mapper;

use JwPersistentUser\Model\SerieTokenInterface;

interface SerieTokenMapperInterface
{
    /**
     * @param SerieTokenInterface $token
     */
    public function persist(SerieTokenInterface $token);

    /**
     * @param int $userId
     * @param string $serieId
     * @return SerieTokenInterface
     */
    public function find($userId, $serieId);

    /**
     * @param SerieTokenInterface $token
     */
    public function remove(SerieTokenInterface $token);
}
