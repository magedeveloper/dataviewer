<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

use Aix\Dataviewer\Tca;

class Group extends Tca\AbstractField implements Tca\FieldInterface
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
        $tca = [
            'exclude' => (int)$this->getField()->isExclude(),
            'label' => $this->getField()->getFrontendLabel(),
            'config' => [
                'type' => 'group',
                'internal_type' => $this->getField()->getConfig('internal_type'),
                'allowed' => $this->getField()->getConfig('allowed'),
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

        // show_thumbs
        $tca['config']['show_thumbs'] = (int)$this->getField()->getConfig('show_thumbs');

        // minitems
        if ($minitems = $this->getField()->getConfig('minitems')) {
            $tca['config']['minitems'] = $minitems;
        }

        // maxitems
        if ($maxitems = $this->getField()->getConfig('maxitems')) {
            $tca['config']['maxitems'] = $maxitems;
        }

        //size
        if ($size = $this->getField()->getConfig('size')) {
            $tca['config']['size'] = $size;
        }

        // multiple
        if ($multiple = $this->getField()->getConfig('multiple')) {
            $tca['config']['multiple'] = (bool)$multiple;
        }

        // disallowed
        if ($disallowed = $this->getField()->getConfig('disallowed')) {
            $tca['config']['disallowed'] = $disallowed;
        }

        // max_size
        if ($max_size = $this->getField()->getConfig('max_size')) {
            $tca['config']['max_size'] = $max_size;
        }

        // uploadfolder
        if ($uploadfolder = $this->getField()->getConfig('uploadfolder')) {
            if ($this->getField()->getConfig('internal_type') == 'file') {
                // We need to prepare the uploadfolder variable for matching the correct value
                // The value has to begin without a '/' and has to end with a '/'
                $uploadfolder = rtrim($uploadfolder, '/');
                $uploadfolder = trim($uploadfolder, '/');
                $uploadfolder .= '/';

                $tca['config']['uploadfolder'] = $uploadfolder;
            }
        }

        // hideMoveIcons
        if ($hideMoveIcons = $this->getField()->getConfig('hideMoveIcons')) {
            $tca['config']['hideMoveIcons'] = (bool)$hideMoveIcons;
        }

        // foreign_table
        if ($foreignTable = $this->getField()->getConfig('foreign_table')) {
            $tca['config']['foreign_table'] = $foreignTable;
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
