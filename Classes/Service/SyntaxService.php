<?php
namespace FluidTYPO3\Builder\Service;

use FluidTYPO3\Builder\Utility\GlobUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Core\Parser\TemplateParser;
use FluidTYPO3\Builder\Result\FluidParserResult;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use Exception;

class SyntaxService implements SingletonInterface {

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
	public function injectObjectManager(ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param TemplateParser $templateParser
	 * @return void
	 */
	public function injectTemplateParser(TemplateParser $templateParser) {
		$this->templateParser = $templateParser;
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
	 */
	public function syntaxCheckFluidTemplateFile($filePathAndFilename) {
		/** @var $result FluidParserResult */
		$result = $this->objectManager->get('FluidTYPO3\Builder\Result\FluidParserResult');
		/** @var $context RenderingContext */
		$context = $this->objectManager->get('TYPO3\CMS\Fluid\Core\Rendering\RenderingContext');
		try {
			$parsedTemplate = $this->templateParser->parse(file_get_contents($filePathAndFilename));
			$result->setLayoutName($parsedTemplate->getLayoutName($context));
			$result->setNamespaces($this->templateParser->getNamespaces());
			$result->setCompilable($parsedTemplate->isCompilable());
		} catch (Exception $error) {
			$result->setError($error);
			$result->setValid(FALSE);
		}
		return $result;
	}

	/**
	 * @param string $path
	 * @param string $formats
	 * @return FluidParserResult[]
	 */
	public function syntaxCheckFluidTemplateFilesInPath($path, $formats) {
		$files = GlobUtility::getFilesRecursive($path, $formats);
		$results = array();
		foreach ($files as $filePathAndFilename) {
			$results[$filePathAndFilename] = $this->syntaxCheckFluidTemplateFile($filePathAndFilename);
		}
		return $results;
	}

	/**
	 * @param string $filePathAndFilename
	 * @return ParserResult
	 */
	public function syntaxCheckPhpFile($filePathAndFilename) {
		/** @var $result FluidParserResult */
		$result = $this->objectManager->get('FluidTYPO3\Builder\Result\FluidParserResult');
		$command = 'php --define error_reporting=0 -le ' . $filePathAndFilename;
		$code = $this->executeCommandAndReturnZeroOrStringMessage($command);
		if (0 !== $code) {
			$output = array();
			$this->executeCommandAndReturnZeroOrStringMessage('php -l ' . $filePathAndFilename . ' 2>&1', $output);
			$error = new Exception(array_shift($output), $code);
			$result->setValid(FALSE);
			$result->setError($error);
		}
		return $result;
	}

	/**
	 * @param string $extensionKey
	 * @return ParserResult[]
	 */
	public function syntaxCheckPhpFilesInExtension($extensionKey) {
		$path = ExtensionManagementUtility::extPath($extensionKey);
		return $this->syntaxCheckPhpFilesInPath($path);
	}

	/**
	 * @param string $path
	 * @return ParserResult[]
	 */
	public function syntaxCheckPhpFilesInPath($path) {
		$files = GlobUtility::getFilesRecursive($path, 'php');
		$files = array_values($files);
		$results = array();
		foreach ($files as $filePathAndFilename) {
			$results[$filePathAndFilename] = $this->syntaxCheckPhpFile($filePathAndFilename);
		}
		return $results;
	}

	/**
	 * @param ParserResult[] $results
	 * @return integer
	 */
	public function countErrorsInResultCollection(array $results) {
		$count = 0;
		foreach ($results as $result) {
			if (FALSE === $result->getValid()) {
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
	protected function executeCommandAndReturnZeroOrStringMessage($command, &$output = array()) {
		$code = 0;
		exec($command, $output, $code);
		return $code;
	}

}
