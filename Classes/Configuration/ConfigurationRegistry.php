<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Configuration;

use Exception;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class ConfigurationRegistry
{
    /**
     * @var ConnectionPool
     */
    protected $connectionPool;

    /**
     * @param ConnectionPool $connectionPool
     */
    public function injectConnectionPool(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
    }

    /**
     * Sorts all datatype tables on top of mod list
     *
     * @return void
     */
    public function registerTableOrderings()
    {
        try {
            // We need to ignore exceptions here in case the table does not exist
            /* @var Connection $query */
            $query = $this->connectionPool
                ->getConnectionForTable('tx_dataviewer_domain_model_datatype');

            $datatypes = $query->select(['uid','tablename'], 'tx_dataviewer_domain_model_datatype')->fetchAll();

            foreach ($datatypes as $_datatype) {
                if ($_datatype['tablename'] != '') {
                   ExtensionManagementUtility::addPageTSConfig("mod.web_list.tableDisplayOrder.{$_datatype['tablename']}.before = pages, fe_groups, fe_users, tx_dataviewer_domain_model_datatype");
                }
            }

        } catch(Exception $e) {
            // No exception printing here
        }
    }
}