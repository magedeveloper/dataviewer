<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Backend;

use Aix\Dataviewer\Domain\Model\AbstractRecordModel;
use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Fluid\View\StandaloneView;
use Aix\Dataviewer\Utility\LocalizationUtility;
use Aix\Dataviewer\Utility\UrlUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class FlashMessageService
{
    /**
     * @var string[]
     */
    protected $allowedRoutes = [
        '/module/web/layout',       // Page Module
        '/module/web/list',         // List Module
        '/module/system/config',    // Configuration Module
    ];

    /**
     * Sends a message with the given parameters.
     *
     * @param string $message The message to be sent.
     * @param string $title The title of the message. (optional)
     * @param mixed $severity The severity level of the message. (optional)
     * @param bool $checkRequest Check the request environment
     * @return void
     */
    public function addFlashMessage(string $message, string $title = '', $severity = ContextualFeedbackSeverity::OK, ?ServerRequestInterface $checkRequest = null): void
    {
        $sendMessage = true;
        if($checkRequest instanceof ServerRequestInterface) {
            $sendMessage = false;
            if (ApplicationType::fromRequest($checkRequest)->isBackend() && in_array($checkRequest->getAttribute('route')->getPath(), $this->allowedRoutes)) {
                // Request is valid and flash message can be sent
                $sendMessage = true;
            }
        }

        if($sendMessage === true) {
            $message = GeneralUtility::makeInstance(FlashMessage::class,
                $message,
                $title,
                $severity,
                true
            );

            $flashMessageService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Messaging\FlashMessageService::class);
            $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
            $messageQueue->addMessage($message);
        }

    }
}