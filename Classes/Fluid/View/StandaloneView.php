<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Fluid\View;

use TYPO3\CMS\Fluid\View\StandaloneView as TYPO3StandaloneView;

class StandaloneView extends TYPO3StandaloneView
{
	/**
	 * Renders template source code by a given string
	 *
	 * @param string $source Template Source Code
	 * @return string
	 */
	public function renderSource($source): string
	{
		$this->setTemplateSource($source);
		return (string)$this->render();
	}

}
