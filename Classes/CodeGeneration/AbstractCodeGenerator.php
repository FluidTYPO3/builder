<?php
namespace FluidTYPO3\Builder\CodeGeneration;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Class AbstractCodeGenerator
 * @package FluidTYPO3\Builder\CodeGeneration
 */
abstract class AbstractCodeGenerator implements CodeGeneratorInterface {

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
	 * @throws \Exception
	 */
	public function createFolder($folderPath) {
		if (TRUE === $this->dry) {
			return TRUE;
		}
		try {
			GeneralUtility::mkdir_deep($folderPath);
		} catch (\InvalidArgumentException $exception) {
			throw new \Exception('Unable to create directory "' . $folderPath . '"', 1371692697);
		}
		return TRUE;
	}

	/**
	 * @param string $filePathAndFilename
	 * @param string $content
	 * @return boolean
	 * @throws \Exception
	 */
	public function createFile($filePathAndFilename, $content) {
		if (TRUE === $this->dry) {
			return TRUE;
		}
		$folderPath = pathinfo($filePathAndFilename, PATHINFO_DIRNAME);
		if (FALSE === is_dir($folderPath)) {
			$this->createFolder($folderPath);
		}
		$createdFile = GeneralUtility::writeFile($filePathAndFilename, $content);
		if (FALSE === $createdFile) {
			throw new \Exception('Unable to create file "' . $filePathAndFilename . '"', 1371695066);
		}
		return TRUE;
	}

	/**
	 * @param string $localRelativePathAndFilename
	 * @param string $destinationPathAndFilename
	 * @return boolean
	 * @throws \Exception
	 */
	public function copyFile($localRelativePathAndFilename, $destinationPathAndFilename) {
		if (TRUE === $this->dry) {
			return TRUE;
		}
		$folderPath = pathinfo($destinationPathAndFilename, PATHINFO_DIRNAME);
		if (FALSE === is_dir($folderPath)) {
			$this->createFolder($folderPath);
		}
		$localFile = $this->getBuilderExtensionPath() . $localRelativePathAndFilename;
		$fileCopied = copy($localFile, $destinationPathAndFilename);
		if (FALSE === $fileCopied) {
			throw new \Exception('Unable to copy file "' . $localFile . '" to "' . $destinationPathAndFilename . '"', 1371695897);
		}
		return TRUE;
	}

	/**
	 * @return string
	 */
	protected function getBuilderExtensionPath() {
		return rtrim(ExtensionManagementUtility::extPath('builder'), '/') . '/';
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
	 * @return CodeTemplate
	 */
	protected function getPreparedCodeTemplate($identifier, $variables) {
		/** @var CodeTemplate $template */
		$template = $this->objectManager->get('FluidTYPO3\Builder\CodeGeneration\CodeTemplate');
		$template->setPath(ExtensionManagementUtility::extPath('builder', 'Resources/Private/CodeTemplates/'));
		$template->setIdentifier($identifier);
		$template->setVariables($variables);
		return $template;
	}

}
