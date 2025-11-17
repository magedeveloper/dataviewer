<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Datatype;

use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Domain\Repository\DatatypeRepository;
use Aix\Dataviewer\ViewHelpers\AbstractViewHelper;

class GetViewHelper extends AbstractViewHelper
{
    /**
     * @var DatatypeRepository
     */
    protected $datatypeRepository;

    /**
     * @param DatatypeRepository $datatypeRepository
     */
    public function injectDatatypeRepository(DatatypeRepository $datatypeRepository)
    {
        $this->datatypeRepository = $datatypeRepository;
    }

    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('uid', 'int', 'Datatype Uid', true, '');
        $this->registerArgument('onlyEnabled', 'bool', 'Only enabled', false, true);
        parent::initializeArguments();
    }

    /**
     * @return Datatype|null
     */
    public function render(): ?Datatype
    {
        $datatype = $this->datatypeRepository->findByUid($this->arguments['uid'], $this->arguments['onlyEnabled']);
        if ($datatype instanceof Datatype) {
            return $datatype;
        }

        return null;
    }
}