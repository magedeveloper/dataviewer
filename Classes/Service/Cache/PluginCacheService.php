<?php
namespace MageDeveloper\Dataviewer\Service\Cache;

use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;

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
class PluginCacheService extends CacheService
{
	/**
	 * Cache Lifetime in Seconds
	 * 
	 * @var int|null
	 */
	protected $lifetime = null;

	/**
	 * Constructor
	 *
	 * @return PluginCacheService
	 */
	public function __construct()
	{
		$this->setCacheName( Configuration::EXTENSION_KEY . "_cache" );
		$this->initializeCache();
	}

	/**
	 * Gets the cache lifetime
	 * 
	 * @return int
	 */
	public function getLifetime()
	{
		return $this->lifetime;
	}

	/**
	 * Sets the cache lifetime
	 * 
	 * @param int $lifetime
	 * @return void
	 */
	public function setLifetime($lifetime)
	{
		$this->lifetime = (int)$lifetime;
	}

	/**
	 * Set valid record ids to the cache for
	 * a specific cache identifier
	 * 
	 * @param string $cacheIdentifier
	 * @param array $recordIds
	 * @return void
	 */
	public function setValidRecordIds($cacheIdentifier, array $recordIds = [])
	{
		$cacheIdentifier .= "_valid";
		return $this->set($cacheIdentifier, $recordIds, [], $this->getLifetime());
	}

	/**
	 * Gets the valid record ids for a
	 * specific cache identifier
	 * 
	 * @param $cacheIdentifier
	 * @return array
	 */
	public function getValidRecordIds($cacheIdentifier)
	{
		$cacheIdentifier .= "_valid";
		$ids = $this->get($cacheIdentifier);
		
		return $ids;
	}
}
