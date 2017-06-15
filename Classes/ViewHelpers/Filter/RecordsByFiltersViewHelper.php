<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Filter;

use MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Extbase\Object\InvalidObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * This ViewHelpers filters given records by a field value condition
 * Usage:
 *
 * {dv:filter.records(records:movies,field:'length',value:'120',condition:'gte')}
 */

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
class RecordsByFiltersViewHelper extends AbstractFilterViewHelper
{
	/**
	 * Gets records by given conditions
	 *
	 * @param array $filters Conditions
	 * @param string $sortField The Sort Field
	 * @param string $sortOrder Sort Order
	 * @param string $limit Limit
	 * @param array $storagePids Storage Pids
	 * @throws \TYPO3\CMS\Extbase\Object\InvalidObjectException
	 * @return array
	 */
	public function render(array $filters, $sortField = "title", $sortOrder = QueryInterface::ORDER_ASCENDING, $limit = null, array $storagePids = [])
	{
		$validRecords = $this->recordRepository->findByAdvancedConditions($filters, $sortField, $sortOrder, $limit, $storagePids);
		return $validRecords;
	}
}
