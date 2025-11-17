<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Cache;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CacheService implements SingletonInterface
{
	/**
	 * @var PhpFrontend
	 */
	protected $cacheInstance;

	/**
	 * Cache Name
	 * @var string
	 */
	protected $cacheName;

	/**
	 * TYPO3 Cache Manager
	 *
	 * @var CacheManager
	 */
	protected $cacheManager;

	/**
	 * Gets the cache manager
	 *
	 * @return object|CacheManager
	 */
	public function getCacheManager(): CacheManager
	{
		if (!$this->cacheManager instanceof CacheManager) {
            $this->cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        }

		return $this->cacheManager;
	}

	/**
	 * Sets the cache name
	 *
	 * @param mixed $cacheName
     * @return void
	 */
	public function setCacheName($cacheName): void
	{
		$this->cacheName = $cacheName;
		$this->initializeCache();
	}

	/**
	 * Gets the cache name
	 *
	 * @return string
	 */
	public function getCacheName(): string
	{
		return (string)$this->cacheName;
	}

	/**
	 * Get entry from caching framework
	 *
	 * @param string $cacheIdentifier cache identifier
	 * @return mixed
     * @throws NoSuchCacheException
	 */
	public function get(string $cacheIdentifier)
	{
		$entry = $this->getCacheManager()->getCache( $this->getCacheName() )
			->get($cacheIdentifier);
		return $entry;
	}

    /**
     * Set an entry to the caching framework
     *
     * @param string $cacheIdentifier
     * @param mixed $entry
     * @param array $tags
     * @param int|null $lifetime
     * @return self
     * @throws NoSuchCacheException
     */
    public function set(string $cacheIdentifier, $entry, array $tags = array(), ?int $lifetime = null): CacheService
	{
		$this->getCacheManager()->getCache( $this->getCacheName() )
			->set($cacheIdentifier, $entry, $tags, $lifetime);

        return $this;
	}

    /**
     * Checks if the cache has an cache identifier
     *
     * @param string $cacheIdentifier
     * @return bool
     * @throws NoSuchCacheException
     */
    public function has(string $cacheIdentifier): bool
    {
        return $this->getCacheManager()->getCache($this->getCacheName())
            ->has($cacheIdentifier);
    }

    /**
     * Removes an cache identifier from the cache
     *
     * @param string $cacheIdentifier
     * @return bool
     * @throws NoSuchCacheException
     */
    public function remove(string $cacheIdentifier): bool
    {
        return $this->getCacheManager()->getCache($this->getCacheName())
            ->remove($cacheIdentifier);
    }


    /**
     * Flush cache
     *
     * @return void
     * @throws NoSuchCacheException
     */
    public function flush()
    {
        $this->getCacheManager()->getCache($this->getCacheName())
            ->flush();
    }

    /**
     * Initialize cache instance to be ready to use
     *
     * @return void
     * @throws \TYPO3\CMS\Core\Cache\Exception
     */
    protected function initializeCache()
    {
        try {
            if (!$this->getCacheManager()->hasCache($this->getCacheName())) {
                $cacheManager = GeneralUtility::makeInstance(CacheManager::class);

                if ($cacheManager->hasCache($this->getCacheName())) {
                    $this->cacheInstance = $cacheManager->getCache($this->getCacheName());
                }
            }

        } catch (Exception $e) {
            throw new \TYPO3\CMS\Core\Cache\Exception($e->getMessage());
        }
    }

	/**
	 * Set/Get cache wrapper
	 * @param string $method
	 * @param array $args
	 * @throws \Exception
	 * @return mixed
	 */
    public function __call(string $method, array $args)
	{
		$key = $this->_underscore(substr($method,3));
		switch ( substr($method, 0, 3) )
		{
			case "get":
				return $this->get($key);
			case "set":
				$tags = array();
				$lifetime = null;
				if (isset($args[1]) && is_array($args[1])) $tags = $args[1];
				if (isset($args[2])) $lifetime = $args[2];
				return $this->set($key, $args[0], $tags, $lifetime);
			case "uns":
				return $this->remove($key);
			case "has":
				return $this->has($key);
		}

		throw new Exception("Invalid method " . get_class($this) . "::" . $method . "(" . print_r($args, true) . ")");
	}

    /**
     * Converts field names for Setters and Getters
     * @param string $name
     * @return string
     */
    protected function _underscore(string $name): string
    {
        return strtolower(preg_replace("/(.)([A-Z])/", "$1_$2", $name));
    }
}
