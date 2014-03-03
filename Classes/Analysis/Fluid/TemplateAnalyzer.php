<?php
namespace FluidTYPO3\Builder\Analysis\Fluid;

use FluidTYPO3\Builder\Parser\ExposedTemplateParser;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use FluidTYPO3\Builder\Result\ParserResult;

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
	public function analyze($templatePathAndFilename) {
		$templateString = file_get_contents($templatePathAndFilename);
		/** @var ExposedTemplateParser $parser */
		$parser = $this->objectManager->get('FluidTYPO3\Builder\Parser\ExposedTemplateParser');
		$parsedTemplate = $parser->parse($templateString);
		$metrics = $this->nodeCounter->count($parser, $parsedTemplate);
		$this->messages = $this->nodeCounter->getMessages();
		$result = new ParserResult();
		$result->setPayload($metrics);
		$result->setValid(TRUE);
		$result->setPayloadType(ParserResult::PAYLOAD_METRICS);
		return $result;
	}

}
