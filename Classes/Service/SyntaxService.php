<?php
namespace FluidTYPO3\Builder\Service;

use FluidTYPO3\Builder\Parser\ExposedTemplateParser;
use FluidTYPO3\Builder\Result\FluidParserResult;
use FluidTYPO3\Builder\Utility\GlobUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class SyntaxService implements SingletonInterface
{

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ExposedTemplateParser
     */
    protected $templateParser;

    /**
     * @param ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Syntax checks a Fluid template file by attempting
     * to load the file and retrieve a parsed template, which
     * will cause traversal of the entire syntax node tree
     * and report any errors about missing or unknown arguments.
     *
     * Will NOT, however, report errors which are caused by
     * variables assigned to the template (there will be no
     * variables while building the syntax tree and listening
     * for errors).
     *
     * @param string $filePathAndFilename
     * @return FluidParserResult
     * @throws \Exception
     */
    public function syntaxCheckFluidTemplateFile($filePathAndFilename)
    {
        /** @var FluidParserResult $result */
        $result = $this->objectManager->get(FluidParserResult::class);
        try {
            $parser = $this->objectManager->get(ExposedTemplateParser::class);
            $context = $parser->getRenderingContext();
            $parsedTemplate = $parser->parse(file_get_contents($filePathAndFilename));
            $result->setLayoutName($parsedTemplate->getLayoutName($context));
            $result->setNamespaces($context->getViewHelperResolver()->getNamespaces());
            $result->setCompilable($parsedTemplate->isCompilable());
        } catch (\TYPO3Fluid\Fluid\Core\Parser\Exception $error) {
            $result->setError($error);
            $result->setValid(false);
        }
        return $result;
    }

    /**
     * @param string $path
     * @param string $formats
     * @return FluidParserResult[]
     */
    public function syntaxCheckFluidTemplateFilesInPath($path, $formats)
    {
        $files = GlobUtility::getFilesRecursive($path, $formats);
        $results = [];
        $pathLength = strlen($path);
        foreach ($files as $filePathAndFilename) {
            $shortFilename = substr($filePathAndFilename, $pathLength);
            $results[$shortFilename] = $this->syntaxCheckFluidTemplateFile($filePathAndFilename);
        }
        return $results;
    }

    /**
     * @param string $filePathAndFilename
     * @return FluidParserResult
     * @throws \Exception
     */
    public function syntaxCheckPhpFile($filePathAndFilename)
    {
        /** @var FluidParserResult $result */
        $result = $this->objectManager->get('FluidTYPO3\Builder\Result\FluidParserResult');
        $command = 'php --define error_reporting=0 -le ' . $filePathAndFilename;
        $code = $this->executeCommandAndReturnZeroOrStringMessage($command);
        if (0 !== $code) {
            $output = [];
            $this->executeCommandAndReturnZeroOrStringMessage('php -l ' . $filePathAndFilename . ' 2>&1', $output);
            $error = new \Exception(array_shift($output), $code);
            $result->setValid(false);
            $result->setError($error);
        }
        return $result;
    }

    /**
     * @param string $extensionKey
     * @return FluidParserResult[]
     */
    public function syntaxCheckPhpFilesInExtension($extensionKey)
    {
        $path = ExtensionManagementUtility::extPath($extensionKey);
        return $this->syntaxCheckPhpFilesInPath($path);
    }

    /**
     * @param string $path
     * @return FluidParserResult[]
     */
    public function syntaxCheckPhpFilesInPath($path)
    {
        $files = GlobUtility::getFilesRecursive($path, 'php');
        $files = array_values($files);
        $results = [];
        foreach ($files as $filePathAndFilename) {
            $results[$filePathAndFilename] = $this->syntaxCheckPhpFile($filePathAndFilename);
        }
        return $results;
    }

    /**
     * @param FluidParserResult[] $results
     * @return integer
     */
    public function countErrorsInResultCollection(array $results)
    {
        $count = 0;
        foreach ($results as $result) {
            if (false === $result->getValid()) {
                ++ $count;
            }
        }
        return $count;
    }

    /**
     * @param string $command
     * @param array $output
     * @return integer
     */
    protected function executeCommandAndReturnZeroOrStringMessage($command, &$output = [])
    {
        $code = 0;
        exec($command, $output, $code);
        return $code;
    }

    /**
     * @param RenderingContextInterface $renderingContext
     * @return ExposedTemplateParser
     */
    protected function getTemplateParser(RenderingContextInterface $renderingContext)
    {
        if (version_compare(ExtensionManagementUtility::getExtensionVersion('core'), 8, '>')) {
            $exposedTemplateParser = new ExposedTemplateParser();
            $exposedTemplateParser->setRenderingContext($renderingContext);
        } else {
            $exposedTemplateParser = new ExposedTemplateParserLegacy();
        }
        return $exposedTemplateParser;
    }
}
