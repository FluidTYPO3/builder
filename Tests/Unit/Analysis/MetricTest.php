<?php
namespace FluidTYPO3\Builder\Tests\Unit\Analysis;
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

use FluidTYPO3\Builder\Analysis\Metric;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Error\Message;

/**
 * Class MetricTest
 */
class MetricTest extends UnitTestCase {

	/**
	 * @param string $property
	 * @param mixed $testValue
	 * @test
	 * @dataProvider getPropertiesTestValues
	 */
	public function testGetterAndSetter($property, $testValue) {
		$instance = new Metric();
		$setter = 'set' . ucfirst($property);
		$getter = 'get' . ucfirst($property);
		$instance->$setter($testValue);
		$this->assertEquals($testValue, $instance->$getter());
	}

	/**
	 * @return array
	 */
	public function getPropertiesTestValues() {
		return array(
			array('name', 'metricname'),
			array('value', 'metricvalue'),
			array('messages', array(new Message('test', 1))),
			array('payload', array('test'))
		);
	}

	/**
	 * @test
	 */
	public function testIncrement() {
		$instance = new Metric();
		$instance->setValue(2);
		$instance->increment();
		$this->assertEquals(3, $instance->getValue());
		$instance->increment(2);
		$this->assertEquals(5, $instance->getValue());
	}

	/**
	 * @test
	 */
	public function testSetOnlyIfHigher() {
		$instance = new Metric();
		$instance->setValue(5);
		$instance->setOnlyIfHigher(3);
		$this->assertEquals(5, $instance->getValue());
		$instance->setOnlyIfHigher(10);
		$this->assertEquals(10, $instance->getValue());
	}

	/**
	 * @test
	 */
	public function testSetOnlyIfLower() {
		$instance = new Metric();
		$instance->setValue(5);
		$instance->setOnlyIfLower(10);
		$this->assertEquals(5, $instance->getValue());
		$instance->setOnlyIfLower(3);
		$this->assertEquals(3, $instance->getValue());
	}

}
