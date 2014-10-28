<?php
namespace FluidTYPO3\Builder\CodeGeneration\Extension;
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

use FluidTYPO3\Builder\CodeGeneration\AbstractCodeGenerator;
use FluidTYPO3\Builder\CodeGeneration\CodeGeneratorInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class ExtensionGenerator
	extends AbstractCodeGenerator
	implements CodeGeneratorInterface {

	const TEMPLATE_EXTTABLES = 'Extension/ext_tables';
	const TEMPLATE_EMCONF = 'Extension/ext_emconf';
	const TEMPLATE_LAYOUT = 'Fluid/Layout';
	const TEMPLATE_CONTENT = 'Fluid/FluxContent';
	const TEMPLATE_PAGE = 'Fluid/FluxPage';
	const TEMPLATE_FLUXFORM = 'Fluid/FluxForm';
	const TEMPLATE_TYPOSCRIPTCONSTANTS = 'Extension/TypoScript/constants';
	const TEMPLATE_TYPOSCRIPTSETUP = 'Extension/TypoScript/setup';

	/**
	 * @var array
	 */
	private $configuration = array();

	/**
	 * @var string
	 */
	private $targetFolder = NULL;

	/**
	 * @param array $configuration
	 * @return void
	 */
	public function setConfiguration(array $configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * @param string $targetFolder
	 * @return void
	 */
	public function setTargetFolder($targetFolder) {
		$this->targetFolder = $targetFolder;
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function generate() {
		$extensionKey = $this->configuration['extensionKey'];
		if (NULL === $this->targetFolder) {
			$this->setTargetFolder(PATH_typo3conf . 'ext/' . $extensionKey);
		}
		if (TRUE === is_dir($this->targetFolder)) {
			throw new \Exception('Extension key "' . $extensionKey . '" already has a folder in "' . $this->targetFolder . '"', 1371692599);
		}
		$filesToBeWritten = array(
			$this->targetFolder . '/ext_emconf.php' => $this->getPreparedCodeTemplate(self::TEMPLATE_EMCONF, $this->configuration)->render()
		);
		$foldersToBeCreated = array($this->targetFolder);
		if (TRUE === in_array('fluidpages', $this->configuration['dependencies'])) {
			$this->appendPageFiles($filesToBeWritten);
			array_push($foldersToBeCreated, $this->targetFolder . '/Resources/Public/Icons');
		}
		if (TRUE === in_array('fluidcontent', $this->configuration['dependencies'])) {
			$this->appendContentFiles($filesToBeWritten);
		}
		if (TRUE === in_array('fluidbackend', $this->configuration['dependencies'])) {
			$this->appendBackendFiles($filesToBeWritten);
		}
		if (TRUE === $this->configuration['controllers']) {
			array_push($foldersToBeCreated, $this->targetFolder . '/Classes/Controller');
		}
		$this->appendTypoScriptConfiguration($filesToBeWritten);
		$this->appendExtensionTablesFile($filesToBeWritten);
		$foldersToBeCreated = array_unique($foldersToBeCreated);
		foreach ($foldersToBeCreated as $folderPathToBeCreated) {
			$this->createFolder($folderPathToBeCreated);
		}
		foreach ($filesToBeWritten as $fileToBeWritten => $fileContentToBeWritten) {
			$this->createFile($fileToBeWritten, $fileContentToBeWritten);
		}
		if (TRUE === in_array('fluidpages', $this->configuration['dependencies'])) {
			array_push($foldersToBeCreated, $this->targetFolder . '/Resources/Public/Icons/Page');
			$this->copyFile('ext_icon.gif', $this->targetFolder . '/Resources/Public/Icons/Page/Standard.gif');
		}
		if (TRUE === in_array('fluidcontent', $this->configuration['dependencies'])) {
			array_push($foldersToBeCreated, $this->targetFolder . '/Resources/Public/Icons/Content');
			$this->copyFile('ext_icon.gif', $this->targetFolder . '/Resources/Public/Icons/Content/Example.gif');
		}
		$this->copyFile('ext_icon.gif', $this->targetFolder . '/ext_icon.gif');
		return 'Built extension "' . $extensionKey . '"';
	}

	/**
	 * @param array $files
	 * @return void
	 */
	protected function appendTypoScriptConfiguration(&$files) {
		$templateVariables = array(
			'extension' => $this->configuration['extensionKey'],
			'signature' => ExtensionManagementUtility::getCN($this->configuration['extensionKey'])
		);
		$folder = $this->targetFolder . '/Configuration/TypoScript';
		$files[$folder . '/constants.txt'] = $this->getPreparedCodeTemplate(self::TEMPLATE_TYPOSCRIPTCONSTANTS, $templateVariables)->render();
		$files[$folder . '/setup.txt'] = $this->getPreparedCodeTemplate(self::TEMPLATE_TYPOSCRIPTSETUP, $templateVariables)->render();
	}

	/**
	 * @param array $files
	 * @return void
	 */
	protected function appendExtensionTablesFile(&$files) {
		$title = trim($this->configuration['title']);
		$templateVariables = array(
			'configuration' => 'TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, \'Configuration/TypoScript\', \'' .  $title . '\');',
			'pages' => '',
			'content' => '',
			'backend' => ''
		);
		if (TRUE === in_array('fluidpages', $this->configuration['dependencies'])) {
			$templateVariables['pages'] = '\FluidTYPO3\Flux\Core::registerProviderExtensionKey(\'' . $this->configuration['extensionKey'] . '\', \'Page\');';
		}
		if (TRUE === in_array('fluidcontent', $this->configuration['dependencies'])) {
			$templateVariables['content'] = '\FluidTYPO3\Flux\Core::registerProviderExtensionKey(\'' . $this->configuration['extensionKey'] . '\', \'Content\');';
		}
		if (TRUE === in_array('fluidbackend', $this->configuration['dependencies'])) {
			$templateVariables['backend'] = '\FluidTYPO3\Flux\Core::registerProviderExtensionKey(\'' . $this->configuration['extensionKey'] . '\', \'Backend\');';
		}
		$files[$this->targetFolder . '/ext_tables.php'] = $this->getPreparedCodeTemplate(self::TEMPLATE_EXTTABLES, $templateVariables)->render();
	}

	/**
	 * @param array $files
	 * @return void
	 */
	protected function appendBackendFiles(&$files) {
		$layoutName = 'Backend';
		$sectionName = 'Main';
		$this->appendLayoutFile($files, 'Backend');
		$this->appendTemplateFile($files, self::TEMPLATE_FLUXFORM, $layoutName, $sectionName, 'Backend/Module.html');
	}

	/**
	 * @param array $files
	 * @return void
	 */
	protected function appendContentFiles(&$files) {
		$layoutName = 'Content';
		$sectionName = 'Main';
		$this->appendLayoutFile($files, $layoutName);
		$this->appendTemplateFile($files, self::TEMPLATE_CONTENT, $layoutName, $sectionName, 'Content/Example.html');
	}

	/**
	 * @param array $files
	 * @return void
	 */
	protected function appendPageFiles(&$files) {
		$layoutName = 'Page';
		$sectionName = 'Main';
		$this->appendLayoutFile($files, $layoutName);
		$this->appendTemplateFile($files, self::TEMPLATE_PAGE, $layoutName, $sectionName, 'Page/Standard.html');
	}

	/**
	 * @param array $files
	 * @param string $identifier
	 * @param string $layout
	 * @param string $section
	 * @param string $placement
	 * @return void
	 */
	protected function appendTemplateFile(&$files, $identifier, $layout, $section, $placement) {
		$templateVariables = array(
			'layout' => $layout,
			'section' => $section,
			'configurationSectionName' => 'Configuration',
			'id' => str_replace('/', '', strtolower($identifier)),
			'label' => $identifier,
			'icon' => 'Icons/' . substr($placement, 0, -4) . 'gif',
			'extension' => $this->configuration['extensionKey'],
			'placement' => $placement
		);
		$templatePathAndFilename = $this->targetFolder . '/Resources/Private/Templates/' . $placement;
		$files[$templatePathAndFilename] = $this->getPreparedCodeTemplate($identifier, $templateVariables)->render();
	}

	/**
	 * @param array $files
	 * @param string $layoutName
	 * @param string $layoutSectionRenderName
	 * @return void
	 */
	protected function appendLayoutFile(&$files, $layoutName, $layoutSectionRenderName = 'Main') {
		$layoutVariables = array(
			'name' => $layoutName,
			'section' => $layoutSectionRenderName
		);
		$layoutPathAndFilename = $this->targetFolder . '/Resources/Private/Layouts/' . $layoutName . '.html';
		$files[$layoutPathAndFilename] = $this->getPreparedCodeTemplate(self::TEMPLATE_LAYOUT, $layoutVariables)->render();
	}

}
