<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Utility\CheckboxUtility;
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
class File extends AbstractFieldvalue implements FieldvalueInterface
{
	/**
	 * Resource Repository
	 * 
	 * @var \TYPO3\CMS\Core\Resource\ResourceFactory
	 * @inject
	 */
	protected $resourceFactory;

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
		return basename($value);
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
	 * @return \TYPO3\CMS\Core\Resource\File[]
	 */
	public function getFrontendValue()
	{
		$value = $this->getValue();
		$values = GeneralUtility::trimExplode(",", $value, true);
		
		$files = [];
		foreach($values as $_file)
		{
			$fullFilePath = GeneralUtility::getFileAbsFileName($_file);

			if(file_exists($fullFilePath))
			{
				$file = $this->resourceFactory->retrieveFileOrFolderObject($fullFilePath);
				$files[] = $file;
			}
		}
			
		return $files;
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
        $files = $this->getFrontendValue();
        $stringArray = [];

        foreach ($files as $_file)
        {
            $stringArray[] = $_file->getName();
        }

        return $stringArray;
    }
}
