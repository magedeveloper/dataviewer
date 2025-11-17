<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

use Aix\Dataviewer\Tca as TypoScriptConfigurationArray;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Tca extends TypoScriptConfigurationArray\AbstractField implements TypoScriptConfigurationArray\FieldInterface
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
        $xml = $this->getField()->getConfig('tca');
        $tcaConfig = GeneralUtility::xml2array($xml);

        $tca = [
            'exclude' => (int)$this->getField()->isExclude(),
            'label' => $this->getField()->getFrontendLabel(),
            'config' => $tcaConfig,
        ];

        return $tca;
    }
}
