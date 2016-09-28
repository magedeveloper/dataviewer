<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use TYPO3\CMS\Backend\Utility\BackendUtility;
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
class Link extends AbstractFieldvalue implements FieldvalueInterface
{
	/**
	 * Content Object Renderer
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 * @inject
	 */
	protected $contentObjectRenderer;

	/**
	 * Gets the optimized value for the field
	 *
	 * @return string
	 */
	public function getValueContent()
	{
		return $this->getValue();
	}

	/**
	 * Gets the optimized search string for the field
	 *
	 * @return string
	 */
	public function getSearch()
	{
		$value = $this->getValue();
		$parts = GeneralUtility::trimExplode(" ", $value, true);
		$urlOrId = reset($parts);
		
		if(is_numeric($urlOrId))
		{
			$searchFields = $GLOBALS["TCA"]["pages"]["ctrl"]["searchFields"];
		
			// Internal Uid of a page
			$valueArr = BackendUtility::getRecord("pages", $urlOrId, $searchFields);
			return implode(" ", $valueArr);
		}
		
		return $urlOrId;
	}

	/**
	 * Gets the final frontend value, that is
	 * pushed in {record.field.value}
	 *
	 * This or these values are the most different
	 * part of the whole output, so if you handle
	 * this, you need to have some knowledge,
	 * what value is returned.
	 *
	 * @return string
	 */
	public function getFrontendValue()
	{
		$value = $this->getValue();
		
		$conf = [
			"parameter" 		=> $value,
			"useCacheHash" 		=> true,
			"returnLast" 		=> "url",
			"forceAbsoluteUrl" 	=> true,
		];

		$parts = explode("- ", $value);
		if (isset($parts[1]))
			$link = $parts[1];	// If we have a title
		else
		{
			$parts = explode(" ", $parts[0]);
			if (isset($parts[0]))
				$link = $parts[0]; // If we have a link url
		}

		$typolink = $link;
		if(TYPO3_MODE == "FE")
			$typolink = $this->contentObjectRenderer->typoLink($link, $conf);
		
		return $typolink;
	}

    /**
     * Gets the value or values as a plain string-array for
     * usage in different possitions to show
     * and use it when needed as a string
     *
     * @return array
     */
    public function getValueArray()
    {
        return [$this->getFrontendValue()];
    }
}
