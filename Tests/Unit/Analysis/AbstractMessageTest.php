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

use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class AbstractMessageTest
 */
class AbstractMessageTest extends UnitTestCase {

	/**
	 * @param string $name
	 * @param mixed $value
	 * @test
	 * @dataProvider getGetterAndSetterTestValues
	 */
	public function testGetterAndSetter($name, $value) {
		$message = $this->getMockForAbstractClass('FluidTYPO3\\Builder\\Analysis\\AbstractMessage');
		$setter = 'set' . ucfirst($name);
		$getter = 'get' . ucfirst($name);
		$message->$setter($value);
		$this->assertEquals($value, $message->$getter());
	}

	/**
	 * @return array
	 */
	public function getGetterAndSetterTestValues() {
		return array(
			array('message', 'I am a message'),
			array('severity', 1),
			array('payload', array('foo' => 'bar'))
		);
	}

}
