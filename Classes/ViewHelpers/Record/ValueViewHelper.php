<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Record;

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
class ValueViewHelper extends \MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper
{
	/**
	 * Fetch a record value
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @param string $field
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Value|null
	 */
	public function render(\MageDeveloper\Dataviewer\Domain\Model\Record $record, $field)
	{
		return $record->getValueByFieldId($field);
	}

}
