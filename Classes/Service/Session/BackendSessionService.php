<?php
namespace MageDeveloper\Dataviewer\Service\Session;

use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
class BackendSessionService
{
	/**
	 * @var string
	 */
	protected $storageKey = "tx_dataviewer";

	/**
	 * Sets the according pid and calculated
	 * the according session storage key
	 * 
	 * @param int $pid
	 * @return void
	 */
	public function setAccordingPid($pid)
	{
		$storageKey = $this->storageKey . "-" . (int)$pid;
		$this->setStorageKey($storageKey);
	}

	/**
	 * Sets the session storage key
	 * 
	 * @param string $storageKey
	 * @return void
	 */
	public function setStorageKey($storageKey) 
	{
		$this->storageKey = $storageKey;
	}

	/**
	 * Sets a value to the backend session
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	public function set($key, $value) 
	{
		$data = $GLOBALS["BE_USER"]->getSessionData($this->storageKey);
		$data[$key] = $value;
		return $GLOBALS["BE_USER"]->setAndSaveSessionData($this->storageKey, $data);
	}

	/**
	 * Removes a value from the backend session
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function unsetData($key) 
	{
		$data = $GLOBALS["BE_USER"]->getSessionData($this->storageKey);
		unset($data[$key]);
		return $GLOBALS["BE_USER"]->setAndSaveSessionData($this->storageKey, $data);
	}


	/**
	 * Gets a value from the backend session
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) 
	{
		$data = $GLOBALS["BE_USER"]->getSessionData($this->storageKey);
		return isset($data[$key]) ? $data[$key] : NULL;
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
				return $this->get($key);
			case "set":
				return $this->set($key, isset($args[0]) ? $args[0] : null);
			case "uns":
				return $this->unsetData($key);
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
