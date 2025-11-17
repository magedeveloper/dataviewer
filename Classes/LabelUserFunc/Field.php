<?php
namespace Aix\Dataviewer\LabelUserFunc;

use Aix\Dataviewer\Utility\LocalizationUtility as Locale;
use Aix\Dataviewer\Configuration\ExtensionConfiguration as Config;
use TYPO3\CMS\Backend\Utility\BackendUtility;


class Field
{
	/**
	 * Field Repository
	 *
	 * @var \Aix\Dataviewer\Domain\Repository\FieldRepository
	 */
	protected $fieldRepository;

	/**
	 * UserFunc for Field Label
	 *
	 * @param array $pObj Object Information
	 * @return void
	 */
	public function displayLabel(&$pObj)
	{
	    $record = BackendUtility::getRecordWSOL($pObj['table'], $pObj['row']['uid']);
        if(is_array($record)) {
            $type = (is_array($record) && is_array($record['type'])) ? reset($record['type']) : $record['type'];
            $label = ($record['frontend_label']) ? $record['frontend_label'] : "[" . Locale::translate("no_label") . "]";
            $pObj['title'] = "[{$record['pid']}] " . strtoupper($type) . ": " . $label . " {" . $record['variable_name'] . "}";
        }
	}

}
