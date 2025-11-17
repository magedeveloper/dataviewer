<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Utility;

use Aix\Dataviewer\Domain\Model\Variable;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GetPostUtility
{
	/**
	 * Secures a GET variable
	 *
	 * @param string $variable
	 * @return string
	 */
	public static function secureVariableGet(string $variable): string
	{
		$variable = htmlspecialchars($variable);
		$variable = strip_tags($variable);

		return $variable;
	}

	/**
	 * Secures a POST variable
	 *
	 * @param string $variable
	 * @return string
	 */
	public static function secureVariablePost(string $variable): string
	{
		$variable = htmlspecialchars($variable);
		$variable = strip_tags($variable);

		return $variable;
	}

    /**
     * @param string $parameterName
     * @param string $type
     * @return mixed
     */
    public static function getEnvironmentalParameterValue(string $parameterName, string $type = 'GET')
    {
        switch ($type) {
            case 'GET':
            case (string)Variable::VARIABLE_TYPE_GET:
                return GeneralUtility::_GET($parameterName);
            case (string)Variable::VARIABLE_TYPE_POST:
            case 'POST':
                return GeneralUtility::_POST($parameterName);
            case (string)Variable::VARIABLE_TYPE_GET_POST:
            case 'GET/POST':
            case 'GETPOST':
            default:
                return GeneralUtility::_GP($parameterName);
        }
    }
}
