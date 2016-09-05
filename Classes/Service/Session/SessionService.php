<?php
namespace MageDeveloper\Dataviewer\Service\Session;

use \MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;

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
class SessionService
{
	/**
	 * Prefix Key
	 * @var string
	 */
	protected $prefixKey = "tx_dataviewer";

	/**
	 * Sets the session prefix key
	 * 
	 * @param string $prefixKey
	 */
	public function setPrefixKey($prefixKey)
	{
		$this->prefixKey = $prefixKey;
	}

	/**
	 * Gets the session prefix key
	 * 
	 * @return string
	 */
	public function getPrefixKey()
	{
		return $this->prefixKey;
	}

	/**
	 * Class constructor.
	 * 
	 * @return SessionService
	 * @throws \Exception
	 */
	public function __construct() 
	{
		$this->sessionObject = $GLOBALS['TSFE']->fe_user;
	}

	/**
	 * Restores data from the session
	 * 
	 * @param string $key
	 * @return mixed
	 */
	protected function restoreFromSession($key) 
	{
		$sessionData = $this->sessionObject->getKey('ses', $this->prefixKey . $key);
		return unserialize($sessionData);
	}

	/**
	 * Writes data to the session
	 * 
	 * @param mixed $object Object to write to the session
	 * @param string $key Identifier for the session
	 * @return \MageDeveloper\Dataviewer\Service\Session\SessionService
	 */
	protected function writeToSession($object, $key) 
	{
		$sessionData = serialize($object);
		$this->sessionObject->setKey('ses', $this->prefixKey . $key, $sessionData);
		$this->sessionObject->storeSessionData();
		return $this;
	}

	/**
	 * Cleans a variable that is stored in the session
	 * 
	 * @param string $key
	 * @return \MageDeveloper\Dataviewer\Service\Session\SessionService
	 */
	protected function cleanUpSession($key) 
	{
		$this->sessionObject->setKey('ses', $this->prefixKey . $key, NULL);
		$this->sessionObject->storeSessionData();
		return $this;
	}


	/**
	 * Set/Get attribute wrapper
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
				return $this->getData($key);
			case "set":
				return $this->setData($key, isset($args[0]) ? $args[0] : null);
			case "uns":
				return $this->unsetData($key);
			case "has":
				return $this->hasData($key);
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

	/**
	 * Sets data to the session
	 * 
	 * @param string $key Session Identifier
	 * @param mixed $value The value to set
	 * @return \MageDeveloper\Dataviewer\Service\Session\SessionService
	 */
	public function setData($key, $value)
	{
		return $this->writeToSession($value, $key);
	}

	/**
	 * Gets data from the session
	 * 
	 * @param string $key Session Identifier
	 * @return mixed
	 */
	public function getData($key)
	{
		$data = $this->restoreFromSession($key);
		return $data;
	}

	/**
	 * Checks if a session value exists
	 * 
	 * @param string $key
	 * @return bool
	 */
	public function hasData($key)
	{
		$data = $this->restoreFromSession($key);
		return ($data)?true:false;
	}

	/**
	 * Unsets data from the session
	 * 
	 * @param string $key
	 * @return \MageDeveloper\Dataviewer\Service\Session\SessionService
	 */
	public function unsetData($key)
	{
		return $this->cleanUpSession($key);
	}

}
