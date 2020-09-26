<?php
namespace FluidTYPO3\Builder\Parser;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Parser\ParsingState;
use TYPO3Fluid\Fluid\Core\Parser\Exception;
use TYPO3Fluid\Fluid\Core\Parser\TemplateParser;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class ExposedTemplateParser extends TemplateParser
{
    /**
     * @var array
     */
    protected $splitTemplate = [];

    /**
     * @var array
     */
    protected $viewHelpersUsed = [];

    /**
     * @return void
     */
    public function initializeObject()
    {
        $this->setRenderingContext(GeneralUtility::makeInstance(ObjectManager::class)->get(RenderingContext::class));
    }

    /**
     * @return array
     */
    public function getUniqueViewHelpersUsed()
    {
        $names = [];
        foreach ($this->viewHelpersUsed as $metadata) {
            list ($namespace, $viewhelper, , ) = array_values($metadata);
            $id = $namespace . ':' . $viewhelper;
            if (false === in_array($id, $names)) {
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
     * @param string|null $templateIdentifier If the template has an identifying string it can be passed here to improve error reporting.
     * @return ParsingState Parsed template
     * @throws Exception
     */
    public function parse($templateString, $templateIdentifier = null)
    {
        if (!is_string($templateString)) {
            throw new Exception('Parse requires a template string as argument, ' . gettype($templateString) . ' given.', 1224237899);
        }
        try {
            $this->reset();

            $templateString = $this->preProcessTemplateSource($templateString);

            $splitTemplate = $this->splitTemplate = $this->splitTemplateAtDynamicTags($templateString);
            $parsingState = $this->buildObjectTree($splitTemplate, self::CONTEXT_OUTSIDE_VIEWHELPER_ARGUMENTS);
        } catch (Exception $error) {
            throw $this->createParsingRelatedExceptionWithContext($error, $templateIdentifier);
        }
        $this->parsedTemplates[$templateIdentifier] = $parsingState;
        return $parsingState;
    }

    /**
     * @return RenderingContextInterface
     */
    public function getRenderingContext()
    {
        return $this->renderingContext;
    }

    /**
     * Initialize the given ViewHelper and adds it to the current node and to
     * the stack.
     *
     * @param ParsingState $state Current parsing state
     * @param string $namespaceIdentifier Namespace identifier - being looked up in $this->namespaces
     * @param string $methodIdentifier Method identifier
     * @param array $argumentsObjectTree Arguments object tree
     * @return ViewHelperNode]null
     * @throws Exception
     */
    protected function initializeViewHelperAndAddItToStack(
        ParsingState $state,
        $namespaceIdentifier,
        $methodIdentifier,
        $argumentsObjectTree
    ) {
        $this->viewHelpersUsed[] = [
            'namespace' => $namespaceIdentifier,
            'viewhelper' => $methodIdentifier
        ];
        return parent::initializeViewHelperAndAddItToStack($state, $namespaceIdentifier, $methodIdentifier, $argumentsObjectTree);
    }

    /**
     * @param array $splitTemplate
     * @param integer $context
     * @return ParsingState
     */
    public function buildObjectTree(array $splitTemplate, $context)
    {
        return parent::buildObjectTree($splitTemplate, $context);
    }

    /**
     * @return array
     */
    public function getSplitTemplate()
    {
        return $this->splitTemplate;
    }
}
