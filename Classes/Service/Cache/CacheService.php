<?php
namespace MageDeveloper\Dataviewer\Service\Cache;

use TYPO3\CMS\Extbase\Object\Container\Exception\CannotInitializeCacheException;

/**
 * MageDeveloper Dataviewer Extension
 * -----------------------------------
 *
 * @category    TYPO3 Extension
 * @package     MageDeveloper\Dataviewer
 * @author		Bastian Zagar
 * @copyright   Magento Developers / magedeveloper.de <kontakt@magedeveloper.de>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CacheService implements \TYPO3\CMS\Core\SingletonInterface
{
	/**
	 * Cache Instance
	 *
	 * @var \TYPO3\CMS\Core\Cache\Frontend\AbstractFrontend
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
	 * @var \TYPO3\CMS\Core\Cache\CacheManager
	 * @inject
	 */
	protected $cacheManager;

	/**
	 * Gets the cache manager
	 *
	 * @return object|\TYPO3\CMS\Core\Cache\CacheManager
	 */
	public function getCacheManager()
	{
		if (!$this->cacheManager instanceof \TYPO3\CMS\Core\Cache\CacheManager)
			$this->cacheManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class);

		return $this->cacheManager;
	}

	/**
	 * Sets the cache name
	 *
	 * @param mixed $cacheName
	 */
	public function setCacheName($cacheName)
	{
		$this->cacheName = $cacheName;
		$this->initializeCache();
	}

	/**
	 * Gets the cache name
	 *
	 * @return mixed
	 */
	public function getCacheName()
	{
		return $this->cacheName;
	}

	/**
	 * Get entry from caching framework
	 *
	 * @param string $cacheIdentifier cache identifier
	 * @return entry or NULL if not found
	 */
	public function get($cacheIdentifier)
	{
		$entry = $this->getCacheManager()->getCache( $this->getCacheName() )
			->get($cacheIdentifier);
		return $entry;
	}

	/**
	 * Set an entry to the caching framework
	 *
	 * @param string $cacheIdentifier
	 * @param string $entry
	 * @param array $tags
	 * @param integer $lifetime
	 * @return void
	 */
	public function set($cacheIdentifier, $entry, array $tags = array(), $lifetime = NULL)
	{
		$this->getCacheManager()->getCache( $this->getCacheName() )
			->set($cacheIdentifier, $entry, $tags, $lifetime);
	}

	/**
	 * Checks if the cache has an cache identifier
	 *
	 * @param string $cacheIdentifier
	 * @return bool
	 * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
	 */
	public function has($cacheIdentifier)
	{
		return $this->getCacheManager()->getCache( $this->getCacheName() )
			->has($cacheIdentifier);
	}

	/**
	 * Removes an cache identifier from the cache
	 *
	 * @param string $cacheIdentifier
	 * @return bool
	 * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
	 */
	public function remove($cacheIdentifier)
	{
		return $this->getCacheManager()->getCache( $this->getCacheName() )
			->remove($cacheIdentifier);
	}

	/**
	 * Flush cache
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->getCacheManager()->getCache( $this->getCacheName() )
			->flush();
	}

	/**
	 * Initialize cache instance to be ready to use
	 *
	 * @throws \TYPO3\CMS\Extbase\Object\Container\Exception\CannotInitializeCacheException
	 * @return void
	 */
	protected function initializeCache()
	{
		try
		{
			if (!$this->getCacheManager()->hasCache( $this->getCacheName() ))
			{
				$typo3CacheFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheFactory::class);

				$this->cacheInstance = $typo3CacheFactory->create(
					$this->getCacheName(),
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->getCacheName()]['frontend'],
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->getCacheName()]['backend'],
					(array)$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->getCacheName()]['options']
				);


			}

		}
		catch (Exception $e)
		{
			throw new \TYPO3\CMS\Extbase\Object\Container\Exception\CannotInitializeCacheException($e->getMessage());
		}
	}

	/**
	 * Set/Get cache wrapper
	 * @param string $method
	 * @param array $args
	 * @throws \Exception
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		$key = $this->_underscore(substr($method,3));
		switch( substr($method, 0, 3) )
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

		throw new \Exception("Invalid method " . get_class($this) . "::" . $method . "(" . print_r($args, 1) . ")");
	}

	/**
	 * Converts field names for Setters and Getters
	 * @param string $name
	 * @return string
	 */
	protected function _underscore($name)
	{
		$result = strtolower(preg_replace("/(.)([A-Z])/", "$1_$2", $name));
		return $result;
	}
}
