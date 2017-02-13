<?php
namespace MageDeveloper\Dataviewer\Controller;

use MageDeveloper\Dataviewer\Service\Session\SortSessionService;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
class SortController extends AbstractController
{
	/***************************************************************************
	 * This controller handles the sorting configuration
	 ***************************************************************************/
	
	/**
	 * Sort Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\SortSettingsService
	 * @inject
	 */
	protected $sortSettingsService;

	/**
	 * Sort Session Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Session\SortSessionService
	 * @inject
	 */
	protected $sortSessionService;

	/**
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Index Action
	 * Displays the sort form
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$sortBy		= $this->sortSessionService->getSortField();
		$sortOrder	= $this->sortSessionService->getSortOrder();
		$sortByOptions = $this->_getSortByOptions();

		$this->view->assign("sortBy", $sortBy);
		$this->view->assign("sortOrder", $sortOrder);

		// Fetching the name of the current sort by option
		$sortByName = null;
		if(in_array($sortBy, array_keys($sortByOptions)))
			$sortByName = $sortByOptions[$sortBy];

		$this->view->assign("sortByOptions", $sortByOptions);
		$this->view->assign("targetUid", $this->sortSettingsService->getTargetContentUid());
		$this->view->assign("sortByName", $sortByName);
	}


	/**
	 * Sort Action
	 * Sort Post from the sort form will
	 * add the posted information to the session
	 *
	 * @param string $sortBy Sort by
	 * @param string $sortOrder Sort Order
	 * @return void
	 */
	public function sortAction($sortBy = null, $sortOrder = null)
	{
		if(!$this->_checkTargetUid())
			$this->forward("index");
	
		if (is_null($sortBy) || is_null($sortOrder))
			$this->forward("index");

		$sortByOptions  = $this->sortSettingsService->getSortFields();
		
		/********************
		 * Validate Sort By
		 ********************/
		if (!in_array($sortBy, $sortByOptions))
			$sortBy = reset($sortByOptions);

		/********************
		 * Validate Sort Order
		 ********************/
		if ($sortOrder != QueryInterface::ORDER_ASCENDING && $sortOrder != QueryInterface::ORDER_DESCENDING)
			$sortOrder = QueryInterface::ORDER_DESCENDING;

		////////////////////////////////////////////////////
		// Signal-Slot for processing the sort parameters //
		//////////////////////////////////////?/////////////
		$this->signalSlotDispatcher->dispatch(
			__CLASS__,
			"searchPostProcess",
			[
				&$sortBy,
				&$sortOrder,
				&$this,
			]
		);	

		$this->sortSessionService->setSortField($sortBy);
		$this->sortSessionService->setSortOrder($sortOrder);

		$this->_redirectToPid();
	}

	/**
	 * Gets the sort by options for
	 * the form select
	 *
	 * @return array
	 */
	protected function _getSortByOptions()
	{
		$sortFields = $this->sortSettingsService->getSortFields();

		$sortByOptions = [];
		if (!empty($sortFields))
		{
			foreach($sortFields as $i=>$_sortField)
			{
				if (is_numeric($_sortField))
				{
					// We guess that sort field is a field id, so we need to fetch
					// the whole field model
					$field = $this->fieldRepository->findByUid($_sortField, false);

					if ($field instanceof \MageDeveloper\Dataviewer\Domain\Model\Field)
					{
						$translationKey = "sort.{$field->getCode()}";
						$translation = Locale::translate($translationKey);

						if ($translation)
							$sortByOptions[$_sortField] = $translation;
						else
							$sortByOptions[$_sortField] = $field->getFrontendLabel();

					}

				}
				else
				{
					// Sorting is made by a database fieldname
					$translationKey = "sort.".strtolower(str_replace(".","_",$_sortField));
					$translation = Locale::translate($translationKey);

					if ($translation)
						$sortByOptions[$_sortField] = $translation;
					else
						$sortByOptions[$_sortField] = $_sortField;
				}
			}
		}
		
		return $sortByOptions;
	}

	/**
	 * initializeView
	 * Initializes the view
	 *
	 * Adds some variables to view that could always
	 * be useful
	 *
	 * @param ViewInterface $view
	 * @return void
	 */
	protected function initializeView(ViewInterface $view)
	{
		// Individual session key
		$uid = $this->sortSettingsService->getTargetContentUid();
		$sessionKey = SortSessionService::SESSION_PREFIX_KEY;
		$this->sortSessionService->setPrefixKey("{$sessionKey}-{$uid}");

		// Parent
		parent::initializeView($view);
	}
}
