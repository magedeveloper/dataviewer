<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Form\Value;

interface ValueInterface
{
    /**
     * Gets the generated value
     * @return mixed
     */
    public function getValue();
}