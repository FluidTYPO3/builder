<?php
namespace FluidTYPO3\Builder\Parser;

use TYPO3\CMS\Fluid\Core\Parser\Exception;
use TYPO3\CMS\Fluid\Core\Parser\TemplateParser;

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
	 * @return \TYPO3\CMS\Fluid\Core\Parser\ParsedTemplateInterface Parsed template
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
	 * @return \TYPO3\CMS\Fluid\Core\Parser\ParsingState
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
