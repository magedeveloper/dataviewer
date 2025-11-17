<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

use TYPO3\CMS\Core\Resource\File;

class Image extends FileRelation
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
        return 'int(11) unsigned DEFAULT \'0\' NOT NULL';
    }

    /**
     * Gets built tca array
     *
     * @return array
     */
    public function getTca(): array
    {
        $tca = parent::getTca();
        $tca['config']['allowed'] = $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'];
        return $this->mergeConfigurationToTca($tca);
    }
}
