<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Cache;

class TcaCacheService extends CacheService
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setCacheName("dataviewer_tca_cache");
    }

}
