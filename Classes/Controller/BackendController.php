<?php
namespace FluidTYPO3\Builder\Controller;

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

use FluidTYPO3\Builder\Analysis\Fluid\TemplateAnalyzer;
use FluidTYPO3\Builder\Analysis\Metric;
use FluidTYPO3\Builder\Result\ParserResult;
use FluidTYPO3\Builder\Service\ExtensionService;
use FluidTYPO3\Builder\Service\FluxFormService;
use FluidTYPO3\Builder\Service\SyntaxService;
use FluidTYPO3\Builder\Utility\ExtensionUtility;
use FluidTYPO3\Flux\Form;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Backend\Template\DocumentTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Class BackendController
 * @package FluidTYPO3\Builder\Controller
 */
class BackendController extends ActionController
{

    /**
     * @var SyntaxService
     */
    protected $syntaxService;

    /**
     * @var ExtensionService
     */
    protected $extensionService;

    /**
     * @var FluxFormService
     */
    protected $fluxFormService;

    /**
     * @param SyntaxService $syntaxService
     * @return void
     */
    public function injectSyntaxService(SyntaxService $syntaxService)
    {
        $this->syntaxService = $syntaxService;
    }

    /**
     * @param ExtensionService $extensionService
     * @return void
     */
    public function injectExtensionService(ExtensionService $extensionService)
    {
        $this->extensionService = $extensionService;
    }

    /**
     * @param FluxFormService $fluxFormService
     * @return void
     */
    public function injectFluxFormService(FluxFormService $fluxFormService)
    {
        $this->fluxFormService = $fluxFormService;
    }

    /**
     * @param string $view
     * @return void
     */
    public function indexAction($view = 'Index')
    {
        $extensions = ExtensionUtility::getAllInstalledFluidEnabledExtensions();
        $selectorOptions = ExtensionUtility::getAllInstalledFluidEnabledExtensionsAsSelectorOptions();
        $formats = [
            'html' => true,
            'xml' => false,
            'txt' => false,
            'eml' => false,
            'yaml' => false,
            'css' => false,
            'js' => false,
        ];
        $this->view->assign('csh', BackendUtility::wrapInHelp('builder', 'modules'));
        $this->view->assign('view', $view);
        $this->view->assign('extensions', $extensions);
        $this->view->assign('extensionSelectorOptions', $selectorOptions);
        $this->view->assign('formats', $formats);
    }

    /**
     * @param string $view
     * @return void
     */
    public function buildFormAction($view = 'BuildForm')
    {
        $author = '';
        if (!empty($GLOBALS['BE_USER']->user['realName']) && !empty($GLOBALS['BE_USER']->user['email'])) {
            $author = $GLOBALS['BE_USER']->user['realName'] . ' <' . $GLOBALS['BE_USER']->user['email'] . '>';
        }
        $this->view->assign('csh', BackendUtility::wrapInHelp('builder', 'modules'));
        $this->view->assign('view', $view);
        $this->view->assign('author', $author);
        $isFluidcontentCoreInstalled = ExtensionManagementUtility::isLoaded('fluidcontent_core') ? "'checked'" : null;
        $this->view->assign('isFluidcontentCoreInstalled', $isFluidcontentCoreInstalled);
    }

    /**
     * @param string $name
     * @param string $author
     * @param string $title
     * @param string $description
     * @param boolean $controllers
     * @param boolean $pages
     * @param boolean $content
     * @param boolean $backend
     * @param boolean $vhs
     * @param boolean $fluidcontentCore
     * @param boolean $dry
     * @param boolean $verbose
     * @return void
     */
    public function buildAction(
        $name,
        $author,
        $title,
        $description,
        $controllers,
        $pages,
        $content,
        $backend,
        $vhs,
        $fluidcontentCore,
        $dry,
        $verbose
    ) {
        $generator = $this->extensionService->buildProviderExtensionGenerator(
            $name,
            $author,
            $title,
            $description,
            $controllers,
            $pages,
            $content,
            $backend,
            $vhs,
            $fluidcontentCore
        );
        $generator->setVerbose($verbose);
        $generator->setDry($dry);
        if (false === $dry) {
            $generator->generate();
        }
        $this->view->assign('boolean', true);
        $this->view->assign('view', 'BuildForm');
        $this->view->assign('attributes', $this->arguments->getArrayCopy());
    }

