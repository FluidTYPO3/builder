<?php

class Tx_Builder_CodeGeneration_Extension_ExtensionGenerator
	extends Tx_Builder_CodeGeneration_AbstractCodeGenerator
	implements Tx_Builder_CodeGeneration_CodeGeneratorInterface {

	const TEMPLATE_EMCONF = 'Extension/ext_emconf';
	const TEMPLATE_LAYOUT = 'Fluid/Layout';
	const TEMPLATE_CONTENT = 'Fluid/FluxContent';
	const TEMPLATE_FLUXFORM = 'Fluid/FluxForm';

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
	 * @throws Exception
	 */
	public function generate() {
		$extensionKey = $this->configuration['extensionKey'];
		$this->setTargetFolder(PATH_typo3conf . 'ext/' . $extensionKey);
		if (TRUE === is_dir($this->targetFolder)) {
			throw new Exception('Extension key "' . $extensionKey . '" already has a folder in "' . $this->targetFolder . '"', 1371692599);
		}
		$filesToBeWritten = array(
			$this->targetFolder . '/ext_emconf.php' => $this->getPreparedCodeTemplate(self::TEMPLATE_EMCONF, $this->configuration)->render()
		);
		$foldersToBeCreated = array($this->targetFolder);
		if (TRUE === in_array('fluidpages', $this->configuration['dependencies'])) {
			$this->appendPageFilesAndFolders($foldersToBeCreated, $filesToBeWritten);
		}
		if (TRUE === in_array('fluidcontent', $this->configuration['dependencies'])) {
			$this->appendContentFilesAndFolders($foldersToBeCreated, $filesToBeWritten);
		}
		if (TRUE === in_array('fluidbackend', $this->configuration['dependencies'])) {
			$this->appendBackendFilesAndFolders($foldersToBeCreated, $filesToBeWritten);
		}
		if (TRUE === $this->configuration['controllers']) {
			array_push($foldersToBeCreated, $this->targetFolder . '/Classes/Controller');
		}
		$foldersToBeCreated = array_unique($foldersToBeCreated);
		foreach ($foldersToBeCreated as $folderPathToBeCreated) {
			$this->createFolder($folderPathToBeCreated);
		}
		$this->copyFile('ext_icon.gif', $this->targetFolder . '/ext_icon.gif');
		foreach ($filesToBeWritten as $fileToBeWritten => $fileContentToBeWritten) {
			$this->createFile($fileToBeWritten, $fileContentToBeWritten);
		}
		return 'Built extension "' . $extensionKey . '"';
	}

	/**
	 * @param array $folders
	 * @param array $files
	 * @return void
	 */
	protected function appendBackendFilesAndFolders(&$folders, &$files) {
		$layoutName = 'Backend';
		$sectionName = 'Main';
		$this->appendLayoutFile($files, 'Backend');
		$this->appendTemplateFile($files, self::TEMPLATE_FLUXFORM, $layoutName, $sectionName, 'Backend/MyModule.html');
		array_push($folders, $this->targetFolder . '/Classes/Controller');
		array_push($folders, $this->targetFolder . '/Resources/Private/Layouts');
		array_push($folders, $this->targetFolder . '/Resources/Private/Templates/Backend');
	}

	/**
	 * @param array $folders
	 * @param array $files
	 * @return void
	 */
	protected function appendContentFilesAndFolders(&$folders, &$files) {
		$layoutName = 'Content';
		$sectionName = 'Main';
		$this->appendLayoutFile($files, $layoutName);
		$this->appendTemplateFile($files, self::TEMPLATE_CONTENT, $layoutName, $sectionName, 'Content/MyContentElement.html');
		array_push($folders, $this->targetFolder . '/Resources/Private/Layouts');
		array_push($folders, $this->targetFolder . '/Resources/Private/Templates/Content');
		array_push($folders, $this->targetFolder . '/Resources/Public/Stylesheets');
		array_push($folders, $this->targetFolder . '/Resources/Public/Scripts');
	}

	/**
	 * @param array $folders
	 * @param array $files
	 * @return void
	 */
	protected function appendPageFilesAndFolders(&$folders, &$files) {
		$layoutName = 'Page';
		$sectionName = 'Main';
		$this->appendLayoutFile($files, $layoutName);
		$this->appendTemplateFile($files, self::TEMPLATE_FLUXFORM, $layoutName, $sectionName, 'Page/MyPageTemplate.html');
		array_push($folders, $this->targetFolder . '/Resources/Private/Layouts');
		array_push($folders, $this->targetFolder . '/Resources/Private/Templates/Page');
		array_push($folders, $this->targetFolder . '/Resources/Public/Stylesheets');
		array_push($folders, $this->targetFolder . '/Resources/Public/Scripts');
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
			'icon' => 'EXT:' . $this->configuration['extensionKey'] . '/ext_icon.gif'
		);
		$files[$this->targetFolder . '/Resources/Private/Templates/' . $placement]
			= $this->getPreparedCodeTemplate($identifier, $templateVariables)->render();
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
		$files[$this->targetFolder . '/Resources/Private/Layouts/' . $layoutName . '.html']
			= $this->getPreparedCodeTemplate(self::TEMPLATE_LAYOUT, $layoutVariables)->render();
	}

}
