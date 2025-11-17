<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Session;

use Exception;

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
    public function setAccordingPid(int $pid): void
    {
        $storageKey = $this->storageKey . "-" . $pid;
        $this->setStorageKey($storageKey);
    }

    /**
     * Sets the session storage key
     *
     * @param string $storageKey
     * @return void
     */
    public function setStorageKey(string $storageKey): void
    {
        $this->storageKey = $storageKey;
    }

    /**
     * Sets a value to the backend session
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $data       = $GLOBALS["BE_USER"]->getSessionData($this->storageKey);
        $data[$key] = $value;
        $GLOBALS["BE_USER"]->setAndSaveSessionData($this->storageKey, $data);
    }

    /**
     * Removes a value from the backend session
     *
     * @param string $key
     * @return void
     */
    public function unsetData(string $key): void
    {
        $data = $GLOBALS["BE_USER"]->getSessionData($this->storageKey);
        unset($data[$key]);
        $GLOBALS["BE_USER"]->setAndSaveSessionData($this->storageKey, $data);
    }


    /**
     * Gets a value from the backend session
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $data = $GLOBALS["BE_USER"]->getSessionData($this->storageKey);

        return isset($data[$key]) ? $data[$key] : null;
    }

    /**
     * Set/Get attribute wrapper
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws Exception
     */
    public function __call(string $method, array $args)
    {
        $key = $this->_underscore(substr($method, 3));
        switch (substr($method, 0, 3)) {
            case "get":
                return $this->get($key);
            case "set":
                $this->set($key, isset($args[0]) ? $args[0] : null);
                return null;
            case "uns":
                $this->unsetData($key);

                return null;
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
