<?php
namespace MageDeveloper\Dataviewer\Hooks;

use In2code\Powermail\Utility\BackendUtility;
use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration;
use MageDeveloper\Dataviewer\Fluid\View\StandaloneView;
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Page\PageRenderer;

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
class ToolbarItem implements ToolbarItemInterface
{
	/**
	 * Object Manager
	 * 
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * persistenceManager
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;

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
	 * Plugin Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService
	 * @inject
	 */
	protected $pluginSettingsService;

	/**
	 * Constructor
	 *
	 * @return ToolbarItem
	 */
	public function __construct()
	{
		$this->objectManager 			= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->persistenceManager		= $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
		$this->iconFactory 				= $this->objectManager->get(\TYPO3\CMS\Core\Imaging\IconFactory::class);
		$this->datatypeRepository		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository::class);
		$this->recordRepository 		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
		$this->pluginSettingsService	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService::class);

		$this->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/Dataviewer/DataviewerMenu');
		$languageService = $this->getLanguageService();
		$this->getPageRenderer()->addInlineLanguageLabelArray([
			'dataviewer.loading' => $languageService->sL('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:backend.loading'),
			'record.delete' => $languageService->sL('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:backend.delete_record'),
			'record.confirmDelete' => $languageService->sL('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:backend.confirm_delete_record'),
		]);
	}

	/**
	 * Renders the menu so that it can be returned as response to an AJAX call
	 *
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function menuAction(ServerRequestInterface $request, ResponseInterface $response)
	{
		$menuContent = $this->getDropDown();

		$response->getBody()->write($menuContent);
		$response = $response->withHeader('Content-Type', 'text/html; charset=utf-8');
		return $response;
	}

	/**
	 * Deletes a record through an AJAX call
	 *
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function removeRecordAction(ServerRequestInterface $request, ResponseInterface $response)
	{
		$parsedBody = $request->getParsedBody();
		$queryParams = $request->getQueryParams();

		$recordId = (int)(isset($parsedBody['recordId']) ? $parsedBody['recordId'] : $queryParams['recordId']);
		$record = $this->recordRepository->findByUid($recordId, false);
		$success = false;
		if($record instanceof \MageDeveloper\Dataviewer\Domain\Model\Record)
		{
			try {
				$this->recordRepository->remove($record);
				$this->persistenceManager->persistAll();
				$success = true;
			} catch(\Exception $e)
			{
				$success = false;
			}
		}

		$response->getBody()->write(json_encode(['success' => $success]));
		return $response;
	}

	/**
	 * Shows or hides a record through an AJAX call
	 *
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function hideShowRecordAction(ServerRequestInterface $request, ResponseInterface $response)
	{
		$parsedBody = $request->getParsedBody();
		$queryParams = $request->getQueryParams();

		$recordId = (int)(isset($parsedBody['recordId']) ? $parsedBody['recordId'] : $queryParams['recordId']);
		$record = $this->recordRepository->findByUid($recordId, false);
		$success = false;
		if($record instanceof \MageDeveloper\Dataviewer\Domain\Model\Record)
		{
			if($record->getHidden() === true)
				$record->setHidden(false);
			else if($record->getHidden() === false)
				$record->setHidden(true);
		
			try {
				$this->recordRepository->update($record);
				$this->persistenceManager->persistAll();
				$success = true;
			} catch(\Exception $e)
			{
				$success = false;
			}
		}

		$response->getBody()->write(json_encode(['success' => $success]));
		return $response;
	}

	/**
	 * Returns the current BE user.
	 *
	 * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected function getBackendUser()
	{
		return $GLOBALS['BE_USER'];
	}

	/**
	 * Checks whether the user has access to this toolbar item
	 *
	 * @return bool TRUE if user has access, FALSE if not
	 */
	public function checkAccess()
	{
		$disabled = $this->getBackendUser()->getTSConfigVal('options.disableDataViewerToolbarItem');
		
		if(is_null($disabled))
			$disabled = false;
			
		return (bool)!$disabled;	
	}

	/**
	 * Render "item" part of this toolbar
	 *
	 * @return string Toolbar item HTML
	 */
	public function getItem()
	{
		$title = "MageDeveloper DataViewer";
		return '<span title="' . $title . '">' . 
				$this->iconFactory->getIcon('extensions-dataviewer-default', Icon::SIZE_SMALL)->render('inline') . 
				'</span>';
	}

	/**
	 * TRUE if this toolbar item has a collapsible drop down
	 *
	 * @return bool
	 */
	public function hasDropDown()
	{
		return true;
	}

	/**
	 * Render "drop down" part of this toolbar
	 *
	 * @return string Drop down HTML
	 */
	public function getDropDown()
	{
		$orderings = ["name" => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING];
		$datatypes = $this->datatypeRepository->findAll(false, $orderings);
		$latest = $this->recordRepository->findLatest(20);
		
		if(count($datatypes))
		{
			/* @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
			$view = $this->objectManager->get(StandaloneView::class);
			$templateFile = $view->getFullTemplatePathForFile( "ToolbarItem/Index.html" );
			$view->setTemplatePathAndFilename($templateFile);
			$view->getRequest()->setControllerExtensionName( ExtensionConfiguration::EXTENSION_KEY );
			
			$view->assign("datatypes", $datatypes);
			$view->assign("latest", $latest);

			return $view->render();
		}
		else
		{
			return Locale::translate("backend.no_datatypes_found");
		}
	
	}

	/**
	 * Returns an array with additional attributes added to containing <li> tag of the item.
	 *
	 * Typical usages are additional css classes and data-* attributes, classes may be merged
	 * with other classes needed by the framework. Do NOT set an id attribute here.
	 *
	 * array(
	 *     'class' => 'my-class',
	 *     'data-foo' => '42',
	 * )
	 *
	 * @return array List item HTML attributes
	 */
	public function getAdditionalAttributes()
	{
	}

	/**
	 * Returns an integer between 0 and 100 to determine
	 * the position of this item relative to others
	 *
	 * By default, extensions should return 50 to be sorted between main core
	 * items and other items that should be on the very right.
	 *
	 * @return int 0 .. 100
	 */
	public function getIndex()
	{
		return 100;
	}

	/**
	 * Returns current PageRenderer
	 *
	 * @return PageRenderer
	 */
	protected function getPageRenderer()
	{
		return GeneralUtility::makeInstance(PageRenderer::class);
	}

	/**
	 * Returns LanguageService
	 *
	 * @return \TYPO3\CMS\Lang\LanguageService
	 */
	protected function getLanguageService()
	{
		return $GLOBALS['LANG'];
	}
}
