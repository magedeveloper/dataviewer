<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Content;

use MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper;

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
class RecordViewHelper extends AbstractViewHelper
{
	/**
	 * Configuration Manager
	 * 
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * Fetches a content record from tt_content by a
	 * given uid
	 *
	 * @param int $uid Uid of the tt_content record
	 * @return string
	 */
	public function render($uid)
	{
		$cObj = $this->configurationManager->getContentObject();

		$conf = [
			"tables"		=> "tt_content",
			"source"		=> (int)$uid,
			"dontCheckPid"	=> 1,
		];

		return $cObj->cObjGetSingle('RECORDS', $conf);
	}
}
