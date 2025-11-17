<?php
declare(strict_types=1);

namespace Aix\Dataviewer\LabelUserFunc;

use Aix\Dataviewer\Domain\Model\Variable as VariableModel;
use Aix\Dataviewer\Domain\Repository\VariableRepository;
use Aix\Dataviewer\Utility\LocalizationUtility as Locale;

class Variable
{
    /**
     * @var VariableRepository
     */
    protected $variableRepository;

    /**
     * @param VariableRepository $variableRepository
     */
    public function injectVariableRepository(VariableRepository $variableRepository)
    {
        $this->variableRepository = $variableRepository;
    }

	/**
	 * UserFunc for FieldValue Label
	 *
	 * @param array $pObj Object Information
	 * @return void
	 */
	public function displayLabel(&$pObj): void
	{
		if (isset($pObj["row"]))
		{
			$row = $pObj["row"];
			/* @var Variable $variable */
			$variable = $this->variableRepository->findByUid($row["uid"], false);

			if ($variable instanceof VariableModel) {
				$type = $variable->getType();
				$name = $variable->getVariableName();
                if($type == \Aix\Dataviewer\Domain\Model\Variable::VARIABLE_TYPE_GET ||
                   $type == \Aix\Dataviewer\Domain\Model\Variable::VARIABLE_TYPE_GET_POST ||
                   $type == \Aix\Dataviewer\Domain\Model\Variable::VARIABLE_TYPE_POST
                ) {
                    if($variable->getParameterName() != '') {
                        $name = $variable->getParameterName();
                    }
                }
				$pid = $variable->getPid();
                $uid = $variable->getUid();
				$typeStr = Locale::translate("variable_type.{$type}");
				if ($typeStr) {
                    $pObj["title"] = "[u:{$uid}|p:{$pid}]" . " " . $typeStr . " '{$name}'";
                }
			}
		}
	}
}
