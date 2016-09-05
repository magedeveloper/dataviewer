<?php
namespace MageDeveloper\Dataviewer\Form\FormDataProvider;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaInlineFile extends \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline
{
	/**
	 * Use RelationHandler to resolve connected uids.
	 *
	 * @param array $parentConfig TCA config section of parent
	 * @param string $parentTableName Name of parent table
	 * @param string $parentUid Uid of parent record
	 * @param string $parentFieldValue Database value of parent record of this inline field
	 * @return array Array with connected uids
	 * @todo: Cover with unit tests
	 */
	protected function resolveConnectedRecordUids(array $parentConfig, $parentTableName, $parentUid, $parentFieldValue)
	{
		$resolvedForeignRecordUids = parent::resolveConnectedRecordUids($parentConfig, $parentTableName, $parentUid, $parentFieldValue);
		
		if(strlen($parentFieldValue) > 0)
			return GeneralUtility::trimExplode(",", $parentFieldValue);
		
		return $resolvedForeignRecordUids;
	}
}
