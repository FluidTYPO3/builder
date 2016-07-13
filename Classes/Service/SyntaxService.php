<?php
namespace FluidTYPO3\Builder\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Claus Due <claus@namelesscoder.net>
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
use FluidTYPO3\Builder\Parser\ExposedTemplateParserLegacy;
use FluidTYPO3\Builder\Result\FluidParserResult;
use FluidTYPO3\Builder\Utility\GlobUtility;
use FluidTYPO3\Flux\Utility\VersionUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class SyntaxService
 * @package FluidTYPO3\Builder\Service
 */
class SyntaxService implements SingletonInterface
{

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var TemplateParser
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
        $result = $this->objectManager->get('FluidTYPO3\Builder\Result\FluidParserResult');
        /** @var RenderingContext $context */
        $context = $this->objectManager->get('TYPO3\CMS\Fluid\Core\Rendering\RenderingContext');
        try {
            $parser = $this->getTemplateParser($context);
            $parsedTemplate = $parser->parse(file_get_contents($filePathAndFilename));
            $result->setLayoutName($parsedTemplate->getLayoutName($context));
            $result->setNamespaces($parser->getNamespaces());
            $result->setCompilable($parsedTemplate->isCompilable());
        } catch (\Exception $error) {
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
        foreach ($files as $filePathAndFilename) {
            $results[$filePathAndFilename] = $this->syntaxCheckFluidTemplateFile($filePathAndFilename);
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
        if (VersionUtility::assertExtensionVersionIsAtLeastVersion('core', 8)) {
            $exposedTemplateParser = new ExposedTemplateParser();
            $exposedTemplateParser->setRenderingContext($renderingContext);
        } else {
            $exposedTemplateParser = new ExposedTemplateParserLegacy();
        }
        return $exposedTemplateParser;
    }
}
