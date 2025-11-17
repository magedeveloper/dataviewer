<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class FieldValue extends AbstractModel
{
    /**
     * Field Value Types
     *
     * @var string
     */
    const TYPE_FIXED_VALUE		= 10;
    const TYPE_DATABASE			= 20;
    const TYPE_TYPOSCRIPT		= 30;
    const TYPE_FIELDVALUES		= 40;

    /**
     * Selection of Value Type
     *
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate(validator="NotEmpty")
     */
    protected $type = 0;

    /**
     * Value Content
     *
     * @var string
     */
    protected $valueContent = '';

    /**
     * Field Content
     *
     * @var Field
     */
    protected $fieldContent = null;

    /**
     * Table Select Field
     *
     * @var string
     */
    protected $tableContent = '';

    /**
     * Column Select Field
     *
     * @var string
     */
    protected $columnName = '';

    /**
     * Where Clause for selection
     *
     * @var string
     */
    protected $whereClause = '';

    /**
     * Read Only
     *
     * @var bool
     */
    protected $isReadonly = false;

    /**
     * Is Default
     *
     * @var bool
     */
    protected $isDefault = false;

    /**
     * Pretends to be empty
     *
     * @var bool
     */
    protected $pretendsEmpty = false;

    /**
     * Value is passed to frontend
     *
     * @var bool
     */
    protected $passToFe = false;

    /**
     * Field of the record value
     *
     * @var \Aix\Dataviewer\Domain\Model\Field
     */
    protected $field = NULL;

    /**
     * Returns the type
     *
     * @return int $type
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param int $type
     * @return void
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * Returns the valueContent
     *
     * @return string $valueContent
     */
    public function getValueContent(): string
    {
        return $this->valueContent;
    }

    /**
     * Sets the valueContent
     *
     * @param string $valueContent
     * @return void
     */
    public function setValueContent(string $valueContent): void
    {
        $this->valueContent = $valueContent;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        $expl = GeneralUtility::trimExplode('|', $this->valueContent);
        if(count($expl) >= 2) {
            return $expl[1];
        }

        return reset($expl);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        $expl = GeneralUtility::trimExplode('|', $this->valueContent);
        if(count($expl) >= 2) {
            return (string)reset($expl);
        }

        return (string)reset($expl);
    }

    /**
     * Returns the field content
     *
     * @return Field|null $field
     */
    public function getFieldContent(): ?Field
    {
        return $this->fieldContent;
    }

    /**
     * Sets the field content
     *
     * @param Field $fieldContent
     * @return void
     */
    public function setFieldContent(Field $fieldContent): void
    {
        $this->fieldContent = $fieldContent;
    }

    /**
     * Returns the tableContent
     *
     * @return string $tableContent
     */
    public function getTableContent(): string
    {
        return $this->tableContent;
    }

    /**
     * Sets the tableContent
     *
     * @param string $tableContent
     * @return void
     */
    public function setTableContent(string $tableContent): void
    {
        $this->tableContent = $tableContent;
    }

    /**
     * Returns the columnName
     *
     * @return string $columnName
     */
    public function getColumnName(): string
    {
        return $this->columnName;
    }

    /**
     * Sets the columnName
     *
     * @param string $columnName
     * @return void
     */
    public function setColumnName(string $columnName): void
    {
        $this->columnName = $columnName;
    }

    /**
     * Returns the whereClause
     *
     * @return string $whereClause
     */
    public function getWhereClause(): string
    {
        if (strlen($this->whereClause)) {
            return " WHERE {$this->whereClause}";
        }

        return $this->whereClause;
    }

    /**
     * Sets the whereClause
     *
     * @param string $whereClause
     * @return void
     */
    public function setWhereClause(string $whereClause): void
    {
        $this->whereClause = $whereClause;
    }

    /**
     * Returns the bool state of readonly
     *
     * @return bool
     */
    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    /**
     * Returns the bool state of default
     *
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * Returns the isDefault
     *
     * @return bool isDefault
     */
    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * Sets the isDefault
     *
     * @param bool $isDefault
     * @return void
     */
    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    /**
     * Returns the isReadonly
     *
     * @return bool isReadonly
     */
    public function getIsReadonly(): bool
    {
        return $this->isReadonly;
    }

    /**
     * Sets the isReadonly
     *
     * @param bool $isReadonly
     * @return void
     */
    public function setIsReadonly(bool $isReadonly): void
    {
        $this->isReadonly = $isReadonly;
    }

    /**
     * Returns the field
     *
     * @return Field $field
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * Sets the field
     *
     * @param Field $field
     * @return void
     */
    public function setField(Field $field): void
    {
        $this->field = $field;
    }

    /**
     * Gets the pretends empty status
     *
     * @return bool
     */
    public function getPretendsEmpty(): bool
    {
        return $this->pretendsEmpty;
    }

    /**
     * Sets the pretends empty status
     *
     * @param bool $pretendsEmpty
     * @return void
     */
    public function setPretendsEmpty(bool $pretendsEmpty): void
    {
        $this->pretendsEmpty = $pretendsEmpty;
    }

    /**
     * Get the setting for
     * pass to frontend
     *
     * @return bool
     */
    public function getPassToFe(): bool
    {
        return $this->passToFe;
    }

    /**
     * Sets pass to frontend
     *
     * @param bool $passToFe
     * @return void
     */
    public function setPassToFe(bool $passToFe): void
    {
        $this->passToFe = $passToFe;
    }

}
