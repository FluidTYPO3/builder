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

use TYPO3\CMS\Fluid\Core\Parser\InterceptorInterface;
use TYPO3\CMS\Fluid\Core\Parser\ParsingState;
use TYPO3\CMS\Fluid\Core\Parser\Exception;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3\CMS\Fluid\Core\Parser\TemplateParser;
use TYPO3\CMS\Fluid\Core\Parser\ParsedTemplateInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\ChildNodeAccessInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\PostParseInterface;

/**
 * Class ExposedTemplateParser
 */
class ExposedTemplateParser extends TemplateParser {

	/**
	 * @var array
	 */
	protected $splitTemplate = array();

	/**
	 * @var array
	 */
	protected $viewHelpersUsed = array();

	/**
	 * @return array
	 */
	public function getUniqueViewHelpersUsed() {
		$names = array();
		foreach ($this->viewHelpersUsed as $metadata) {
			list ($namespace, $viewhelper, , ) = array_values($metadata);
			$id = $namespace . ':' . $viewhelper;
			if (FALSE === in_array($id, $names)) {
				$names[] = $id;
			}
		}
		return $names;
	}

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
	 * Initialize the given ViewHelper and adds it to the current node and to
	 * the stack.
	 *
	 * @param ParsingState $state Current parsing state
	 * @param string $namespaceIdentifier Namespace identifier - being looked up in $this->namespaces
	 * @param string $methodIdentifier Method identifier
	 * @param array $argumentsObjectTree Arguments object tree
	 * @return void
	 * @throws Exception
	 */
	protected function initializeViewHelperAndAddItToStack(ParsingState $state, $namespaceIdentifier, $methodIdentifier, $argumentsObjectTree) {
		if (!array_key_exists($namespaceIdentifier, $this->namespaces)) {
			throw new Exception('Namespace could not be resolved. This exception should never be thrown!', 1224254792);
		}
		$viewHelper = $this->objectManager->get($this->resolveViewHelperName($namespaceIdentifier, $methodIdentifier));
		$this->viewHelperNameToImplementationClassNameRuntimeCache[$namespaceIdentifier][$methodIdentifier] = get_class($viewHelper);

		// The following three checks are only done *in an uncached template*, and not needed anymore in the cached version
		$expectedViewHelperArguments = $viewHelper->prepareArguments();
		$this->abortIfUnregisteredArgumentsExist($expectedViewHelperArguments, $argumentsObjectTree);
		$this->abortIfRequiredArgumentsAreMissing($expectedViewHelperArguments, $argumentsObjectTree);
		$this->rewriteBooleanNodesInArgumentsObjectTree($expectedViewHelperArguments, $argumentsObjectTree);

		/** @var ViewHelperNode $currentViewHelperNode */
		$currentViewHelperNode = $this->objectManager->get('TYPO3\\CMS\\Fluid\\Core\\Parser\\SyntaxTree\\ViewHelperNode', $viewHelper, $argumentsObjectTree);

		$state->getNodeFromStack()->addChildNode($currentViewHelperNode);

		if ($viewHelper instanceof ChildNodeAccessInterface && !($viewHelper instanceof CompilableInterface)) {
			$state->setCompilable(FALSE);
		}

		// PostParse Facet
		if ($viewHelper instanceof PostParseInterface) {
			// Don't just use $viewHelper::postParseEvent(...),
			// as this will break with PHP < 5.3.
			call_user_func(array($viewHelper, 'postParseEvent'), $currentViewHelperNode, $argumentsObjectTree, $state->getVariableContainer());
		}

		$this->callInterceptor($currentViewHelperNode, InterceptorInterface::INTERCEPT_OPENING_VIEWHELPER, $state);

		$state->pushNodeToStack($currentViewHelperNode);
		$this->viewHelpersUsed[] = array(
			'namespace' => $namespaceIdentifier,
			'viewhelper' => $methodIdentifier
		);
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
