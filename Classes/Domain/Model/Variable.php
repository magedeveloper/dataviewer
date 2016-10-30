<?php
namespace MageDeveloper\Dataviewer\Domain\Model;

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
class Variable extends AbstractModel
{
	/**
	 * Variable Types
	 * 
	 * @var int
	 */
	const VARIABLE_TYPE_FIXED 			= 0;
	const VARIBALE_TYPE_TYPOSCRIPT 		= 1;
	const VARIABLE_TYPE_TYPOSCRIPT_VAR	= 2;
	const VARIABLE_TYPE_GET				= 3;
	const VARIABLE_TYPE_POST			= 4;
	const VARIABLE_TYPE_RECORD			= 5;
	const VARIABLE_TYPE_RECORD_FIELD	= 6;
	const VARIABLE_TYPE_DATABASE		= 7;
	const VARIABLE_TYPE_FRONTEND_USER	= 8;
	const VARIABLE_TYPE_SERVER			= 9;
	const VARIABLE_TYPE_DYNAMIC_RECORD	= 10;

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
	 * Variable Value
	 * 
	 * @var string
	 */
	protected $variableValue;

	/**
	 * Record
	 * 
	 * @var \MageDeveloper\Dataviewer\Domain\Model\Record
	 */
	protected $record = NULL;

	/**
	 * Field
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Model\Field
	 */
	protected $field = NULL;

	/**
	 * Table Select Field
	 *
	 * @var string
	 */
	protected $tableContent = "";

	/**
	 * Column Name Field
	 *
	 * @var string
	 */
	protected $columnName = "";

	/**
	 * Where Clause for selection
	 *
	 * @var string
	 */
	protected $whereClause ="";

	/**
	 * SERVER Environment Value
	 *
	 * @var string
	 */
	protected $server ="";

	/**
	 * Gets the type
	 * 
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Sets the type
	 * 
	 * @param int $type
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
	public function getVariableName()
	{
		return $this->variableName;
	}

	/**
	 * Sets the variable name
	 * 
	 * @param string $variableName
	 * @return void
	 */
	public function setVariableName($variableName)
	{
		$this->variableName = $variableName;
	}

	/**
	 * Gets the variable value
	 * 
	 * @return string
	 */
	public function getVariableValue()
	{
		return $this->variableValue;
	}

	/**
	 * Sets the variable value
	 * 
	 * @param string $variableValue
	 * @return void
	 */
	public function setVariableValue($variableValue)
	{
		$this->variableValue = $variableValue;
	}

	/**
	 * Returns the record
	 *
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 */
	public function getRecord()
	{
		return $this->record;
	}

	/**
	 * Sets the record
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @return void
	 */
	public function setRecord(\MageDeveloper\Dataviewer\Domain\Model\Record $record)
	{
		$this->record = $record;
	}

	/**
	 * Returns the field
	 *
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * Sets the field
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return void
	 */
	public function setField(\MageDeveloper\Dataviewer\Domain\Model\Field $field)
	{
		$this->field = $field;
	}

	/**
	 * Returns the tableContent
	 *
	 * @return string $tableContent
	 */
	public function getTableContent()
	{
		return $this->tableContent;
	}

	/**
	 * Sets the tableContent
	 *
	 * @param string $tableContent
	 * @return void
	 */
	public function setTableContent($tableContent)
	{
		$this->tableContent = $tableContent;
	}

	/**
	 * Returns the columnName
	 *
	 * @return string $columnName
	 */
	public function getColumnName()
	{
		return $this->columnName;
	}

	/**
	 * Sets the columnName
	 *
	 * @param string $columnName
	 * @return void
	 */
	public function setColumnName($columnName)
	{
		$this->columnName = $columnName;
	}

	/**
	 * Returns the whereClause
	 *
	 * @return string $whereClause
	 */
	public function getWhereClause()
	{
		return $this->whereClause;
	}

	/**
	 * Sets the whereClause
	 *
	 * @param string $whereClause
	 * @return void
	 */
	public function setWhereClause($whereClause)
	{
		$this->whereClause = $whereClause;
	}

	/**
	 * Gets the name for the server
	 * environment variable name
	 * 
	 * @return string
	 */
	public function getServer()
	{
		return $this->server;
	}

	/**
	 * Sets a server environment variable name
	 * 
	 * @param string $server
	 * @return void
	 */
	public function setServer($server)
	{
		$this->server = $server;
	}
}
