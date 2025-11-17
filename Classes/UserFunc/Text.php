<?php
namespace Aix\Dataviewer\UserFunc;

use Aix\Dataviewer\Domain\Model\Variable;
use Aix\Dataviewer\Fluid\View\StandaloneView;
use Aix\Dataviewer\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Text
{
    /**
     * Variable Repository
     *
     * @var \Aix\Dataviewer\Domain\Repository\VariableRepository
     */
    protected $variableRepository;

    /**
     * Plugin Settings Service
     *
     * @var \Aix\Dataviewer\Service\Settings\Plugin\PluginSettingsService
     */
    protected $pluginSettingsService;

    /**
     * FlexForm Service
     *
     * @var \Aix\Dataviewer\Service\FlexForm\FlexFormService
     */
    protected $flexFormService;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->variableRepository		= GeneralUtility::makeInstance(\Aix\Dataviewer\Domain\Repository\VariableRepository::class);
        $this->pluginSettingsService    = GeneralUtility::makeInstance(\Aix\Dataviewer\Service\Settings\Plugin\PluginSettingsService::class);
        $this->flexformService          = GeneralUtility::makeInstance(\Aix\Dataviewer\Service\FlexForm\FlexFormService::class);
    }

    /**
     * Just display nothing :)
     *
     * @param array $config Configuration Array
     * @param array $parentObject Parent Object
     * @return array
     */
    public function displayNothing(array &$config, &$parentObject)
    {
        return "";
    }

    /**
     * Just display nothing :)
     *
     * @param array $config Configuration Array
     * @param array $parentObject Parent Object
     * @return array
     */
    public function displayNoConfigurationMessage(array &$config, &$parentObject)
    {
        $message = LocalizationUtility::translate("message.this_field_has_no_configuration");
        return "<div class=\"message message-alert\">{$message}</div>";
    }

    /**
     * Display a simple error text in backend
     *
     * @param array $config Configuration Array
     * @param array $parentObject Parent Object
     * @return array
     */
    public function displayMessage(array &$config, &$parentObject)
    {
        $message = "Error @ {$config["itemFormElName"]}";

        $parameters = $config["parameters"];
        if (isset($parameters["message"]))
            $message = $parameters["message"];

        $severity = "danger";
        if (isset($parameters["severity"]))
            $severity = $parameters["severity"];

        return "<div class=\"alert alert-{$severity}\">{$message}</div>";
    }

}
