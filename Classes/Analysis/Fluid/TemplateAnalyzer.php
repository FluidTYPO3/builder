<?php
namespace FluidTYPO3\Builder\Analysis\Fluid;
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
 * ************************************************************* */

use FluidTYPO3\Builder\Parser\ExposedTemplateParser;
use FluidTYPO3\Builder\Result\ParserResult;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Class TemplateAnalyzer
 */
class TemplateAnalyzer {

	/**
	 * @var NodeCounter
	 */
	protected $nodeCounter;

	/**
	 * @var ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var array
	 */
	protected $messages = array();

	/**
	 * @var ExposedTemplateParser
	 */
	protected $parser;

	/**
	 * @param ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
		$this->nodeCounter = $this->objectManager->get('FluidTYPO3\Builder\Analysis\Fluid\NodeCounter');
	}

	/**
	 * @param string $templatePathAndFilename
	 * @return ParserResult
	 */
	public function analyzePathAndFilename($templatePathAndFilename) {
		$templateString = file_get_contents($templatePathAndFilename);
		return $this->analyze($templateString);
	}

	/**
	 * @param string $templateString
	 * @return ParserResult
	 */
	public function analyze($templateString) {
		/** @var ExposedTemplateParser $parser */
		$parser = $this->objectManager->get('FluidTYPO3\Builder\Parser\ExposedTemplateParser');
		$parsedTemplate = $parser->parse($templateString);
		$metrics = $this->nodeCounter->count($parser, $parsedTemplate);
		$this->messages = $this->nodeCounter->getMessages();
		$result = new ParserResult();
		$result->setViewHelpers($parser->getUniqueViewHelpersUsed());
		$result->setPayload($metrics);
		$result->setValid(TRUE);
		$result->setPayloadType(ParserResult::PAYLOAD_METRICS);
		$this->parser = $parser;
		return $result;
	}

	/**
	 * @return ExposedTemplateParser
	 */
	public function getParser() {
		return $this->parser;
	}

}
