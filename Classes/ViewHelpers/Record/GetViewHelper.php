<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Record;

use Aix\Dataviewer\Domain\Model\AbstractRecordModel;
use Aix\Dataviewer\Domain\Repository\AbstractRepository;
use Aix\Dataviewer\ViewHelpers\AbstractViewHelper;


class GetViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments.
     * @return void
     * @api
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('uid', 'int', 'Record Uid', true, '');
        $this->registerArgument('datatype', '\\Aix\\Dataviewer\\Domain\\Model\\Datatype', 'Datatype of the according record', true, '');
        $this->registerArgument('onlyEnabled', 'bool', 'Only enabled', false, true);
        parent::initializeArguments();
    }

    /**
     * @return AbstractRecordModel|null
     */
    public function render(): ?AbstractRecordModel
    {
        $datatype = $this->arguments['datatype'];
        $repository = $datatype->getRepository();
        if($repository instanceof AbstractRepository) {
            return $repository->findByUid($this->arguments['uid'], $this->arguments['onlyEnabled'],false);
        }

        return null;
    }
}