<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

use Aix\Dataviewer\Tca;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class FileRelation extends Tca\AbstractField implements Tca\FieldInterface
{
    /**
     * Gets the sql create statement
     *
     * @return string
     */
    public function getSqlCreateStatement(): string
    {
        if ($this->getField()->getDatabaseType() != '') {
            return $this->getField()->getDatabaseType();
        }
        return 'varchar(255)';
    }

    /**
     * @return string
     */
    public function getVariableType(): string
    {
        if (!$this->getField()->getIsObjectStorage() && strpos($this->getField()->getFrontendType(), '\\') !== false) {
            return $this->getField()->getFrontendType();
        }

        return 'string';
    }

    /**
     * Gets built tca array
     *
     * @return array
     */
    public function getTca(): array
    {
        // allowed
        $allowed = $this->getField()->getConfig('allowed');
        if($allowedDefault = $this->getField()->getConfig('allowed_default')){
            if ($allowedDefault != '') {
                $allowed = $allowedDefault;
            }
        }

        $tca = [
            'exclude' => (int)$this->getField()->isExclude(),
            'label' => $this->getField()->getFrontendLabel(),
            'config' => [
                'type' => 'file',
                'allowed' => $allowed,
                'foreign_field' => 'uid_foreign',
                'foreign_label' => 'uid_local',
                'foreign_match_fields' => [
                    'fieldname' => $this->getField()->getVariableName(),
                ],
                'foreign_selector' => 'uid_local',
                'foreign_sortby' => 'sorting_foreign',
                'foreign_table' => 'sys_file_reference',
                'foreign_table_field' => 'tablenames',
            ],
            'appearance' => [
                'useSortable' => true,
                'headerThumbnail' => [
                    'field' => 'uid_local',
                    'width' => '45',
                    'height' => '45c',
                ],
                'showPossibleLocalizationRecords' => false,
                'showRemovedLocalizationRecords' => false,
                'showSynchronizationLink' => false,
                'showAllLocalizationLink' => false,
                'fileUploadAllowed' => (bool)$this->getField()->getConfig('fileUploadAllowed'),
                'fileByUrlAllowed' => (bool)$this->getField()->getConfig('fileByUrlAllowed'),
                'enabledControls' => [
                    'info' => true,
                    'new' => false,
                    'dragdrop' => true,
                    'sort' => true,
                    'hide' => true,
                    'delete' => true,
                    'localize' => true,
                ],
            ],
            'behaviour' => [
                'localizationMode' => 'select',
                'localizeChildrenAtParentLocalization' => true,
            ],
        ];

        return $this->mergeConfigurationToTca($tca);
    }

    /**
     * Merges the field configuration to the tca
     *
     * @param array $tca
     * @return array
     */
    public function mergeConfigurationToTca(array $tca): array
    {
        $tca = parent::mergeConfigurationToTca($tca);

        // minitems
        if ($minitems = $this->getField()->getConfig('minitems')) {
            $tca['config']['minitems'] = $minitems;
        }

        // maxitems
        if ($maxitems = $this->getField()->getConfig('maxitems')) {
            $tca['config']['maxitems'] = $maxitems;
        }

        // size
        if ($size = $this->getField()->getConfig('size')) {
            $tca['config']['size'] = $size;
        }

        return $tca;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        $value = parent::getDefaultValue();

        if (is_array($value)) {
            return implode(',', $value);
        }

        return '';
    }
}
