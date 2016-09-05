<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Utility\CheckboxUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\UnknownClassException;

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
class Datatype extends Inline
{
	/**
	 * Gets the model class for the according
	 * inline elements
	 *
	 * @return string
	 */
	public function getModelClass()
	{
		return \MageDeveloper\Dataviewer\Domain\Model\Record::class;
	}

	/**
	 * Gets the foreign table for the
	 * according inline elements
	 *
	 * @return string
	 */
	public function getForeignTable()
	{
		return "tx_dataviewer_domain_model_record";
	}
}
