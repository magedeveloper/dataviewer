<?php
namespace MageDeveloper\Dataviewer\CmsLayout;

use \MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;

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
class Record extends AbstractCmsLayout
{
	/**
	 * The correct list type for this layout view
	 * 
	 * @var string
	 */
	protected $listType = "dataviewer_record";
}
