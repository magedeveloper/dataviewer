<?php
namespace MageDeveloper\Dataviewer\Hooks;

use MageDeveloper\Dataviewer\Utility\LocalizationUtility;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * MageDeveloper Dataviewer Extension
 * -----------------------------------
 *
 * @category    TYPO3 Extension
 * @package     MageDeveloper\Dataviewer
 * @author		Bastian Zagar
 * @copyright   Magento Developers / magedeveloper.de <kontakt@magedeveloper.de>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DocHeaderButtons 
{
	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Datatype Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository
	 * @inject
	 */
	protected $datatypeRepository;

	/**
	 * Record Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
	 * @inject
	 */
	protected $recordRepository;

	/**
	 * Icon Factory
	 * 
	 * @var \TYPO3\CMS\Core\Imaging\IconFactory
	 * @inject
	 */
	protected $iconFactory;

	/**
	 * Constructor
	 *
	 * @return DocHeaderButtons
	 */
	public function __construct()
	{
		$this->objectManager      = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->datatypeRepository = $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository::class);
		$this->recordRepository   = $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
		
		$this->iconFactory		  = $this->objectManager->get(\TYPO3\CMS\Core\Imaging\IconFactory::class);
	}

	/**
	 * Get buttons
	 *
	 * @param array $params
	 * @param ButtonBar $buttonBar
	 * @return array
	 */
	public function getButtons(array $params, ButtonBar $buttonBar)
	{
		$buttons 				= $params['buttons'];
		$currentPageId 			= $this->_resolveCurrentPageId();

		$allowedModules = [
			"web_layout",
			"web_list",
			"web_info",
			"web_func",
			//"web_ViewpageView",
		];

		if (!in_array(GeneralUtility::_GET("M"), $allowedModules) || !$currentPageId || $currentPageId == 0)
			return $buttons;
		
		$html 		= "{namespace core=TYPO3\\CMS\\Core\\ViewHelpers}
					   {namespace dv=MageDeveloper\\Dataviewer\\ViewHelpers}";
					   
					   
		$htmlButton = "<a href=\"{dv:backend.newLink(pid:'{currentPageId}',table:'tx_dataviewer_domain_model_record',datatype:datatype.uid)}\" title=\"{title}\"><core:icon identifier=\"extensions-dataviewer-{icon}\" size=\"small\" /><core:icon identifier=\"actions-add\" size=\"small\" /></a>";
		/* @var \MageDeveloper\Dataviewer\Fluid\View\StandaloneView $view */
		$view 		= $this->objectManager->get(\MageDeveloper\Dataviewer\Fluid\View\StandaloneView::class);

		$datatypes = $this->datatypeRepository->findAllOnPid($currentPageId);
		if($datatypes)
		{
			foreach($datatypes as $_datatype)
			{
				/* @var \MageDeveloper\Dataviewer\Domain\Model\Datatype $_datatype */
				
				if($_datatype->getHideAdd())
					continue;
				
				$iconId = "default";
				if($_datatype->getIcon())
					$iconId = $_datatype->getIcon();
				
				$rendered = "";
				$buttonHtmlRender = $html . $htmlButton;
				$title = LocalizationUtility::translate("module.create_record_by_datatype", [$_datatype->getName()]);

				$view->assign("currentPageId", $currentPageId);
				$view->assign("datatype", $_datatype);
				$view->assign("icon", $iconId);
				$view->assign("title", $title);				
				
				$rendered = $view->renderSource($buttonHtmlRender);
				
				$button = $buttonBar->makeFullyRenderedButton();
				$button->setHtmlSource($rendered);
				
				$buttons[ButtonBar::BUTTON_POSITION_LEFT][2][] = $button;
			}
			
		}

		return $buttons;
	}

	/**
	 * Resolves the current page id
	 *
	 * @return int
	 */
	protected function _resolveCurrentPageId()
	{
		$currentPageId = (int)GeneralUtility::_GP("id");

		if (!$currentPageId || $currentPageId <= 0)
		{
			$returnUrl = GeneralUtility::_GP("returnUrl");
			$currentPageId = \MageDeveloper\Dataviewer\Utility\UrlUtility::extractPidFromUrl($returnUrl);
		}

		return (int)$currentPageId;
	}
}
