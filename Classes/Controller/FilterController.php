<?php
namespace MageDeveloper\Dataviewer\Controller;

use MageDeveloper\Dataviewer\Service\Session\FilterSessionService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

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
class FilterController extends AbstractController
{
	/***************************************************************************
	 * This controller is used for filtering records
	 ***************************************************************************/

	/**
	 * Filter Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\FilterSettingsService
	 * @inject
	 */
	protected $filterSettingsService;

	/**
	 * Filter Session Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Session\FilterSessionService
	 * @inject
	 */
	protected $filterSessionService;

	/**
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Index Action
	 * Displays the search form and
	 * evaluates the search on post/get
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$filters = $this->_getFilters();
		$activeFilters = $this->filterSessionService->getSelectedOptions();

		// Inject fields
		$this->_injectFields($filters);
		$this->_injectFields($activeFilters);

		$activeFiltersGrouped = [];
		foreach($activeFilters as $_activeFilter)
		{
			$activeFiltersGrouped[$_activeFilter["field_id"]]["field"] = $_activeFilter["field"];
			$activeFiltersGrouped[$_activeFilter["field_id"]]["filters"][] = $_activeFilter;
		}

		$this->view->assign("filters", $filters);
		$this->view->assign("activeFilters", $activeFiltersGrouped);
		$this->view->assign("targetUid", $this->filterSettingsService->getTargetContentUid());
	}

	/**
	 * Action for adding new filters to the session
	 *
	 * @return void
	 */
	public function addAction()
	{
		if(!$this->_checkTargetUid())
			$this->_redirectToPid();

		if ($this->request->hasArgument("filters"))
		{
			// Prepare Filters
			$filtersFromPost = $this->request->getArgument("filters");
			$merge = false; // Merge Filters, this is on our todo list
			$selected = [];

			foreach($filtersFromPost as $_fieldId=>$_fArray)
			{
				foreach($_fArray as $key=>$_id)
				{
					if ($selectedOption = $this->_getOptionById($_id))
						$selected[] = $selectedOption;
					else
						if($selectedOption = $this->_getOptionById($key))
						{
							// We only add the filter, if we received a useful value
							if($_id != "")
							{
								$selectedOption["field_value"] = $_id;
								$selected[] = $selectedOption;
							}
						}
						else
						{
							// Option was not found in current filter,
							// so we determine the setting, if we can
							// merge it anyway with the previous selected
							if($merge === true)
							{
								// We need to security check the selected option
								$selectedOption["field_value"] = $_id;
								$selected[] = $selectedOption;
							}
						}
				}

			}

			$previousSelected = $this->filterSessionService->getSelectedOptions();
			$selectedOptions = $this->_prepareSelectedOptionsArray($previousSelected, $selected, $merge);

			/////////////////////////////////////////////////////////////////
			// Signal-Slot for the post-processing of the selected options //
			/////////////////////////////////////////////////////////////////
			$this->signalSlotDispatcher->dispatch(
				__CLASS__,
				"postProcessSelectedOptions",
				[
					&$selectedOptions,
					&$this,
				]
			);

			$this->filterSessionService->setSelectedOptions($selectedOptions);
		}

		$this->_redirectToPid();
	}

	/**
	 * Remove a filter from the session
	 *
	 * @param string $id
	 * @return void
	 */
	public function removeAction($id)
	{
		if(!$this->_checkTargetUid())
			$this->forward("index");

		$this->filterSessionService->removeOption($id);
		$this->forward("index");
	}

	/**
	 * Resets all filters
	 *
	 * @return void
	 */
	public function resetAction()
	{
		if(!$this->_checkTargetUid())
			$this->forward("index");

		$this->filterSessionService->setSelectedOptions([]);
		$this->forward("index");
	}

	/**
	 * Injects fields to a filter array
	 *
	 * @param array $filters
	 * @return void
	 */
	protected function _injectFields(array &$filters)
	{
		foreach($filters as $_fId=>$_filter)
		{
			$fieldId = $_filter["field_id"];

			if(is_numeric($fieldId))
			{
				$field   = $this->fieldRepository->findByUid($fieldId, true);
				if ($field instanceof \MageDeveloper\Dataviewer\Domain\Model\Field)
					$filters[$_fId]["field"] = $field;
				else
					unset($filters[$_fId]);	// We unset the filter, because we could'nt find the according field	
			}
			else
			{
				$filters[$_fId]["field"] = $fieldId;
			}
		}
	}

	/**
	 * Gets the filters from the plugin settings
	 *
	 * @return array
	 */
	protected function _getFilters()
	{
		$filters = $this->filterSettingsService->getFilters();

		// Fill in session data
		foreach($filters as $i=>$_filter)
		{
			$fieldId = $_filter["field_id"];
			$filters[$i]["is_active"] = false;
			$filterType = $_filter["filter_type"];

			foreach($_filter["options"] as $j=>$_option)
			{
				$optionId = $_option["id"];
				$optionSelected = $this->filterSessionService->checkIsSelected($fieldId, $optionId);
				$filters[$i]["options"][$j]["selected"] = $optionSelected;
				$filters[$i]["options"][$j]["filter_type"] = $filterType;

				if ($optionSelected)
					$filters[$i]["is_active"] = true;

			}
		}

		return $filters;
	}

	/**
	 * Gets an according option setting by a given id hash
	 *
	 * @param string $id
	 * @return array|bool
	 */
	protected function _getOptionById($id)
	{
		$filters = $this->_getFilters();
		foreach($filters as $i=>$_filter)
		{
			$options = $_filter["options"];
			foreach($options as $_i=>$_option)
			{
				if ($_option["id"] == $id)
					return $_option;
			}

		}
		return false;
	}

	/**
	 * Prepares an final filter array from raw data from the form post
	 *
	 * @param array $previousSelectedOptions
	 * @param array $currentSelectedOptions
	 * @param bool $merge
	 * @return array
	 */
	protected function _prepareSelectedOptionsArray(array $previousSelectedOptions = [], array $currentSelectedOptions = [], $merge = false)
	{
		$selectedOptions = [];

		foreach($previousSelectedOptions as $i=>$_prvOpt)
			foreach($currentSelectedOptions as $j=>$_curOpt)
			{
				if(($_prvOpt["field_id"] == $_curOpt["field_id"]) && $merge === false)
				{
					// Clean previous array
					unset($previousSelectedOptions[$i]);
				}

				if($_curOpt["filter_field"] == "")
				{
					// Resetting the filter 
					unset($currentSelectedOptions[$j]);
				}
			}

		$selectedOptions = array_merge($previousSelectedOptions, $currentSelectedOptions);
		return $selectedOptions;
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
		$uid = $this->filterSettingsService->getTargetContentUid();
		$sessionKey = FilterSessionService::SESSION_PREFIX_KEY;
		$this->filterSessionService->setPrefixKey("{$sessionKey}-{$uid}");

		// Parent
		parent::initializeView($view);
	}
}
