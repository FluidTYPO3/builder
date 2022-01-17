<?php
namespace FluidTYPO3\Builder\CodeGeneration\Extension;

use FluidTYPO3\Builder\CodeGeneration\AbstractCodeGenerator;
use FluidTYPO3\Builder\CodeGeneration\CodeGeneratorInterface;
use FluidTYPO3\Flux\Controller\ContentController;
use FluidTYPO3\Flux\Controller\PageController;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionGenerator extends AbstractCodeGenerator implements CodeGeneratorInterface
{
    const TEMPLATE_CONTROLLER = 'Controller/Controller';
    const TEMPLATE_EXTLOCALCONF = 'Extension/ext_localconf';
    const TEMPLATE_EMCONF = 'Extension/ext_emconf';
    const TEMPLATE_LAYOUT = 'Fluid/Layout';
    const TEMPLATE_CONTENT = 'Fluid/Content';
    const TEMPLATE_PAGE = 'Fluid/Page';
    const TEMPLATE_FLUXFORM = 'Fluid/Form';
    const TEMPLATE_TYPOSCRIPTCONSTANTS = 'Extension/TypoScript/constants';
    const TEMPLATE_TYPOSCRIPTSETUP = 'Extension/TypoScript/setup';
    const TEMPLATE_LANGUAGEFILE = 'Extension/Language/locallang.xlf';

    /**
     * @var array
     */
    private $configuration = [];

    /**
     * @var string
     */
    private $targetFolder = null;

    /**
     * @param array $configuration
     * @return void
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string $targetFolder
     * @return void
     */
    public function setTargetFolder($targetFolder)
    {
        $this->targetFolder = $targetFolder;
    }

    /**
     * @return string
     */
    protected function getExtensionKeyFromSettings()
    {
        $extensionKey = $this->configuration['extensionKey'];
        if (false !== strpos($extensionKey, '.')) {
            $extensionKey = array_pop(explode('.', $extensionKey));
        }
        return GeneralUtility::camelCaseToLowerCaseUnderscored($extensionKey);
    }

    /**
     * @return string
     */
    protected function getExtensionNamespaceFromSettings()
    {
        return $this->configuration['extensionNamespace'];
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function generate()
    {
        $extensionKey = $this->getExtensionKeyFromSettings();
        if (null === $this->targetFolder) {
            $this->setTargetFolder((defined('PATH_site') ? constant('PATH_typo3conf') . 'ext/' : Environment::getPublicPath() . '/typo3conf/ext/') . $extensionKey);
        }
        if (true === is_dir($this->targetFolder)) {
            throw new \RuntimeException(
                'Extension key "' . $extensionKey . '" already has a folder in "' . $this->targetFolder . '"',
                1371692599
            );
        }
        $filesToBeWritten = [
            $this->targetFolder . '/ext_emconf.php' => $this->getPreparedCodeTemplate(
                self::TEMPLATE_EMCONF,
                $this->configuration
            )->render()
        ];
        $foldersToBeCreated = [$this->targetFolder];
        $hasVhs = true === in_array('vhs', $this->configuration['dependencies']);
        $appendLanguageFile = false;
        if ($this->configuration['pages'] ?? false) {
            $this->appendPageFiles($filesToBeWritten);
            $appendLanguageFile = true;
        }
        if ($this->configuration['content'] ?? false) {
            $this->appendContentFiles($filesToBeWritten, $hasVhs);
            $appendLanguageFile = true;
        }
        if ($appendLanguageFile) {
            $this->appendLanguageFile($filesToBeWritten);
        }
        $controllerFolder = $this->targetFolder . '/Classes/Controller/';
        if (true === $this->configuration['controllers']) {
            array_push($foldersToBeCreated, $controllerFolder);
        }
        if (true === $this->configuration['controllers']) {
            if ($this->configuration['content'] ?? false) {
                $this->appendControllerClassFile(
                    $filesToBeWritten,
                    'Content',
                    ContentController::class,
                    $controllerFolder
                );
            }
            if ($this->configuration['pages'] ?? false) {
                $this->appendControllerClassFile(
                    $filesToBeWritten,
                    'Page',
                    PageController::class,
                    $controllerFolder
                );
            }
        }
        $this->appendTypoScriptConfiguration($filesToBeWritten);
        $this->appendExtensionLocalconfFile($filesToBeWritten);
        $this->appendTypoScriptIntegrationFile($filesToBeWritten);
        $foldersToBeCreated = array_unique($foldersToBeCreated);
        foreach ($foldersToBeCreated as $folderPathToBeCreated) {
            $this->createFolder($folderPathToBeCreated);
        }
        foreach ($filesToBeWritten as $fileToBeWritten => $fileContentToBeWritten) {
            $this->createFile($fileToBeWritten, $fileContentToBeWritten);
        }
        if ($this->configuration['pages'] ?? false) {
            $this->copyFile(
                'Resources/Public/Icons/Example.svg',
                $this->targetFolder . '/Resources/Public/Icons/Page/Standard.svg'
            );
        }
        if ($this->configuration['content'] ?? false) {
            $this->copyFile(
                'Resources/Public/Icons/Example.svg',
                $this->targetFolder . '/Resources/Public/Icons/Content/Example.svg'
            );
        }
        $this->copyFile(
            'Resources/Public/Icons/Example.svg',
            $this->targetFolder . '/Resources/Public/Icons/Extension.svg'
        );
        return 'Built extension "' . $extensionKey . '"';
    }

    /**
     * @param array $files
     * @param string $controllerName
     * @param string $parentControllerClassName
     * @param string $folder
     */
    protected function appendControllerClassFile(&$files, $controllerName, $parentControllerClassName, $folder)
    {
        $templateVariables = $this->configuration;
        $templateVariables['controllerName'] = $controllerName;
        $templateVariables['parentControllerClass'] = $parentControllerClassName;
        $templateVariables['namespace'] = $this->getExtensionNamespaceFromSettings() . 'Controller';
        $files[$folder . $controllerName . 'Controller.php'] = $this->getPreparedCodeTemplate(
            self::TEMPLATE_CONTROLLER,
            $templateVariables
        )->render();
    }

    /**
     * @param array $files
     * @return void
     */
    protected function appendTypoScriptConfiguration(&$files)
    {
        $extensionKey = $this->getExtensionKeyFromSettings();
        $templateVariables = [
            'extension' => $extensionKey,
            'signature' => ExtensionManagementUtility::getCN($extensionKey)
        ];
        $folder = $this->targetFolder . '/Configuration/TypoScript';
        $files[$folder . '/constants.typoscript'] = $this->getPreparedCodeTemplate(
            self::TEMPLATE_TYPOSCRIPTCONSTANTS,
            $templateVariables
        )->render();
        $files[$folder . '/setup.typoscript'] = $this->getPreparedCodeTemplate(
            self::TEMPLATE_TYPOSCRIPTSETUP,
            $templateVariables
        )->render();
    }

    /**
     * @param array $files
     * @return void
     */
    protected function appendExtensionLocalconfFile(&$files)
    {
        $templateVariables = [
            'pages' => '',
            'content' => '',
            'configuration' => '',
        ];

        // note: the following code uses the provided "extensionKey" *directly* because
        // for these registrations, we require the full Vendor.ExtensionName if that
        // is the format used. Otherwise, legacy class names would be expected.
        if ($this->configuration['pages'] ?? false) {
            $templateVariables['pages'] = '\FluidTYPO3\Flux\Core::registerProviderExtensionKey(\'' .
                $this->configuration['extensionName'] . '\', \'Page\');';
        }
        if ($this->configuration['content'] ?? false) {
            $templateVariables['content'] = '\FluidTYPO3\Flux\Core::registerProviderExtensionKey(\'' .
                $this->configuration['extensionName'] . '\', \'Content\');';
        }
        $files[$this->targetFolder . '/ext_localconf.php'] = $this->getPreparedCodeTemplate(
            self::TEMPLATE_EXTLOCALCONF,
            $templateVariables
        )->render();
    }

    /**
     * @param array $files
     * @return void
     */
    protected function appendTypoScriptIntegrationFile(&$files)
    {
        $title = trim($this->configuration['title']);
        $templateVariables = [
            'pages' => '',
            'content' => '',
            'configuration' => sprintf(
                '\\%s::addStaticFile(\'%s\', \'Configuration/TypoScript\', \'%s\');',
                ExtensionManagementUtility::class,
                $this->configuration['extensionKey'],
                $title
            )
        ];

        $files[$this->targetFolder . '/Configuration/TCA/Overrides/sys_template.php'] = $this->getPreparedCodeTemplate(
            self::TEMPLATE_EXTLOCALCONF,
            $templateVariables
        )->render();
    }

    /**
     * @param array $files
     * @return void
     */
    protected function appendLanguageFile(&$files)
    {
        $variables = [
            'extension' => $this->getExtensionKeyFromSettings(),
            'date' => date('c')
        ];
        $filePathAndFilename = $this->targetFolder . '/Resources/Private/Language/locallang.xlf';
        $files[$filePathAndFilename] = $this->getPreparedCodeTemplate(
            self::TEMPLATE_LANGUAGEFILE,
            $variables
        )->render();
    }

    /**
     * @param array $files
     * @param boolean $hasVhs
     * @return void
     */
    protected function appendContentFiles(&$files, $hasVhs)
    {
        $layoutName = 'Content';
        $sectionName = 'Main';
        $variables = [
            'formId' => 'example'
        ];
        $this->appendLayoutFile($files, $layoutName);
        if (true === $hasVhs) {
            $variables['vhs'] = 'xmlns:v="http://typo3.org/ns/FluidTYPO3/Vhs/ViewHelpers"';
            $layoutName = 'Content';
        }
        $this->appendTemplateFile(
            $files,
            self::TEMPLATE_CONTENT,
            $layoutName,
            $sectionName,
            'Content/Example.html',
            $variables
        );
    }

    /**
     * @param array $files
     * @return void
     */
    protected function appendPageFiles(&$files)
    {
        $layoutName = 'Page';
        $sectionName = 'Main';
        $variables = [
            'formId' => 'standard'
        ];
        $this->appendLayoutFile($files, $layoutName);
        $this->appendTemplateFile(
            $files,
            self::TEMPLATE_PAGE,
            $layoutName,
            $sectionName,
            'Page/Standard.html',
            $variables
        );
    }

    /**
     * @param array $files
     * @param string $identifier
     * @param string $layout
     * @param string $section
     * @param string $placement
     * @param array $variables
     * @return void
     */
    protected function appendTemplateFile(&$files, $identifier, $layout, $section, $placement, array $variables)
    {
        $templateVariables = [
            'layout' => $layout,
            'section' => $section,
            'configurationSectionName' => 'Configuration',
            'id' => str_replace('/', '', strtolower($identifier)),
            'label' => $identifier,
            'icon' => 'Icons/' . substr($placement, 0, -4) . 'gif',
            'extension' => $this->getExtensionKeyFromSettings(),
            'placement' => $placement
        ];
        $templateVariables = array_merge_recursive($variables, $templateVariables);
        $templatePathAndFilename = $this->targetFolder . '/Resources/Private/Templates/' . $placement;
        $files[$templatePathAndFilename] = $this->getPreparedCodeTemplate($identifier, $templateVariables)->render();
    }

    /**
     * @param array $files
     * @param string $layoutName
     * @param string $layoutSectionRenderName
     * @param string $layoutType
     */
    protected function appendLayoutFile(
        &$files,
        $layoutName,
        $layoutSectionRenderName = 'Main',
        $layoutType = self::TEMPLATE_LAYOUT
    ) {
        $layoutVariables = [
            'name' => $layoutName,
            'section' => $layoutSectionRenderName
        ];
        $layoutPathAndFilename = $this->targetFolder . '/Resources/Private/Layouts/' . $layoutName . '.html';
        $files[$layoutPathAndFilename] = $this->getPreparedCodeTemplate($layoutType, $layoutVariables)->render();
    }
}
