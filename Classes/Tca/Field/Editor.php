<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

use Aix\Dataviewer\Tca;

class Editor extends Textarea implements Tca\FieldInterface
{
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
                'type' => 'text',
                'renderType' => 't3editor', // TYPO3 13: codeEditor
                'enableRichtext' => true,
                'format' => $this->getField()->getConfig('format')?:'mixed',
                'rows' => (int)$this->getField()->getConfig('rows')?:10,
            ],
        ];

        return $this->mergeConfigurationToTca($tca);
    }
}
