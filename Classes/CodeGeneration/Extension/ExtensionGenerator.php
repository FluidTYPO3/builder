<?php
namespace FluidTYPO3\Builder\CodeGeneration\Extension;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Claus Due <claus@namelesscoder.net>
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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ExtensionGenerator
 * @package FluidTYPO3\Builder\CodeGeneration\Extension
 */
class ExtensionGenerator extends AbstractCodeGenerator implements CodeGeneratorInterface
{

    const TEMPLATE_CONTROLLER = 'Controller/Controller';
    const TEMPLATE_EXTTABLES = 'Extension/ext_tables';
    const TEMPLATE_EXTLOCALCONF = 'Extension/ext_localconf';
    const TEMPLATE_EMCONF = 'Extension/ext_emconf';
    const TEMPLATE_LAYOUT = 'Fluid/Layout';
    const TEMPLATE_CONTENT_CORE_LAYOUT = 'Fluid/ContentCoreLayout';
    const TEMPLATE_BACKEND_LAYOUT = 'Fluid/BackendLayout';
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
        $extensionKey = $this->configuration['extensionKey'];
        if (false !== strpos($extensionKey, '.')) {
            list ($vendor, $extensionName) = explode('.', $extensionKey);
            return $vendor . '\\' . $extensionName . '\\';
        }
        return GeneralUtility::underscoredToUpperCamelCase($extensionKey) . '\\';
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function generate()
    {
        $extensionKey = $this->getExtensionKeyFromSettings();
        if (null === $this->targetFolder) {
            $this->setTargetFolder(PATH_typo3conf . 'ext/' . $extensionKey);
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
        $hasFluidpages = true === in_array('fluidpages', $this->configuration['dependencies']);
        $hasFluidcontent = true === in_array('fluidcontent', $this->configuration['dependencies']);
        $hasFluidbackend = true === in_array('fluidbackend', $this->configuration['dependencies']);
        $hasVhs = true === in_array('vhs', $this->configuration['dependencies']);
        if (true === $hasFluidpages) {
            $this->appendPageFiles($filesToBeWritten);
        }
        if (true === $hasFluidcontent) {
            $this->appendContentFiles($filesToBeWritten, $hasVhs);
        }
        if (true === $hasFluidpages || true === $hasFluidcontent) {
            $this->appendLanguageFile($filesToBeWritten);
        }
        if (true === $hasFluidbackend) {
            $this->appendBackendFiles($filesToBeWritten);
        }
        $controllerFolder = $this->targetFolder . '/Classes/Controller/';
        if (true === $this->configuration['controllers'] || true === $hasFluidbackend) {
            array_push($foldersToBeCreated, $controllerFolder);
        }
        if (true === $this->configuration['controllers']) {
            if (true === $hasFluidcontent) {
                $this->appendControllerClassFile(
                    $filesToBeWritten,
                    'Content',
                    'FluidTYPO3\\Fluidcontent\\Controller\\ContentController',
                    $controllerFolder
                );
            }
            if (true === $hasFluidpages) {
                $this->appendControllerClassFile(
                    $filesToBeWritten,
                    'Page',
                    'FluidTYPO3\\Fluidpages\\Controller\\PageController',
                    $controllerFolder
                );
            }
        }
        // backend-module always needs a BackendController
        if (true === $hasFluidbackend) {
            $this->appendControllerClassFile(
                $filesToBeWritten,
                'Backend',
                'FluidTYPO3\\Fluidbackend\\Controller\\BackendController',
                $controllerFolder
            );
        }
        $this->appendTypoScriptConfiguration($filesToBeWritten);
        $this->appendExtensionTablesFile($filesToBeWritten);
        $this->appendExtensionLocalconfFile($filesToBeWritten);
        if (true === $hasFluidcontent || true === $hasFluidpages) {
            array_push($foldersToBeCreated, $this->targetFolder . '/Resources/Private/Language');
        }
        $foldersToBeCreated = array_unique($foldersToBeCreated);
        foreach ($foldersToBeCreated as $folderPathToBeCreated) {
            $this->createFolder($folderPathToBeCreated);
        }
        foreach ($filesToBeWritten as $fileToBeWritten => $fileContentToBeWritten) {
            $this->createFile($fileToBeWritten, $fileContentToBeWritten);
        }
        if (true === $hasFluidpages) {
            $this->copyFile(
                'Resources/Public/Icons/Example.svg',
                $this->targetFolder . '/Resources/Public/Icons/Page/Standard.svg'
            );
        }
        if (true === $hasFluidcontent) {
            $this->copyFile(
                'Resources/Public/Icons/Example.svg',
                $this->targetFolder . '/Resources/Public/Icons/Content/Example.svg'
            );
        }
        $this->copyFile('ext_icon.gif', $this->targetFolder . '/ext_icon.gif');
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
        $files[$folder . '/constants.txt'] = $this->getPreparedCodeTemplate(
            self::TEMPLATE_TYPOSCRIPTCONSTANTS,
            $templateVariables
        )->render();
        $files[$folder . '/setup.txt'] = $this->getPreparedCodeTemplate(
            self::TEMPLATE_TYPOSCRIPTSETUP,
            $templateVariables
        )->render();
    }

    /**
     * @param array $files
     * @return void
     */
    protected function appendExtensionTablesFile(&$files)
    {
        $title = trim($this->configuration['title']);
        $templateVariables = [
            'configuration' => sprintf(
                '%s::addStaticFile($_EXTKEY, \'Configuration/TypoScript\', \'%s\');',
                ExtensionManagementUtility::class,
                $title
            )
        ];
        $files[$this->targetFolder . '/ext_tables.php'] = $this->getPreparedCodeTemplate(
            self::TEMPLATE_EXTTABLES,
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
            'backend' => ''
        ];

        // note: the following code uses the provided "extensionKey" *directly* because
        // for these registrations, we require the full Vendor.ExtensionName if that
        // is the format used. Otherwise, legacy class names would be expected.
        if (true === in_array('fluidpages', $this->configuration['dependencies'])) {
            $templateVariables['pages'] = '\FluidTYPO3\Flux\Core::registerProviderExtensionKey(\'' .
                $this->configuration['extensionKey'] . '\', \'Page\');';
        }
        if (true === in_array('fluidcontent', $this->configuration['dependencies'])) {
            $templateVariables['content'] = '\FluidTYPO3\Flux\Core::registerProviderExtensionKey(\'' .
                $this->configuration['extensionKey'] . '\', \'Content\');';
        }
        if (true === in_array('fluidbackend', $this->configuration['dependencies'])) {
            $templateVariables['backend'] = '\FluidTYPO3\Flux\Core::registerProviderExtensionKey(\'' .
                $this->configuration['extensionKey'] . '\', \'Backend\');';
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
    protected function appendBackendFiles(&$files)
    {
        $layoutName = 'Backend';
        $sectionName = 'Main';
        $variables = [
            'formId' => 'module'
        ];
        $this->appendLayoutFile($files, 'Backend', 'Main', self::TEMPLATE_BACKEND_LAYOUT);
        $this->appendTemplateFile(
            $files,
            self::TEMPLATE_FLUXFORM,
            $layoutName,
            $sectionName,
            'Backend/Module.html',
            $variables
        );
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
            $this->appendLayoutFile($files, 'ContentCore', 'Main', self::TEMPLATE_CONTENT_CORE_LAYOUT);
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
