<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Utility;

use Aix\Dataviewer\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TSConfigUtility
{

    /**
     * @param string $table
     * @return Connection
     */
    public static function getConnectionForTable(string $table): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($table);
    }

    /**
     * Adding user ts config dynamically from the settings environment
     *
     * @return void
     */
    public static function addPageTSConfig(): void
    {
        /* @var Connection $connection */
        $connection = self::getConnectionForTable(ExtensionConfiguration::EXTENSION_DATATYPE_TABLE);

        $datatypes = $connection->select(['*'], ExtensionConfiguration::EXTENSION_DATATYPE_TABLE)->fetchAllAssociative();

        $deniedTables = [];
        foreach ($datatypes as $_datatype) {
            $tableName = $_datatype['tablename'];

            // Hide Table Check
            if ($_datatype['hide_records'] == '1') {
                ExtensionManagementUtility::addPageTSConfig("mod.web_list.table.{$tableName}.hideTable = 1");
            }

            // Hide Add Record Button
            if ($_datatype['hide_add'] == '1') {
                $deniedTables[] = $tableName;
            }

            // Setting table to top of lists
            ExtensionManagementUtility::addPageTSConfig("mod.web_list.tableDisplayOrder.{$tableName}.before = pages, fe_groups, fe_users, tx_dataviewer_domain_model_datatype,tx_dataviewer_domain_model_field");
        }

        // Preventing create new record buttons
        $deniedTablesStr = implode(',', $deniedTables);
        ExtensionManagementUtility::addPageTSConfig("mod.web_list.deniedNewTables = {$deniedTablesStr}");
    }

}
