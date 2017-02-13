<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Utility\ArrayUtility;
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
class FileRelation extends AbstractFieldvalue implements FieldvalueInterface
{
	/**
	 * File Repository
	 *
	 * @var \TYPO3\CMS\Core\Resource\FileRepository
	 * @inject
	 */
	protected $fileRepository;

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
		$fileIds = GeneralUtility::trimExplode(",", $value, true);
		$fileRelationStringValue = "";

		foreach($fileIds as $_fileId)
		{
			if(!is_numeric($_fileId))
			{
				$fileValue = $this->_getDirectPost($_fileId);
				$parts = GeneralUtility::trimExplode("_", $fileValue);
				if (is_array($parts))
				{
					try {
						$fileReferenceId = end($parts);
						$fileReference   = $this->objectManager->get(\TYPO3\CMS\Core\Resource\FileReference::class,
							["uid_local" => $fileReferenceId]);
						$fileRelationStringValue .= $fileReference->getName() . ",";
					} catch (\Exception $e) { }
				}

			}
			else
			{
				if($_fileId)
				{
					try {
						/* @var \TYPO3\CMS\Core\Resource\File $file */
						$file = $this->fileRepository->findFileReferenceByUid($_fileId);

						if ($file instanceof \TYPO3\CMS\Core\Resource\FileInterface)
							$fileRelationStringValue .= $file->getName() . ",";

					} catch (\Exception $e) { }
				}
			}


		}

		return trim($fileRelationStringValue, ",");
	}

	/**
	 * Searches a file id in the posted form data
	 *
	 * @param string $fileId
	 * @return array
	 */
	protected function _getDirectPost($fileId)
	{
		$uc = GeneralUtility::_POST("uc");
		if(is_array($uc))
		{
			$found = reset(ArrayUtility::recursiveFindKey($fileId, $uc));
			if($found == 1)
			{
				// We need to fetch the id from the data of the form post
				$formPostData = GeneralUtility::_POST("data");
				if(isset($formPostData["sys_file_reference"][$fileId]) &&
					isset($formPostData["sys_file_reference"][$fileId]["uid_local"]))
				{
					return $formPostData["sys_file_reference"][$fileId]["uid_local"];
				}
			}

		}

		return;
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
	 * @return \TYPO3\CMS\Core\Resource\FileReference[]
	 */
	public function getFrontendValue()
	{
		$value 		= $this->getValue();
		$fileIds 	= GeneralUtility::trimExplode(",", $value, true);
		$files 		= [];

		foreach($fileIds as $_fileId)
		{
			/* @var \TYPO3\CMS\Core\Resource\File $file */
			if(is_numeric($_fileId))
			{
				try {
					$file = $this->fileRepository->findFileReferenceByUid($_fileId);
					if ($file instanceof \TYPO3\CMS\Core\Resource\FileInterface)
						$files[] = $file;
				} catch (\Exception $e) { }
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

		foreach($files as $_file)
		{
			$stringArray[] = $_file->getName();
		}

		return $stringArray;
	}
}
