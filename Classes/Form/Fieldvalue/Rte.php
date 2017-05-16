<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Html\RteHtmlParser;
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
class Rte extends AbstractFieldvalue implements FieldvalueInterface
{
	/**
	 * Parses RTE content
	 * 
	 * @param string $value
	 * @return string
	 */
	public function parseRTE($value)
	{
		// Initialize transformation:
		/* @var RteHtmlParser $parseHTML */
		$parseHTML = GeneralUtility::makeInstance(RteHtmlParser::class);
		$parseHTML->init("tt_content" . ':' . "bodytext"); // We imitate a tt_content bodytext field
		
		// Perform transformation from db -> rte:
		return $parseHTML->TS_transform_rte($value);
	}

	/**
	 * Gets the optimized value for the field
	 *
	 * @return string
	 */
	public function getValueContent()
	{
		$value = $this->getValue();
		return $this->parseRTE($value);
	}

	/**
	 * Gets the optimized search string for the field
	 *
	 * @return string
	 */
	public function getSearch()
	{
		$html = $this->getValue();
		$html = html_entity_decode($html);
		$html = trim($html);
		return strip_tags($html);
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
	 * @return mixed
	 */
	public function getFrontendValue()
	{
		$value = $this->getValue();
		return $value;
		//return $this->parseRTE($value);
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
        return [$this->getSearch()];
    }
}