    /**
     * @validate $syntax NotEmpty
     * @validate $extensions NotEmpty
     * @validate $formats NotEmpty
     * @ignorevalidation $filteredFiles
     * @param array $syntax
     * @param array $extensions
     * @param array $formats
     * @param array $filteredFiles
     */
    public function syntaxAction(array $syntax, array $extensions, array $formats, array $filteredFiles = [])
    {
        /** @var DocumentTemplate $document */
        $document = &$GLOBALS['TBE_TEMPLATE'];
        $resourcePath = $document->backPath . ExtensionManagementUtility::extRelPath('builder') . 'Resources/Public/';
        $pageRenderer = new PageRenderer();
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.canvasTextRenderer.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.canvasAxisTickRenderer.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.cursor.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.categoryAxisRenderer.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.barRenderer.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.pointLabels.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/plotter.js');
        $reports = [];
        $csvFormats = trim(implode(',', $formats), ',');
        foreach ($extensions as $extensionKey) {
            if (true === empty($extensionKey)) {
                continue;
            }
            $extensionFolder = ExtensionManagementUtility::extPath($extensionKey);
            $reports[$extensionKey] = [];
            foreach ($syntax as $syntaxName) {
                if (true === empty($syntaxName)) {
                    continue;
                }
                $reportsForSyntaxName = [];
                if ('php' === $syntaxName) {
                    $reportsForSyntaxName = $this->syntaxService->syntaxCheckPhpFilesInPath(
                        $extensionFolder . '/Classes'
                    );
                } elseif ('fluid' === $syntaxName) {
                    $reportsForSyntaxName = $this->syntaxService->syntaxCheckFluidTemplateFilesInPath(
                        $extensionFolder . '/Resources',
                        $csvFormats
                    );
                } elseif ('profile' === $syntaxName) {
                    $files = GeneralUtility::getAllFilesAndFoldersInPath([], $extensionFolder, $csvFormats);
                    if (0 === count($filteredFiles)) {
                        $filteredFiles = $files;
                    }
                    $this->view->assign('files', $files);
                    $this->view->assign('basePathLength', strlen($extensionFolder . '/Resources/Private'));
                    foreach ($files as $file) {
                        if (0 < count($filteredFiles) && false === in_array($file, $filteredFiles)) {
                            continue;
                        }
                        $shortFilename = substr($file, strlen($extensionFolder . '/Resources/Private'));
                        /** @var TemplateAnalyzer $templateAnalyzer */
                        $templateAnalyzer = $this->objectManager->get(TemplateAnalyzer::class);
                        $reportsForSyntaxName[$shortFilename] = $templateAnalyzer->analyzePathAndFilename($file);
                    }
                    $reports[$extensionKey][$syntaxName]['json'] = $this->encodeMetricsToJson($reportsForSyntaxName);
                }
                $reports[$extensionKey][$syntaxName]['reports'] = $reportsForSyntaxName;
                $reports[$extensionKey][$syntaxName]['errors'] = $this->syntaxService->countErrorsInResultCollection(
                    $reportsForSyntaxName
                );
            }
        }
        $this->view->assign('filteredFiles', $filteredFiles);
        $this->view->assign('reports', $reports);
        $this->view->assign('extensions', $extensions);
        $this->view->assign('formats', $formats);
        $this->view->assign('syntax', $syntax);
        $this->view->assign('view', 'Index');
    }

