<?php
namespace MageDeveloper\Dataviewer\RealUrl;

use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;

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
class RealUrlConfiguration
{
	/**
	 * Gets the configuration array for realurl
	 * 
	 * @return array
	 */
	public function getRealUrlConfiguration(&$params, $pObj)
	{
		// Merging the configuration
		return array_merge_recursive($params["config"], 
			$this->getPostVarSets_Record(),
			$this->getPostVarSets_Pager()
		);
	}

	/**
	 * =======================================================
	 * postVarSets for the Pager plugin
	 * =======================================================
	 *
	 * @return array
	 */
	public function getPostVarSets_Pager()
	{
		return $config = [
			"postVarSets" => [
				"_DEFAULT" => [
					"page" => array(
						array(
							"GETvar" => "tx_dataviewer_pager[controller]",
							"valueMap"	=> array(
								"Pager" => "",
							),
							'noMatch' => 'bypass',
						),
						array(
							"GETvar" => "tx_dataviewer_pager[action]",
							"valueMap"	=> array(
								"page" => "",
							),
							'noMatch' => 'bypass',
						),
						array(
							"GETvar" => "tx_dataviewer_pager[page]",
						),
						array(
							"GETvar" => "tx_dataviewer_pager[targetUid]",
						),
					),
				],
			],
		];
	}

	/**
	 * =======================================================
	 * postVarSets for the Record plugin
	 * =======================================================
	 * 
	 * @return array
	 */
	public function getPostVarSets_Record()
	{
		return $config = [
			"postVarSets" => [
				"_DEFAULT" => [
					"get" => array(
						array(
							"GETvar" => "tx_dataviewer_record[controller]",
							"valueMap"	=> array(
								"Record" => "",
							),
							'noMatch' => 'bypass',
						),
						array(
							"GETvar" 	=> "tx_dataviewer_record[action]",
							"valueMap"	=> array(
								"list" => "list",
								"detail" => "detail",
								"part" => "part",
								"record" => "dynamicDetail",
							),
							'noMatch' => 'bypass',
						),
						array(
							'GETvar' => 'tx_dataviewer_record[record]',
							'lookUpTable' => array(
								'table' => 'tx_dataviewer_domain_model_record',
								'id_field' => 'uid',
								'alias_field' => 'title',
								'addWhereClause' => ' AND NOT deleted',
								'useUniqueCache' => '1',
								'useUniqueCache_conf' => array(
									'strtolower' => '1',
									'spaceCharacter' => '-'
								)
							),
						),
					),
				],
			],
		];
	}
}
