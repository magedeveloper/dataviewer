<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca;

interface FieldInterface
{
    /**
     * Gets the sql create statement
     *
     * @return string
     */
    public function getSqlCreateStatement(): string;

    /**
     * Gets the variable type of the field
     * for creating the frontend class
     *
     * e.g. bool, int, array
     *
     * @return string
     */
    public function getVariableType(): string;

    /**
     * Gets the TCA for the field
     *
     * @return array
     */
    public function getTca(): array;
}
