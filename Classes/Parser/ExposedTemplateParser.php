<?php
namespace FluidTYPO3\Builder\Parser;
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

use TYPO3\CMS\Fluid\Core\Parser\ParsingState;
use TYPO3\CMS\Fluid\Core\Parser\Exception;
use TYPO3\CMS\Fluid\Core\Parser\TemplateParser;
use TYPO3\CMS\Fluid\Core\Parser\ParsedTemplateInterface;

class ExposedTemplateParser extends TemplateParser {

	/**
	 * @var array
	 */
	protected $splitTemplate = array();

	/**
	 * Parses a given template string and returns a parsed template object.
	 *
	 * The resulting ParsedTemplate can then be rendered by calling evaluate() on it.
	 *
	 * Normally, you should use a subclass of AbstractTemplateView instead of calling the
	 * TemplateParser directly.
	 *
	 * @param string $templateString The template to parse as a string
	 * @return ParsedTemplateInterface Parsed template
	 * @throws Exception
	 */
	public function parse($templateString) {
		if (!is_string($templateString)) {
			throw new Exception('Parse requires a template string as argument, ' . gettype($templateString) . ' given.', 1224237899);
		}
		$this->reset();

		$templateString = $this->extractNamespaceDefinitions($templateString);
		$this->splitTemplate = $this->splitTemplateAtDynamicTags($templateString);

		$parsingState = $this->buildObjectTree($this->splitTemplate, self::CONTEXT_OUTSIDE_VIEWHELPER_ARGUMENTS);

		$variableContainer = $parsingState->getVariableContainer();
		if ($variableContainer !== NULL && $variableContainer->exists('layoutName')) {
			$parsingState->setLayoutNameNode($variableContainer->get('layoutName'));
		}

		return $parsingState;
	}

	/**
	 * @param array $splitTemplate
	 * @param integer $context
	 * @return ParsingState
	 */
	public function buildObjectTree($splitTemplate, $context = self::CONTEXT_OUTSIDE_VIEWHELPER_ARGUMENTS) {
		return parent::buildObjectTree($splitTemplate, $context);
	}

	/**
	 * @return array
	 */
	public function getSplitTemplate() {
		return $this->splitTemplate;
	}

}
