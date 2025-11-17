<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Domain\Model;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class AbstractRecordModel extends AbstractModel
{
    /**
     * Record Title
     * @var string
     */
    protected $title = '';

    /**
     * Datatype
     * @var Datatype
     */
    protected $datatype;

    /**
     * Path Segment
     * @var string
     */
    protected $pathSegment = '';

    /**
     * @var int
     */
    protected $sysLanguageUid;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return Datatype
     */
    public function getDatatype(): Datatype
    {
        return $this->datatype;
    }

    /**
     * @param Datatype $datatype
     */
    public function setDatatype(Datatype $datatype): void
    {
        $this->datatype = $datatype;
    }

    /**
     * @return string
     */
    public function getPathSegment(): string
    {
        return $this->pathSegment;
    }

    /**
     * @param string $pathSegment
     */
    public function setPathSegment(string $pathSegment): void
    {
        $this->pathSegment = $pathSegment;
    }

    /**
     * @return int
     */
    public function getSysLanguageUid(): int
    {
        return $this->sysLanguageUid;
    }

    /**
     * @param int $sysLanguageUid
     * @return void
     */
    public function setSysLanguageUid($sysLanguageUid): void
    {
        $this->sysLanguageUid = $sysLanguageUid;
    }

    /**
     * @param string $field
     * @return ObjectStorage
     */
    public function getFieldSorted(string $field): ObjectStorage
    {
        $row        = BackendUtility::getRecord($this->getDatatype()->getTablename(), $this->getUid(), $field);
        $value      = $row[$field];
        $ids        = GeneralUtility::intExplode(',', $value);
        $fieldValue = $this->_getProperty($field);

        // UID => POSITION
        $recordIdsInPlace = array_flip(array_column($fieldValue->toArray(), 'uid'));

        $objStorage = new ObjectStorage();
        foreach ($ids as $uid) {
            $position = $recordIdsInPlace[$uid];
            $obj      = $fieldValue->toArray()[$position];
            if (!is_null($obj)) {
                $objStorage->attach($obj);
            }
        }

        return $objStorage;
    }
}