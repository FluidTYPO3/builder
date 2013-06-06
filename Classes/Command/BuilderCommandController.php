<?php

class Tx_Builder_Command_BuilderCommandController extends Tx_Extbase_MVC_Controller_CommandController {

	/**
	 * @var Tx_Builder_Service_SyntaxService
	 */
	protected $syntaxService;

	/**
	 * @param Tx_Builder_Service_SyntaxService $syntaxService
	 * @return void
	 */
	public function injectSyntaxService(Tx_Builder_Service_SyntaxService $syntaxService) {
		$this->syntaxService = $syntaxService;
	}

	/**
	 * Syntax check Fluid template
	 *
	 * Checks one template file, all templates in
	 * an extension or a sub-path (which can be used
	 * with an extension key for a relative path)
	 *
	 * @param string $extension Optional extension key (if path is included, only files in that path in this extension are checked)
	 * @param string $path file or folder path (if extensionKey is included, path is relative to this extension)
	 * @param string $extensions If provided, this CSV list of file extensions are considered Fluid templates
	 * @param boolean $verbose If TRUE, outputs more information about each file check - default is to only output errors
	 * @return void
	 */
	public function templateSyntaxCommand($extension = NULL, $path = NULL, $extensions = 'html,xml,txt', $verbose = FALSE) {
		$this->assertEitherExtensionKeyOrPathOrBothAreProvidedOrExit($extension, $path);
		$path = Tx_Builder_Utility_GlobUtility::getRealPathFromExtensionKeyAndPath($extension, $path);
		$files = Tx_Builder_Utility_GlobUtility::getFilesRecursive($path, $extensions);
		$files = array_values($files);
		$errors = FALSE;
		$this->response->setContent('Performing a syntax check on fluid templates (types: ' . $extensions . '; path: ' . $path . ')' . LF);
		$this->response->send();
		foreach ($files as $filePathAndFilename) {
			$basePath = str_replace(PATH_site, '', $filePathAndFilename);
			$result = $this->syntaxService->syntaxCheckFluidTemplateFile($filePathAndFilename);
			if (NULL !== $result->getError()) {
				$this->response->appendContent('[ERROR] File ' . $basePath . ' has an error: ' . LF);
				$this->response->appendContent($result->getError()->getMessage() . ' (' . $result->getError()->getCode() . ')' . LF);
				$this->response->send();
				$errors = TRUE;
			} elseif (TRUE === (boolean) $verbose) {
				$namespaces = $result->getNamespaces();
				$this->response->appendContent('File is compilable: ' . (TRUE === $result->getCompilable() ? 'YES' : 'NO (WARNING)') . LF);
				$this->response->appendContent('File ' . (NULL !== $result->getLayoutName() ? 'has layout (' . $result->getLayoutName() . ')' : 'DOES NOT reference a Layout') . LF);
				$this->response->appendContent('File has ' . count($namespaces) . ' namespace(s)' . (0 < count($namespaces) ? ': ' . $result->getNamespacesFlattened() : ''). LF);
				$this->response->appendContent('[OK] File  ' . $basePath . ' is valid.' . LF);
				$this->response->send();
			}
			$this->response->setContent(LF);
		}
		$this->stop($files, $errors, $verbose);
	}

	/**
	 * Syntax check PHP code
	 *
	 * Checks PHP source files in $path, if extension
	 * key is also given, only files in that path relative
	 * to that extension are checked.
	 *
	 * @param string $extension Optional extension key (if path is included, only files in that path in this extension are checked)
	 * @param string $path file or folder path (if extensionKey is included, path is relative to this extension)
	 * @param boolean $verbose If TRUE, outputs more information about each file check - default is to only output errors
	 * @return void
	 */
	public function phpsyntaxCommand($extension = NULL, $path = NULL, $verbose = FALSE) {
		$this->assertEitherExtensionKeyOrPathOrBothAreProvidedOrExit($extension, $path);
		$path = Tx_Builder_Utility_GlobUtility::getRealPathFromExtensionKeyAndPath($extension, $path);
		$files = Tx_Builder_Utility_GlobUtility::getFilesRecursive($path, 'php');
		$errors = FALSE;
		foreach ($files as $filePathAndFilename) {
			$result = $this->syntaxService->syntaxCheckPhpFile($filePathAndFilename);
			if (NULL !== $result->getError()) {
				$errors = TRUE;
				$this->response->setContent('[ERROR] ' . $result->getError()->getMessage() . ' (' . $result->getError()->getCode() . ')' . LF);
			} elseif (TRUE === (boolean) $verbose) {

			}

		}
		$this->stop($files, $errors, $verbose);
	}

	/**
	 * @param string $extension
	 * @param string $path
	 * @return void
	 */
	private function assertEitherExtensionKeyOrPathOrBothAreProvidedOrExit($extension, $path) {
		if (NULL === $extension && NULL === $path) {
			$this->response->setContent('Either "extensionKey" or "path" or both must be specified' . LF);
			$this->response->send();
			$this->response->setExitCode(128);
			$this->forward('error');
		}
	}

	/**
	 * Black hole
	 *
	 * @return void
	 */
	protected function errorCommand() {

	}

	/**
	 * @param array $files
	 * @param boolean  $errors
	 * @param boolean $verbose
	 */
	protected function stop($files, $errors, $verbose) {
		if (TRUE === (boolean) $verbose) {
			if (FALSE === $errors) {
				$this->response->setContent('No errors encountered - ' . count($files) . ' file(s) are all okay' . LF);
			} else {
				$this->response->setContent('Errors were detected - review the summary above' . LF);
				$this->response->setExitCode(1);
			}
		}
		$this->response->send();
	}

}