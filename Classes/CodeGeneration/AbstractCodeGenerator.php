<?php

abstract class Tx_Builder_CodeGeneration_AbstractCodeGenerator {

	/**
	 * @var boolean
	 */
	protected $verbose = TRUE;

	/**
	 * @var boolean
	 */
	protected $dry = FALSE;

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
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
		$madeDirectory = t3lib_div::mkdir_deep($folderPath);
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
		$createdFile = t3lib_div::writeFile($filePathAndFilename, $content);
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
		$localFile = t3lib_extMgm::extPath('builder', $localRelativePathAndFilename);
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
		/** @var $template Tx_Builder_CodeGeneration_CodeTemplate */
		$template = $this->objectManager->get('Tx_Builder_CodeGeneration_CodeTemplate');
		$template->setIdentifier($identifier);
		$template->setVariables($variables);
		return $template;
	}

}
