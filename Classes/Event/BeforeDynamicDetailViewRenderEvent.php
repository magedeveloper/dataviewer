<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Event;

use Aix\Dataviewer\Controller\RecordController;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;

final class BeforeDynamicDetailViewRenderEvent
{
    public function __construct(
        protected readonly RecordController $recordController,
        protected readonly RequestInterface $request,
        protected array $settings,
        protected array $variables
    ) {
    }

    /**
     * @return RecordController
     */
    public function getRecordController(): RecordController
    {
        return $this->recordController;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     * @return void
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @param array $variables
     * @return void
     */
    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

}

