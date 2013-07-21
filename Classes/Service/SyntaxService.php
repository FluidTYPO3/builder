<?php

class Tx_Builder_Service_SyntaxService implements t3lib_Singleton {

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var Tx_Fluid_Core_Parser_TemplateParser
	 */
	protected $templateParser;

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param Tx_Fluid_Core_Parser_TemplateParser $templateParser
	 * @return void
	 */
	public function injectTemplateParser(Tx_Fluid_Core_Parser_TemplateParser $templateParser) {
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
	 * @return Tx_Builder_Result_FluidParserResult
	 */
	public function syntaxCheckFluidTemplateFile($filePathAndFilename) {
		/** @var $result Tx_Builder_Result_FluidParserResult */
		$result = $this->objectManager->get('Tx_Builder_Result_FluidParserResult');
		/** @var $context Tx_Fluid_Core_Rendering_RenderingContext */
		$context = $this->objectManager->get('Tx_Fluid_Core_Rendering_RenderingContext');
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
	 * @return Tx_Builder_ResultFluidParserResult[]
	 */
	public function syntaxCheckFluidTemplateFilesInPath($path, $formats) {
		$files = Tx_Builder_Utility_GlobUtility::getFilesRecursive($path, $formats);
		$results = array();
		foreach ($files as $filePathAndFilename) {
			$results[$filePathAndFilename] = $this->syntaxCheckFluidTemplateFile($filePathAndFilename);
		}
		return $results;
	}

	/**
	 * @param string $filePathAndFilename
	 * @return Tx_Builder_Result_ParserResult
	 */
	public function syntaxCheckPhpFile($filePathAndFilename) {
		/** @var $result Tx_Builder_Result_FluidParserResult */
		$result = $this->objectManager->get('Tx_Builder_Result_FluidParserResult');
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
	 * @param string $path
	 * @return Tx_Builder_Result_ParserResult[]
	 */
	public function syntaxCheckPhpFilesInPath($path) {
		$files = Tx_Builder_Utility_GlobUtility::getFilesRecursive($path, 'php');
		$files = array_values($files);
		$results = array();
		foreach ($files as $filePathAndFilename) {
			$results[$filePathAndFilename] = $this->syntaxCheckPhpFile($filePathAndFilename);
		}
		return $results;
	}

	/**
	 * @param Tx_Builder_Result_ParserResult[] $results
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
