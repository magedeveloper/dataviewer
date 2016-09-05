<?php
namespace MageDeveloper\Dataviewer\UserFunc;

use MageDeveloper\Dataviewer\Utility\DebugUtility;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use MageDeveloper\Dataviewer\Utility\IconUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class Database
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Field Repository
	 * 
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * FieldValue Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldValueRepository
	 * @inject
	 */
	protected $fieldValueRepository;

	/**
	 * Constructor
	 *
	 * @return Database
	 */
	public function __construct()
	{
		$this->objectManager 		= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->fieldRepository		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\FieldRepository::class);
		$this->fieldValueRepository	= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\FieldValueRepository::class);
	}
	
	/**
	 * Populate flexform tables
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateTablesAction(array &$config, &$parentObject)
	{
		$options = array();

		$label = Locale::translate("flexform.please_select", \MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration::EXTENSION_KEY);
		$options[] = array("label" => $label, 0 => $label, 1 => "");

		$res = $GLOBALS['TYPO3_DB']->sql_query("SHOW TABLES");

		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0)
		{
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
			{
				$tablename = end($row);
				$options[] = array("label" => $tablename, 0 => $tablename, 1 => $tablename);
			}
		}

		$config["items"] = $options;

		return $config;
	}

	/**
	 * Populate flexform tables
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateColumnsAction(array &$config, &$parentObject)
	{
		$tablename = reset($config["row"]["table_content"]);

		$options = array();

		if ($tablename)
		{
			$res = $GLOBALS['TYPO3_DB']->sql_query("SHOW COLUMNS FROM {$tablename}");

			if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0)
			{
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
				{
					$field = $row["Field"];
					$options[] = array("label" => $field, 0 => $field, 1 => $field);
				}
			}

		}

		$config["items"] = $options;

		return $config;
	}

	/**
	 * Displays the result of the selected table/column
	 *
	 * @param array $config
	 * @param array $parentObject
	 * @return string
	 */
	public function displayTableContentResult(array &$config, &$parentObject)
	{
		$this->populateColumnsAction($config, $parentObject);
		unset($config["items"][0]);

		$html = "";

		$options = array();

		if (isset($config["row"]))
		{
			$fieldValueUid = $config["row"]["uid"];
			$fieldValue = $this->fieldValueRepository->findByUid($fieldValueUid);

			if ($fieldValue instanceof \MageDeveloper\Dataviewer\Domain\Model\FieldValue)
			{
				if (($fieldValue->getType() == \MageDeveloper\Dataviewer\Domain\Model\FieldValue::TYPE_DATABASE)
					&&
					$fieldValue->getTableContent() &&
					$fieldValue->getColumnName()
				)
				{
					try {
						$result = $this->fieldRepository->findEntriesForFieldValue($fieldValue);

						$html .= "<h4>".Locale::translate("items", array(count($result)))."</h4>";
						$html .= DebugUtility::debugVariable($result);
					} catch ( \Exception $e) {
						$statement = "SELECT * FROM {$fieldValue->getTableContent()} {$fieldValue->getWhereClause()}";
						$html = "<div class=\"alert alert-danger\" role=\"alert\">{$e->getMessage()}<br />Statement: {$statement}</div>";
					}

				}
			}

		}

		return $html;
	}
}
