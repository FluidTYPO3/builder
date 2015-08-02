<?php
namespace FluidTYPO3\Builder\Tests\Unit\Analysis\Fluid;
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

use FluidTYPO3\Builder\Analysis\Fluid\NodeCounter;
use FluidTYPO3\Builder\Analysis\Metric;
use FluidTYPO3\Builder\Parser\ExposedTemplateParser;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class NodeCounterTest
 */
class NodeCounterTest extends UnitTestCase {

	/**
	 * @var array
	 */
	protected $expectedMetricValues = array(
		NodeCounter::METRIC_TOTAL_SPLITS => 8,
		NodeCounter::METRIC_TOTAL_NODES => 12,
		NodeCounter::METRIC_VIEWHELPERS => 3,
		NodeCounter::METRIC_SECTIONS => 1,
		NodeCounter::METRIC_CONDITION_NODES => 1,
		NodeCounter::METRIC_NODES_PER_SECTION_AVERAGE => 3,
		NodeCounter::METRIC_NODES_PER_SECTION_MAXIMUM => 3,
		NodeCounter::METRIC_CACHED_SIZE => 3.39999999999,
		NodeCounter::METRIC_MAXIMUM_ARGUMENT_COUNT => 3,
		NodeCounter::METRIC_MAXIMUM_NESTING_LEVEL => 2
	);

	/**
	 * @return array
	 */
	protected function getPreparedFixtures() {
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		/** @var ExposedTemplateParser $template */
		$template = $objectManager->get('FluidTYPO3\\Builder\\Parser\\ExposedTemplateParser');
		$fixture = ExtensionManagementUtility::extPath('builder', 'Tests/Fixtures/Templates/AnalysisFixture.html');
		$parsedTemplate = $template->parse(file_get_contents($fixture));
		/** @var NodeCounter $nodeCounter */
		$nodeCounter = $objectManager->get('FluidTYPO3\\Builder\\Analysis\\Fluid\\NodeCounter');
		return array($nodeCounter, $template, $parsedTemplate);
	}

	/**
	 * @test
	 */
	public function testNodeCounterAgainstKnownFixture() {
		/** @var NodeCounter $nodeCounter */
		list ($nodeCounter, $template, $parsedTemplate) = $this->getPreparedFixtures();
		$result = $nodeCounter->count($template, $parsedTemplate);
		$expectedValues = $this->expectedMetricValues;
		/** @var Metric $metric */
		foreach ($result as $metric) {
			$name = $metric->getName();
			$this->assertEquals($expectedValues[$name], $metric->getValue(), 'Unexpected value of ' . $name);
		}
	}

}
