<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Domain\Model;

use Aix\Dataviewer\Factory\VariableFactory;
use Aix\Dataviewer\Service\FlexForm\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Variable extends AbstractModel
{
	/**
	 * Variable Types
	 *
	 * @var int
	 */
	const VARIABLE_TYPE_FIXED 			    = 0;
	const VARIBALE_TYPE_TYPOSCRIPT 		    = 1;
	const VARIABLE_TYPE_TYPOSCRIPT_VAR      = 2;
	const VARIABLE_TYPE_GET				    = 3;
	const VARIABLE_TYPE_POST			    = 4;
	const VARIABLE_TYPE_GET_POST			= 5;
	//const VARIABLE_TYPE_RECORD_FIELD	    = 6;
	const VARIABLE_TYPE_DATABASE		    = 7;
	const VARIABLE_TYPE_FRONTEND_USER	    = 8;
	const VARIABLE_TYPE_SERVER			    = 9;
	const VARIABLE_TYPE_DYNAMIC_RECORD	    = 10;
	const VARIABLE_TYPE_USER_SESSION	    = 11;
	const VARIABLE_TYPE_PAGE			    = 12;
    const VARIABLE_TYPE_USERFUNC		    = 13;
    const VARIABLE_TYPE_BACKEND_USER	    = 14;
    const VARIABLE_TYPE_LANGUAGE_UID	    = 15;
    const VARIABLE_TYPE_SESSION_CONTAINER   = 16;
    const VARIABLE_TYPE_EXTENSION_CONFIG    = 17;
    const VARIABLE_TYPE_TYPOSCRIPT_SETTINGS = 18;

	/**
	 * Type Casting
	 *
	 * @var int
	 */
	const VARIABLE_TYPE_CAST_NONE		= 0;
	const VARIABLE_TYPE_CAST_BOOLEAN 	= 1;
	const VARIABLE_TYPE_CAST_INTEGER	= 2;
	const VARIABLE_TYPE_CAST_FLOAT		= 3;
	const VARIABLE_TYPE_CAST_STRING		= 4;
	const VARIABLE_TYPE_CAST_ARRAY		= 5;
	const VARIABLE_TYPE_CAST_OBJECT		= 6;
	const VARIABLE_TYPE_CAST_NULL		= 7;

	/**
	 * Variable Type
	 *
	 * @var int
	 */
	protected $type = 0;

	/**
	 * Variable Name
	 *
	 * @var string
	 */
	protected $variableName;

    /**
     * Parameter Name
     *
     * @var string
     */
    protected $parameterName = '';

	/**
	 * Session Key
	 *
	 * @var string
	 */
	protected $sessionKey;

	/**
	 * Variable Value
	 *
	 * @var string
	 */
	protected $variableValue;

	/**
	 * Variable Type Cast
	 *
	 * @var int
	 */
	protected $typeCast = 0;

    /**
     * Extension Name to load
     * config from
     * @var string
     */
	protected $extConf = '';

    /**
     * TypoScript Path
     *
     * @var string
     */
	protected $typoscriptPath = '';

	/**
	 * Field
	 *
	 * @var Field
	 */
	protected $field = null;

	/**
	 * Table Select Field
	 *
	 * @var string
	 */
	protected $tableContent = '';

	/**
	 * Column Name Field
	 *
	 * @var string
	 */
	protected $columnName = '';

	/**
	 * Where Clause for selection
	 *
	 * @var string
	 */
	protected $whereClause = '';

	/**
	 * SERVER Environment Value
	 *
	 * @var string
	 */
	protected $server = '';

	/**
	 * Page
	 *
	 * @var int
	 */
	protected $page = 0;

	/**
	 * User Func
	 *
	 * @var string
	 */
	protected $userFunc = '';

    /**
     * @var string
     */
	protected $allowedValues = '';

    /**
     * @var string
     */
	protected $regex = '';

    /**
     * @var string
     */
	protected $valueSwitch = '';

    /**
     * @var null|Datatype
     */
    protected $datatype = null;

	/**
	 * Gets the type
	 *
	 * @return int
	 */
	public function getType(): int
	{
		return $this->type;
	}

	/**
	 * Sets the type
	 *
	 * @param mixed $type Cast to int type
	 * @return void
	 */
	public function setType($type)
	{
		$this->type = (int)$type;
	}

	/**
	 * Gets the variable name
	 *
	 * @return string
	 */
	public function getVariableName(): string
	{
		return $this->variableName;
	}

	/**
	 * Sets the variable name
	 *
	 * @param string $variableName
	 * @return void
	 */
	public function setVariableName(string $variableName): void
	{
		$this->variableName = $variableName;
	}

    /**
     * @return string
     */
    public function getParameterName(): string
    {
        return $this->parameterName;
    }

    /**
     * @param string $parameterName
     */
    public function setParameterName(string $parameterName): void
    {
        $this->parameterName = $parameterName;
    }

	/**
	 * Gets the session key
	 *
	 * @return string
	 */
	public function getSessionKey(): string
	{
		return $this->sessionKey;
	}

	/**
	 * Sets the session key
	 *
	 * @param string $sessionKey
	 * @return void
	 */
	public function setSessionKey(string $sessionKey): void
	{
		$this->sessionKey = $sessionKey;
	}

	/**
	 * Gets the variable value
	 *
	 * @return string
	 */
	public function getVariableValue(): string
	{
		return $this->variableValue;
	}

	/**
	 * Sets the variable value
	 *
	 * @param string $variableValue
	 * @return void
	 */
	public function setVariableValue(string $variableValue): void
	{
		$this->variableValue = $variableValue;
	}

    /**
     * @return string
     */
    public function getExtConf(): string
    {
        return $this->extConf;
    }

    /**
     * @param string $extConf
     */
    public function setExtConf(string $extConf): void
    {
        $this->extConf = $extConf;
    }

    /**
     * @return string
     */
    public function getTyposcriptPath(): string
    {
        return $this->typoscriptPath;
    }

    /**
     * @param string $typoscriptPath
     */
    public function setTyposcriptPath(string $typoscriptPath): void
    {
        $this->typoscriptPath = $typoscriptPath;
    }

	/**
	 * Returns the field
	 *
	 * @return Field $field
	 */
	public function getField(): Field
	{
		return $this->field;
	}

	/**
	 * Sets the field
	 *
	 * @param Field $field
	 * @return void
	 */
	public function setField(\Aix\Dataviewer\Domain\Model\Field $field): void
	{
		$this->field = $field;
	}

	/**
	 * Returns the tableContent
	 *
	 * @return string $tableContent
	 */
	public function getTableContent(): string
	{
		return $this->tableContent;
	}

	/**
	 * Sets the tableContent
	 *
	 * @param string $tableContent
	 * @return void
	 */
	public function setTableContent(string $tableContent): void
	{
		$this->tableContent = $tableContent;
	}

	/**
	 * Returns the columnName
	 *
	 * @return string $columnName
	 */
	public function getColumnName(): string
	{
		return $this->columnName;
	}

	/**
	 * Sets the columnName
	 *
	 * @param string $columnName
	 * @return void
	 */
	public function setColumnName(string $columnName): void
	{
		$this->columnName = $columnName;
	}

	/**
	 * Returns the whereClause
	 *
	 * @return string $whereClause
	 */
	public function getWhereClause(): string
	{
		return $this->whereClause;
	}

	/**
	 * Sets the whereClause
	 *
	 * @param string $whereClause
	 * @return void
	 */
	public function setWhereClause(string $whereClause): void
	{
		$this->whereClause = $whereClause;
	}

	/**
	 * Gets the name for the server
	 * environment variable name
	 *
	 * @return string
	 */
	public function getServer(): string
	{
		return $this->server;
	}

	/**
	 * Sets a server environment variable name
	 *
	 * @param string $server
	 * @return void
	 */
	public function setServer(string $server): void
	{
		$this->server = $server;
	}

	/**
	 * Gets the page
	 *
	 * @return int
	 */
	public function getPage(): int
	{
		return $this->page;
	}

	/**
	 * Sets the page
	 *
	 * @param int $page
	 * @return void
	 */
	public function setPage(int $page): void
	{
		$this->page = $page;
	}

	/**
	 * Gets the user func
	 *
	 * @return string
	 */
	public function getUserFunc(): string
	{
		return $this->userFunc;
	}

	/**
	 * Sets a user func
	 *
	 * @param string $userFunc
	 * @return void
	 */
	public function setUserFunc(string $userFunc): void
	{
		$this->userFunc = $userFunc;
	}

	/**
	 * Gets the type cast of the variable
	 *
	 * @return int
	 */
	public function getTypeCast(): int
	{
		return $this->typeCast;
	}

	/**
	 * Sets the type cast of the variable
	 *
	 * @param int $typeCast
	 * @return void
	 */
	public function setTypeCast(int $typeCast): void
	{
		$this->typeCast = $typeCast;
	}

	/**
	 * Casts a type for a value
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function castType($value)
	{
		$typeCast = $this->getTypeCast();

		switch ($typeCast)
		{
			case self::VARIABLE_TYPE_CAST_BOOLEAN:
				$success = settype($value, "boolean");
				break;
			case self::VARIABLE_TYPE_CAST_INTEGER:
				$success = settype($value, "integer");
				break;
			case self::VARIABLE_TYPE_CAST_FLOAT:
				$success = settype($value, "float");
				break;
			case self::VARIABLE_TYPE_CAST_STRING:
				$success = settype($value, "string");
				break;
			case self::VARIABLE_TYPE_CAST_ARRAY:
				$success = settype($value, "array");
				break;
			case self::VARIABLE_TYPE_CAST_OBJECT:
				$success = settype($value, "object");
				break;
			case self::VARIABLE_TYPE_CAST_NULL:
				$success = settype($value, "null");
				break;
			case self::VARIABLE_TYPE_CAST_NONE:
			default:
				$success = true;
				break;
		}

		if ($success === false)
			$value = NULL;

		return $value;
	}

    /**
     * Retrieve the generated variable value
     *
     * @return mixed
     */
	public function getValue()
    {
        try {
            $variableFactory = GeneralUtility::makeInstance(VariableFactory::class);
            $value = $variableFactory->prepareVariableValue($this);
        } catch (\Exception $e) {
            throw $e;
        }

        return $value;
    }

    /**
     * @param string $allowedValues
     */
    public function setAllowedValues(string $allowedValues): void
    {
        $this->allowedValues = $allowedValues;
    }

    /**
     * @return array
     */
    public function getAllowedValues(): array
    {
        $flexformService = GeneralUtility::makeInstance(FlexFormService::class);
        $flex = $flexformService->convertFlexFormContentToArray($this->allowedValues);
        $allowedValues = [];

        if(array_key_exists('allowed_values', $flex) && is_array($flex) && ($flex['allowed_values']) && !empty($flex['allowed_values'])) {
            foreach($flex['allowed_values'] as $_allowedValue) {
                $value = $_allowedValue['values']['value'];
                switch ($this->getTypeCast())
                {
                    case self::VARIABLE_TYPE_CAST_BOOLEAN:
                        $allowedValues[] = (bool)$value;
                        break;
                    case self::VARIABLE_TYPE_CAST_INTEGER:
                        $allowedValues[] = (int)$value;
                        break;
                    case self::VARIABLE_TYPE_CAST_FLOAT:
                        $allowedValues[] = (float)$value;
                        break;
                    case self::VARIABLE_TYPE_CAST_NONE:
                    case self::VARIABLE_TYPE_CAST_NULL:
                    case self::VARIABLE_TYPE_CAST_STRING:
                    default:
                        $allowedValues[] = (string)$value;
                        break;
                }
            }
        }

        return $allowedValues;
    }

    /**
     * @return string
     */
    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * @param string $regex
     */
    public function setRegex(string $regex): void
    {
        $this->regex = $regex;
    }

    /**
     * @return array
     */
    public function getValueSwitch(): array
    {
        $flexformService = GeneralUtility::makeInstance(FlexFormService::class);
        $valueSwitch = $flexformService->convertFlexFormContentToArray($this->valueSwitch);

        $switch = [];
        if(is_array($valueSwitch) && array_key_exists('value_switch', $valueSwitch)) {
            foreach($valueSwitch['value_switch'] as $_switchVal) {
                $switch[] = $_switchVal['values'];
            }
        }

        return $switch;
    }

    /**
     * @param string $valueSwitch
     */
    public function setValueSwitch(string $valueSwitch): void
    {
        $this->valueSwitch = $valueSwitch;
    }

    /**
     * @return bool
     */
    public function hasValueSwitch(): bool
    {
        return ($this->valueSwitch != '');
    }

    /**
     * @return Datatype|null
     */
    public function getDatatype(): ?Datatype
    {
        return $this->datatype;
    }

    /**
     * @param Datatype|null $datatype
     */
    public function setDatatype(?Datatype $datatype): void
    {
        $this->datatype = $datatype;
    }

}
