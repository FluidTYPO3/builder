<?php
namespace FluidTYPO3\Builder\Analysis\Fluid;

use FluidTYPO3\Builder\Parser\ExposedTemplateParser;
use FluidTYPO3\Builder\Parser\ExposedTemplateParserLegacy;
use FluidTYPO3\Builder\Result\ParserResult;
use FluidTYPO3\Flux\Utility\VersionUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Exception;

/**
 * Class TemplateAnalyzer
 */
class TemplateAnalyzer
{

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
    protected $messages = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->nodeCounter = $this->objectManager->get('FluidTYPO3\Builder\Analysis\Fluid\NodeCounter');
    }

    /**
     * @param string $templatePathAndFilename
     * @return ParserResult
     */
    public function analyzePathAndFilename($templatePathAndFilename)
    {
        $templateString = file_get_contents($templatePathAndFilename);
        return $this->analyze($templateString);
    }

    /**
     * @param string $templateString
     * @return ParserResult
     */
    public function analyze($templateString)
    {
        /** @var ExposedTemplateParser $parser */
        $parser = $this->getTemplateParser();
        $result = new ParserResult();
        try {
            $parsedTemplate = $parser->parse($templateString);
            $metrics = $this->nodeCounter->count($parser, $parsedTemplate);
            $this->messages = $this->nodeCounter->getMessages();
            $result->setViewHelpers($parser->getUniqueViewHelpersUsed());
            $result->setPayload($metrics);
            $result->setValid(true);
            $result->setPayloadType(ParserResult::PAYLOAD_METRICS);
        } catch (Exception $error) {
            $result->setValid(false);
        }
        $this->parser = $parser;
        return $result;
    }

    /**
     * @return ExposedTemplateParser
     */
    protected function getTemplateParser()
    {
        $exposedTemplateParser = new ExposedTemplateParser();
        $exposedTemplateParser->setRenderingContext(new RenderingContext());
        return $exposedTemplateParser;
    }
}
