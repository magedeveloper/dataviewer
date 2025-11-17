<?php
declare(strict_types=1);

namespace Aix\Dataviewer\UserFunc;

use Aix\Dataviewer\Domain\Model\FieldValue;
use Aix\Dataviewer\Domain\Repository\FieldRepository;
use Aix\Dataviewer\Domain\Repository\FieldValueRepository;
use Aix\Dataviewer\Utility\DebugUtility;
use Aix\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Database
{
    /**
     * Field Repository
     *
     * @var FieldRepository
     */
    protected $fieldRepository;

    /**
     * FieldValue Repository
     *
     * @var FieldValueRepository
     */
    protected $fieldValueRepository;

    /**
     * @param FieldRepository $fieldRepository
     */
    public function injectFieldRepository(FieldRepository $fieldRepository)
    {
        $this->fieldRepository = $fieldRepository;
    }

    /**
     * @param FieldRepository $fieldValueRepository
     */
    public function injectFieldValueRepository(FieldRepository $fieldValueRepository)
    {
        $this->fieldValueRepository = $fieldValueRepository;
    }

    /**
	 * Populate flexform tables
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateTablesAction(array &$config, &$parentObject): array
	{
        $options = [
            [
                'label' => '',
                'value' => ''
            ]
        ];

		/* @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
		$query = GeneralUtility::makeInstance(ConnectionPool::class)
			->getConnectionForTable("tt_content");

		$tables = $query->fetchAllAssociative("SHOW TABLES");

		foreach ($tables as $_table) {
			$tableName = reset($_table);
			$options[] = [
                'label' => $tableName,
                'value' => $tableName,
            ];
		}

		$config['items'] = $options;

		return $config;
	}

	/**
	 * Populate flexform tables
	 *
	 * @param array $config Configuration Array
	 * @param mixed $parentObject Parent Object
	 * @return array
	 */
	public function populateColumnsAction(array &$config, &$parentObject): array
	{
		$tableName = reset($config['row']['table_content']);

		$options = [];
		if ($tableName) {
			/* @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
			$query = GeneralUtility::makeInstance(ConnectionPool::class)
				->getConnectionForTable('tt_content');

			$columns = $query->fetchAllAssociative("SHOW COLUMNS FROM {$tableName}");

			foreach ($columns as $_column) {
				$field = $_column['Field'];
				$options[] = [
                    'label' => $field,
                    'value' => $field,
                ];
			}

		}

		$config['items'] = $options;

		return $config;
	}

	/**
	 * Displays the result of the selected table/column
	 *
	 * @param array $config
	 * @param array $parentObject
	 * @return string
     * @throws \Exception
	 */
	public function displayTableContentResult(array &$config, &$parentObject): string
	{
		$this->populateColumnsAction($config, $parentObject);
		unset($config['items'][0]);

		$html = '';

		$options = [];

		if (isset($config['row']))
		{
			$fieldValueUid = $config['row']['uid'];
			$fieldValue = $this->fieldValueRepository->findByUid($fieldValueUid);

			if ($fieldValue instanceof FieldValue) {
				$statement = "SELECT * FROM {$fieldValue->getTableContent()} {$fieldValue->getWhereClause()}";


                if (($fieldValue->getType() == FieldValue::TYPE_DATABASE)
                    &&
                    $fieldValue->getTableContent() &&
                    $fieldValue->getColumnName() &&
                    strpos($statement, '{') === false
                ) {
                    try {
                        $result = $this->fieldRepository->findEntriesForFieldValue($fieldValue);

                        $html .= '<h4>'.Locale::translate('items', [count($result)]).'</h4>';
                        $html .= DebugUtility::debugVariable($result);

                    } catch (\Exception $e) {
                        $html = "<div class=\"alert alert-danger\">{$e->getMessage()}<br /><br /><strong>Statement:</strong><br /><pre>{$statement}</pre></div>";
                    }
                }
                else {
			        $html = "<strong>Statement</strong><br /><pre>{$statement}</pre>";
				}
			}

		}

		return $html;
	}
}
