<?php
namespace FluidTYPO3\Builder\Tests\Unit\CodeGeneration;
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

use FluidTYPO3\Builder\CodeGeneration\CodeTemplate;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class CodeTemplateTest
 */
class CodeTemplateTest extends UnitTestCase {

	/**
	 * @dataProvider getGetterAndSetterTestValues
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$subject = new CodeTemplate();
		$setter = 'set' . ucfirst($property);
		$getter = 'get' . ucfirst($property);
		$subject->$setter($value);
		$this->assertEquals($value, $subject->$getter());
	}

	/**
	 * @return array
	 */
	public function getGetterAndSetterTestValues() {
		return [
			['identifier', 'test'],
			['variables', ['test' => 'test']],
			['path', 'test'],
			['suffix', 'path']
		];
	}

	/**
	 * @dataProvider getRenderTestValues
	 * @param string $marker
	 * @param string $expectedOutput
	 */
	public function testRender($marker, $expectedOutput) {
		$subject = new CodeTemplate();
		$subject->setVariables(['foo' => $marker]);
		$subject->setIdentifier('CodeTemplate');
		$subject->setPath($fixture = ExtensionManagementUtility::extPath('builder', 'Tests/Fixtures/Templates/'));
		$this->assertEquals($expectedOutput, trim($subject->render()));
	}

	/**
	 * @return array
	 */
	public function getRenderTestValues() {
		return [
			['bar', 'content: bar'],
			['baz', 'content: baz']
		];
	}

}
