<?php
namespace FluidTYPO3\Builder\CodeGeneration;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

abstract class AbstractCodeGenerator {

	/**
	 * @var boolean
	 */
	protected $verbose = TRUE;

	/**
	 * @var boolean
	 */
	protected $dry = FALSE;

	/**
	 * @var ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @param ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param boolean $dry
	 * @return void
	 */
	public function setDry($dry) {
		$this->dry = $dry;
	}

	/**
	 * @param boolean $verbose
	 * @return void
	 */
	public function setVerbose($verbose) {
		$this->verbose = $verbose;
	}

	/**
	 * @param string $folderPath
	 * @return boolean
	 * @throws Exception
	 */
	public function createFolder($folderPath) {
		if (TRUE === $this->dry) {
			return TRUE;
		}
		$madeDirectory = GeneralUtility::mkdir_deep($folderPath);
		if (FALSE === $madeDirectory) {
			throw new Exception('Unable to create directory "' . $folderPath . '"', 1371692697);
		}
		return TRUE;
	}

	/**
	 * @param string $filePathAndFilename
	 * @param string $content
	 * @return boolean
	 * @throws Exception
	 */
	public function createFile($filePathAndFilename, $content) {
		if (TRUE === $this->dry) {
			return TRUE;
		}
		$folderPath = pathinfo($filePathAndFilename, PATHINFO_DIRNAME);
		if (FALSE === is_dir($folderPath)) {
			$this->createFolder($folderPath);
		}
		$createdFile = \t3lib_div::writeFile($filePathAndFilename, $content);
		if (FALSE === $createdFile) {
			throw new Exception('Unable to create file "' . $filePathAndFilename . '"', 1371695066);
		}
		return TRUE;
	}

	/**
	 * @param string $localRelativePathAndFilename
	 * @param string $destinationPathAndFilename
	 * @return boolean
	 * @throws Exception
	 */
	public function copyFile($localRelativePathAndFilename, $destinationPathAndFilename) {
		if (TRUE === $this->dry) {
			return TRUE;
		}
		$folderPath = pathinfo($destinationPathAndFilename, PATHINFO_DIRNAME);
		if (FALSE === is_dir($folderPath)) {
			$this->createFolder($folderPath);
		}
		$localFile = ExtensionManagementUtility::extPath('builder', $localRelativePathAndFilename);
		$fileCopied = copy($localFile, $destinationPathAndFilename);
		if (FALSE === $fileCopied) {
			throw new Exception('Unable to copy file "' . $localFile . '" to "' . $destinationPathAndFilename . '"', 1371695897);
		}
		return TRUE;
	}

	/**
	 * @param string $filePathAndFilename
	 * @return void
	 */
	public function save($filePathAndFilename) {
		$code = $this->generate();
		$this->createFile($filePathAndFilename, $code);
	}

	/**
	 * @param string $identifier
	 * @param array $variables
	 * @return Tx_Builder_CodeGeneration_CodeTemplate
	 */
	protected function getPreparedCodeTemplate($identifier, $variables) {
		/** @var $template CodeTemplate */
		$template = $this->objectManager->get('FluidTYPO3\Builder\CodeGeneration\CodeTemplate');
		$template->setIdentifier($identifier);
		$template->setVariables($variables);
		return $template;
	}

}
