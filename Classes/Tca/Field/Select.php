<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

use Aix\Dataviewer\Fluid\View\StandaloneView;

use Aix\Dataviewer\Tca;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Select extends Tca\AbstractField implements Tca\FieldInterface
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
                'type' => 'select',
                'renderType' => 'selectSingle',
                'multiple' => 0,
                'maxitems' => 1,
                'items' => $this->getItems(),
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

        // Foreign Table Configuration
        if ($this->getField()->getConfig('foreign')) {
            $tca['config']['items'] = [];
        }

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

        // multiple
        if ($multiple = $this->getField()->getConfig('multiple')) {
            $tca['config']['multiple'] = (bool)$multiple;
        }

        // selectedListStyle
        if ($selectedListStyle = $this->getField()->getConfig('selectedListStyle')) {
            $tca['config']['selectedListStyle'] = $selectedListStyle;
        }

        // itemsProcFunc
        if ($itemsProcFunc = $this->getField()->getConfig('itemsProcFunc')) {
            $tca['config']['itemsProcFunc'] = $itemsProcFunc;
        }

        /* foreign is enabled */
        if ($this->getField()->getConfig('foreign')) {

            //foreign_table
            if ($foreignTable = $this->getField()->getConfig('foreign_table')) {
                $tca['config']['foreign_table'] = $foreignTable;

                if (isset($GLOBALS['TCA'][$foreignTable]['ctrl']['languageField'])) {
                    $languageField = $GLOBALS['TCA'][$foreignTable]['ctrl']['languageField'];
                    $tca['config']['foreign_table_where'] = ($tca['config']['foreign_table_where']??'');
                    $tca['config']['foreign_table_where'] .= " AND {$foreignTable}.{$languageField} IN (-1, 0) ";
                }

                //foreign_table_where
                if ($foreignTableWhere = $this->getField()->getConfig('foreign_table_where')) {
                    // We need to render the foreign table where clause with fluid
                    /* @var \Aix\Dataviewer\Fluid\View\StandaloneView $standaloneView */
                    $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
                    $standaloneView->assign('field', $this->getField());
                    $standaloneView->assign('fieldtype', $this);
                    $standaloneView->setTemplateSource($foreignTableWhere);
                    $foreignTableWhereRendered = $standaloneView->render();
                    $tca['config']['foreign_table_where'] = ($tca['config']['foreign_table_where']??'');
                    $tca['config']['foreign_table_where'] .= " {$foreignTableWhereRendered} ";

                    if ($sortField = $this->getField()->getConfig('sort_field')) {
                      $sortOrder = ($this->getField()->getConfig('sort_order') == 'ASC') ? 'ASC' : 'DESC';
                      $tca['config']['foreign_table_where'] .= " ORDER BY {$foreignTable}.{$sortField}  {$sortOrder}";
                    }
                }
            }

            // Additional items from fieldvalues
            if ((bool)$this->getField()->getConfig('include_field_values_as_options') == true) {
                $tca['config']['items'] = $this->getItems();
            }

            // Suggest Wizard
            if ((bool)$this->getField()->getConfig('suggest_wizard') == true) {
                $tca['config']['enableMultiSelectFilterTextfield'] = true;
            }
        }

        return $tca;
    }

    /**
     * Gets the values for the field
     *
     * @return string
     */
    public function getDefaultValue()
    {
        $values = parent::getDefaultValue();
        if(is_array($values)) {
            return (string)reset($values);
        }
        return '';
    }
}
