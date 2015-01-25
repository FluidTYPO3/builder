<?php
namespace FluidTYPO3\Builder\Tests\Unit\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Tests\BaseTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FrontendControllerTest
 */
class FrontendControllerTest extends BaseTestCase {

	/**
	 * @test
	 */
	public function injectsExpectedProperties() {
		$class = $this->getInstanceClassName();
		$instance = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')->get($class);
		$this->assertAttributeInstanceOf('FluidTYPO3\\Builder\\Service\\ExtensionService', 'extensionService', $instance);
	}

	/**
	 * @return string
	 */
	protected function getInstanceClassName() {
		return substr(str_replace('Tests\\Unit\\', '', get_class($this)), 0, -4);
	}

}
