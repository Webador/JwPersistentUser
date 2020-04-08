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
     * @param int $userId
     * @return mixed
     */
    public function findAllByUser(int $userId);

    /**
     * @param SerieTokenInterface $token
     */
    public function remove(SerieTokenInterface $token);
}
