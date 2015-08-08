<?php
namespace FluidTYPO3\Builder\Tests\Unit\CodeGeneration\Extension;
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
use FluidTYPO3\Builder\CodeGeneration\Extension\ExtensionGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ExtensionGeneratorTest
 */
class ExtensionGeneratorTest extends UnitTestCase {

	/**
	 * @throws \org\bovigo\vfs\vfsStreamException
	 */
	public static function setUpBeforeClass() {
		vfsStreamWrapper::register();
		vfsStreamWrapper::setRoot(new vfsStreamDirectory('temp'));
	}

	/**
	 * @test
	 */
	public function testDryRun() {
		/** @var ObjectManager $objectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

		/** @var ExtensionGenerator|\PHPUnit_Framework_MockObject_MockObject $instance */
		$instance = $this->getMock(
			'FluidTYPO3\\Builder\\CodeGeneration\\Extension\\ExtensionGenerator',
			array('getBuilderExtensionPath', 'getPreparedCodeTemplate')
		);
		$codeTemplate = $this->getMock('FluidTYPO3\\Builder\\CodeGeneration\\CodeTemplate', array('getFilePath'));
		$codeTemplate->expects($this->any())->method('getFilePath')->will($this->returnArgument(0));
		$instance->expects($this->any())->method('getBuilderExtensionPath')->will($this->returnValue(vfsStream::url('temp/')));
		$instance->expects($this->any())->method('getPreparedCodeTemplate')->will($this->returnValue($codeTemplate));
		$instance->injectObjectManager($objectManager);
		$instance->setDry(TRUE);
		$instance->setConfiguration(array(
			'dependencies' => array('fluidpages', 'fluidcontent', 'fluidbackend'),
			'controllers' => TRUE, 'extensionKey' => 'Vendor.Dummy'
		));
		$result = $instance->generate();
		$this->assertEquals('Built extension "dummy"', $result);
	}

	/**
	 * @test
	 */
	public function testThrowsExceptionIfTargetDirectoryExists() {
		$instance = new ExtensionGenerator();
		$instance->setTargetFolder(vfsStream::url('temp'));
		$this->setExpectedException('RuntimeException');
		$instance->generate();
	}

}
