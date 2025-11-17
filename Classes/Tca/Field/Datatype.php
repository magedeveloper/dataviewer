<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

use Aix\Dataviewer\Domain\Model\Datatype as DatatypeModel;
use TYPO3\CMS\Extbase\Property\Exception\InvalidDataTypeException;

class Datatype extends Inline
{
    /**
     * Gets the selected datatype model
     *
     * @return DatatypeModel
     */
    public function getSelectedDatatype(): DatatypeModel
    {
        $datatypeUid = (int)$this->getField()->getConfig('datatype');
        $datatype = $this->datatypeRepository->findByUid($datatypeUid, false);
        if ($datatype instanceof DatatypeModel) {
            return $datatype;
        }

        throw new InvalidDataTypeException('Please select a valid datatype!');
    }

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
     * @throws InvalidDataTypeException
     */
    public function getVariableType(): string
    {
        $datatype = $this->getSelectedDatatype();
        return '\\'.$datatype->getFullyQualifiedClassName();
    }

    /**
     * Gets built tca array
     *
     * @return array
     */
    public function getTca(): array
    {
        $datatype = $this->getSelectedDatatype();
        $tca = parent::getTca();

        $tca['config']['foreign_table'] = $datatype->getTablename();

        return $this->mergeConfigurationToTca($tca);
    }
}
