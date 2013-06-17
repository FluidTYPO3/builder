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
	public function fluidSyntaxCommand($extension = NULL, $path = NULL, $extensions = 'html,xml,txt', $verbose = FALSE) {
		$verbose = (boolean) $verbose;
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
			} elseif (TRUE === $verbose) {
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
		$verbose = (boolean) $verbose;
		$this->assertEitherExtensionKeyOrPathOrBothAreProvidedOrExit($extension, $path);
		$path = Tx_Builder_Utility_GlobUtility::getRealPathFromExtensionKeyAndPath($extension, $path);
		$files = Tx_Builder_Utility_GlobUtility::getFilesRecursive($path, 'php');
		$errors = FALSE;
		foreach ($files as $filePathAndFilename) {
			$result = $this->syntaxService->syntaxCheckPhpFile($filePathAndFilename);
			if (NULL !== $result->getError()) {
				$errors = TRUE;
				$this->response->setContent('[ERROR] ' . $result->getError()->getMessage() . ' (' . $result->getError()->getCode() . ')' . LF);
			}

		}
		$this->stop($files, $errors, $verbose);
	}

	/**
	 * Makes simple VH unit test class(es)
	 *
	 * Saves a file only if it does not already exist. Which means
	 * if you have to rebuild your files, remove the old ones first.
	 *
	 * If an extension key is provided but no class name, every
	 * ViewHelper in the provided extension is considered.
	 *
	 * The default location of generated test case classes is:
	 *
	 * EXT:<ext>/Tests/Unit/ViewHelpers/<class>Test.php
	 *
	 * Where <ext> is the extension key detected from the class name
	 * and <class> is the last part of the ViewHelper class filename
	 * relative to Classes/ViewHelpers directory and ".php" stripped.
	 *
	 * @param string $extension The extension key, if class is not used
	 * @param string $class The class name, if extension key is not used
	 * @param string $author The author to be set in the class doc comment
	 * @param boolean $overwrite If TRUE, allows existing files to be overridden - USE CAUTION!
	 * @param boolean $dry If TRUE, performs a dry run and reports files that would change
	 * @param boolean $verbose If TRUE, outputs more information about actions taken
	 * @return void
	 */
	public function unitViewHelperCommand($extension = NULL, $class = NULL, $author = NULL, $overwrite = FALSE, $dry = FALSE, $verbose = FALSE) {
		$dry = (boolean) $dry;
		$verbose = (boolean) $verbose;
		$overwrite = (boolean) $overwrite;
		if (NULL === $extension && NULL === $class) {
			$this->response->setContent('Either "extension" or "class" or both must be specified' . LF);
			$this->response->send();
			$this->response->setExitCode(255);
			$this->forward('error');
		}
		if (NULL !== $class) {
			$classes = array($class);
		} else {
			$classes = $this->getClassNamesInExtension($extension);
		}
		foreach ($classes as $class) {
			$classNameSeparator = FALSE === strpos($class, '_') ? '\\' : '_';
			$parts = explode($classNameSeparator, $class);
			foreach ($parts as $index => $part) {
				unset($parts[$index]);
				if ('ViewHelpers' === $part) {
					break;
				}
			}
			$targetPathAndFilename = 'EXT:' . $extension . '/Tests/Unit/ViewHelpers/' . implode('/', $parts) . 'Test.php';
			/** @var $classCodeGenerator Tx_Builder_CodeGeneration_Testing_ViewHelperTestCaseGenerator */
			$classCodeGenerator = $this->objectManager->get('Tx_Builder_CodeGeneration_Testing_ViewHelperTestCaseGenerator');
			$classCodeGenerator->setViewHelperClassName($class);
			$classCodeGenerator->setAuthor($author);
			$classCodeGenerator->setPackage(\t3lib_div::underscoredToUpperCamelCase($extension));
			$code = $classCodeGenerator->generate();
			if (TRUE === $dry) {
				if (TRUE === $verbose) {
					$this->response->appendContent('Would generate ViewHelper test class: ');
					$this->response->appendContent("\n\t" . $class . 'Test');
					$this->response->appendContent("\n\t" . $targetPathAndFilename);
					$this->response->appendContent("\n\t" . '(' . strlen($code) . ' bytes would be written)');
					$this->response->appendContent(LF . LF);
				}
				continue;
			}
			$absoluteTargetPathAndFilename = \t3lib_div::getFileAbsFileName($targetPathAndFilename);
			$directory = pathinfo($absoluteTargetPathAndFilename, PATHINFO_DIRNAME);
			if (FALSE === is_dir($directory)) {
				$createdDirectory = mkdir($directory, 0775, TRUE);
				if (FALSE === $createdDirectory) {
					$this->response->setContent('Could not create directory ' . $directory . ' - insufficient permissions?' . LF);
					$this->response->send();
					$this->response->setExitCode(1024);
					return;
				}
			}
			if (TRUE === file_exists($absoluteTargetPathAndFilename) && TRUE === $overwrite) {
				\t3lib_div::writeFile($absoluteTargetPathAndFilename, $code);
			} elseif (FALSE === file_exists($absoluteTargetPathAndFilename) || 0 === filesize($absoluteTargetPathAndFilename)) {
				\t3lib_div::writeFile($absoluteTargetPathAndFilename, $code);
			}
		}
	}

	/**
	 * Installs an extension by key
	 *
	 * The extension files must be present in one of the
	 * recognised extension folder paths in TYPO3.
	 *
	 * @param string $extensionKey
	 * @return void
	 * @throws \Exception
	 */
	public function installCommand($extensionKey) {
		if (6 > substr(TYPO3_version, 0, 1)) {
			throw new \Exception('Installing/uninstalling extensions only works on 6.0+ currently', 1371468427);
		}
		/** @var $service \TYPO3\CMS\Extensionmanager\Utility\InstallUtility */
		$service = $this->objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
		$service->install($extensionKey);
	}

	/**
	 * Uninstalls an extension by key
	 *
	 * The extension files must be present in one of the
	 * recognised extension folder paths in TYPO3.
	 *
	 * @param string $extensionKey
	 * @return void
	 * @throws \Exception
	 */
	public function uninstallCommand($extensionKey) {
		if (6 > substr(TYPO3_version, 0, 1)) {
			throw new \Exception('Installing/uninstalling extensions only works on 6.0+ currently', 1371468427);
		}
		/** @var $service \TYPO3\CMS\Extensionmanager\Utility\InstallUtility */
		$service = $this->objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
		$service->uninstall($extensionKey);
	}

	/**
	 * Black hole
	 *
	 * @return void
	 */
	protected function errorCommand() {

	}

	/**
	 * @param string $extension
	 * @param string $path
	 * @return void
	 */
	private function assertEitherExtensionKeyOrPathOrBothAreProvidedOrExit($extension, $path) {
		if (NULL === $extension && NULL === $path) {
			$this->response->setContent('Either "extension" or "path" or both must be specified' . LF);
			$this->response->send();
			$this->response->setExitCode(128);
			$this->forward('error');
		}
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

	/**
	 * Get all class names inside this namespace and return them as array.
	 *
	 * @param string $combinedExtensionKey Extension Key with (possibly) leading Vendor Prefix
	 * @return array
	 */
	protected function getClassNamesInExtension($combinedExtensionKey) {
		$allViewHelperClassNames = array();
		list ($vendor, $extensionKey) = $this->getRealExtensionKeyAndVendorFromCombinedExtensionKey($combinedExtensionKey);
		$path = t3lib_extMgm::extPath($extensionKey, 'Classes/ViewHelpers/');
		$filesInPath = t3lib_div::getAllFilesAndFoldersInPath(array(), $path, 'php');
		foreach ($filesInPath as $filePathAndFilename) {
			$className = $this->getRealClassNameBasedOnExtensionAndFilenameAndExistence($combinedExtensionKey, $filePathAndFilename);
			if (class_exists($className)) {
				$parent = $className;
				while ($parent = get_parent_class($parent)) {
					if ($parent === 'Tx_Fluid_Core_ViewHelper_AbstractViewHelper' || $parent === 'TYPO3\\CMS\\Fluid\Core\\ViewHelper\\AbstractViewHelper') {
						array_push($allViewHelperClassNames, $className);
					}
				}
			}
		}
		$affectedViewHelperClassNames = array();
		foreach ($allViewHelperClassNames as $viewHelperClassName) {
			$classReflection = new ReflectionClass($viewHelperClassName);
			if ($classReflection->isAbstract() === TRUE) {
				continue;
			}
			if (strncmp($namespace, $viewHelperClassName, strlen($namespace)) === 0) {
				$affectedViewHelperClassNames[] = $viewHelperClassName;
			}
		}
		sort($affectedViewHelperClassNames);
		return $affectedViewHelperClassNames;
	}

	/**
	 * Returns the true class name of the ViewHelper as defined
	 * by the extensionKey (which may be vendorname.extensionkey)
	 * and the class name. If vendorname is used, namespaced
	 * classes are assumed. If no vendorname is used a namespaced
	 * class is first attempted, if this does not exist the old
	 * Tx_ prefixed class name is tried. If this too does not exist,
	 * an Exception is thrown.
	 *
	 * @param string $combinedExtensionKey
	 * @param string $filename
	 * @return string
	 * @throws Exception
	 */
	protected function getRealClassNameBasedOnExtensionAndFilenameAndExistence($combinedExtensionKey, $filename) {
		list ($vendor, $extensionKey) = $this->getRealExtensionKeyAndVendorFromCombinedExtensionKey($combinedExtensionKey);
		$filename = str_replace(\t3lib_extMgm::extPath($extensionKey, 'Classes/ViewHelpers/'), '', $filename);
		$stripped = substr($filename, 0, -4);
		if ($vendor) {
			$classNamePart = str_replace('/', '\\', $stripped);
			$className = $vendor . '\\' . ucfirst(\t3lib_div::underscoredToLowerCamelCase($extensionKey)) . '\\ViewHelpers\\' . $classNamePart;
		} else {
			$classNamePart = str_replace('/', '_', $stripped);
			$className = 'Tx_' . ucfirst(\t3lib_div::underscoredToLowerCamelCase($extensionKey)) . '_ViewHelpers_' . $classNamePart;
		}
		return $className;
	}

	/**
	 * @param string $extensionKey
	 * @return array
	 */
	protected function getRealExtensionKeyAndVendorFromCombinedExtensionKey($extensionKey) {
		if (FALSE !== strpos($extensionKey, '.')) {
			list ($vendor, $extensionKey) = explode('.', $extensionKey);
			if ('TYPO3' === $vendor) {
				$vendor = 'TYPO3\\CMS';
			}
		} else {
			$vendor = NULL;
		}
		$extensionKey = strtolower($extensionKey);
		return array($vendor, $extensionKey);
	}

}
