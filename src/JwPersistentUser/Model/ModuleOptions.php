<?php

namespace JwPersistentUser\Model;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $serieTokenEntityClass = 'JwPersistentUser\Model\SerieToken';

    /**
     * @return string
     */
    public function getSerieTokenEntityClass()
    {
        return $this->serieTokenEntityClass;
    }

    /**
     * @param string $serieTokenEntityClass
     * @return $this
     */
    public function setSerieTokenEntityClass($serieTokenEntityClass)
    {
        $this->serieTokenEntityClass = $serieTokenEntityClass;
        return $this;
    }
}