    /**
     * @return void
     */
    public function kickstarterAction()
    {
        $forms = $this->fluxFormService->getAllRegisteredForms();

        $this->view->assign('view', 'Kickstarter');
        $this->view->assign('forms', $forms);
    }

    /**
     * @param string $templatePathAndFilename
     * @return void
     */
    public function kickstarterEditAction($templatePathAndFilename)
    {
        $templateSource = file_get_contents($templatePathAndFilename);
        $matches = [];
        preg_match_all('/<f:section name="Main">([\\s\\S]*?)<\\/f:section/msiu', $templateSource, $matches);
        $data = $this->fluxFormService->getRegisteredFormAndGridByTemplateName($templatePathAndFilename);
        $this->view->assign(
            'structure',
            [
                'form' => $this->fluxFormService->convertFormToStructure($data['form']),
                'grid' => $this->fluxFormService->convertGridToStructure($data['grid'])
            ]
        );
        $this->view->assign('data', $data);
        $this->view->assign('templateFile', $templatePathAndFilename);
        $this->view->assign('mainContent', $matches[1][0]);
        $this->view->assign('backups', $this->fluxFormService->getBackupsForTemplateFile($templatePathAndFilename));
    }

    /**
     * @param string $templatePathAndFilename
     * @param Form $form
     * @param string $mainContent
     * @param string $layoutName
     * @param Form\Container\Grid|null $grid
     * @return string
     */
    public function kickstarterUpdateAction(
        $templatePathAndFilename,
        Form $form,
        $mainContent = null,
        $layoutName = null,
        Form\Container\Grid $grid = null
    )
    {
        if ($grid === null) {
            $grid = Form\Container\Grid::create();
        }
        $source = $this->fluxFormService->convertDataToTemplate(['form' => $form, 'grid' => $grid], $mainContent, $layoutName);
        $this->fluxFormService->writeTemplateFileWithBackup($templatePathAndFilename, $source);
        $this->redirect('kickstarterEdit', null, null, ['templatePathAndFilename' => $templatePathAndFilename]);
    }

    /**
     * @param string $templatePathAndFilename
     * @param integer $backupTimestamp
     * @return void
     */
    public function kickstarterBackupRestoreAction($templatePathAndFilename, $backupTimestamp)
    {
        $backupFilePath = pathinfo($templatePathAndFilename, PATHINFO_DIRNAME) . '/.backups/' . $backupTimestamp . '.' . pathinfo($templatePathAndFilename, PATHINFO_BASENAME);
        $backupFileSource = file_get_contents($backupFilePath);
        $this->fluxFormService->writeTemplateFileWithBackup($templatePathAndFilename, $backupFileSource);
        $this->redirect('kickstarterEdit', null, null, ['templatePathAndFilename' => $templatePathAndFilename]);
    }

    /**
     * @return void
     */
    public function kickstarterNewAction()
    {
        $structure = [
            'id' => 'test',
            'label' => 'Some form',
            'sheets' => [
                'default' => [
                    'label' => 'Sheet label',
                    'description' => 'Sheet description',
                    'shortDescription' => 'Short description',
                    'fields' => [
                        'test' => [
                            'type' => 'Input',
                            'label' => 'Test field',
                            'default' => 'default value',
                            'placeholder' => 'Placeholder text',
                            'wizards' => [
                                'link' => [
                                    'type' => 'Link'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $this->view->assign('view', 'Kickstarter');
        $this->view->assign('structure', json_encode($structure));
    }

    /**
     * @param ParserResult[] $metrics
     * @return string
     */
    protected function encodeMetricsToJson($metrics)
    {
        foreach ($metrics as $index => $metric) {
            $values = $metric->getPayload();
            $metrics[$index] = [];
            /** @var Metric $value */
            foreach ($values as $value) {
                $metrics[$index][$value->getName()] = $value->getValue();
            }
        }
        return json_encode($metrics);
    }
}
