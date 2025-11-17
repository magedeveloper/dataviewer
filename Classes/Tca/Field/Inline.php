<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

use Aix\Dataviewer\Fluid\View\StandaloneView;
use Aix\Dataviewer\Tca;

class Inline extends Tca\AbstractField implements Tca\FieldInterface
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
        return 'varchar(255) DEFAULT \'\' NOT NULL';
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
        $tca = [
            'exclude' => (int)$this->getField()->isExclude(),
            'label' => $this->getField()->getFrontendLabel(),
            'config' => [
                'type' => 'inline',
                'foreign_table' => $this->getField()->getConfig('foreign_table'),
                'appearance' => [
                    'collapseAll' => 1,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 0,
                    'showPossibleLocalizationRecords' => 0,
                    'useSortable' => 1,
                    'showAllLocalizationLink' => 1,
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'fieldWizard' => [
                    'localizationStateSelector' => [
                        'disabled' => false,
                    ],
                ],
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

        // maxitems
        if ($maxitems = $this->getField()->getConfig('maxitems')) {
            $tca['config']['maxitems'] = $maxitems;
        }

        // minitems
        if ($minitems = $this->getField()->getConfig('minitems')) {
            $tca['config']['minitems'] = $minitems;
        }

        // size
        if ($size = $this->getField()->getConfig('size')) {
            $tca['config']['size'] = $size;
        }

        // foreign_table
        $foreignTable = $this->getField()->getConfig('foreign_table');

        if (isset($GLOBALS['TCA'][$foreignTable]['ctrl']['languageField'])) {
            $languageField = $GLOBALS['TCA'][$foreignTable]['ctrl']['languageField'];
            if(!array_key_exists('foreign_table_where', $tca['config'])) {
                $tca['config']['foreign_table_where'] = '';
            }
            $tca['config']['foreign_table_where'] .= " AND {$foreignTable}.{$languageField} IN (-1, 0) ";
        }

        // foreign_table_where
        if ($foreignTableWhere = $this->getField()->getConfig('foreign_table_where')) {
            // We need to render the foreign table where clause with fluid
            /* @var \Aix\Dataviewer\Fluid\View\StandaloneView $standaloneView */
            $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
            $standaloneView->assign('field', $this->getField());
            $standaloneView->assign('fieldtype', $this);
            $standaloneView->setTemplateSource($foreignTableWhere);
            $foreignTableWhereRendered = $standaloneView->render();

            $tca['config']['foreign_table_where'] .= " {$foreignTableWhereRendered} ";

            if ($sortField = $this->getField()->getConfig('sort_field')) {
                $sortOrder = ($this->getField()->getConfig('sort_order') == 'ASC')?'ASC':'DESC';
                $tca['config']['foreign_table_where'] .= " ORDER BY {$foreignTable}.{$sortField}  {$sortOrder}";
            }
        }

        // overrideChildTca
        if ($foreignRecordDefaults = $this->getField()->getForeignRecordDefaults()) {
            $tca['config']['overrideChildTca'] = $foreignRecordDefaults;
        }

        // foreign_sortby
        if ($foreignSortby = $this->getField()->getConfig('foreign_sortby')) {
            $tca['config']['foreign_sortby'] = $foreignSortby;
        }

        // foreign_field
        if ($foreignField = $this->getField()->getConfig('foreign_field')) {
            $tca['config']['foreign_field'] = $foreignField;
        }

        return $tca;
    }

    /**
     * Gets the values for the field
     * @return string
     */
    public function getDefaultValue()
    {
        $values = parent::getDefaultValue();
        if(!is_null($values)) {
            return implode(',',array_column($values,0));
        }
        return '';
    }
}
