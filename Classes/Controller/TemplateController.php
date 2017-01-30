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

use FluidTYPO3\Builder\Service\FluxFormService;
use FluidTYPO3\Flux\Form;
use FluidTYPO3\Flux\Service\FluxService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Fluid\View\TemplatePaths;

/**
 * Class TemplateController
 */
class TemplateController extends ActionController
{
    /**
     * @var FluxFormService
     */
    protected $fluxFormService;

    /**
     * @var FluxService
     */
    protected $fluxService;

    /**
     * @param FluxFormService $fluxFormService
     * @return void
     */
    public function injectFluxFormService(FluxFormService $fluxFormService)
    {
        $this->fluxFormService = $fluxFormService;
    }

    /**
     * @param FluxService $fluxService
     * @return void
     */
    public function injectFluxService(FluxService $fluxService)
    {
        $this->fluxService = $fluxService;
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        $forms = $this->fluxFormService->getAllRegisteredForms();

        $this->view->assign('view', 'FluxAdministration');
        $this->view->assign('forms', $forms);
    }

    /**
     * @param string $templatePathAndFilename
     * @param string $extensionName
     * @return void
     */
    public function editAction($templatePathAndFilename, $extensionName)
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
        $format = pathinfo($templatePathAndFilename, PATHINFO_EXTENSION);
        $viewConfiguration = $this->fluxService->getViewConfigurationForExtensionName($extensionName);
        $templatePaths = new TemplatePaths($viewConfiguration);
        $layoutNames = $templatePaths->resolveAvailableLayoutFiles($format);
        $layoutNames = array_map(function($item) {
            return pathinfo($item, PATHINFO_FILENAME);
        }, $layoutNames);
        $layoutNames = ['' => ''] + array_combine($layoutNames, $layoutNames);

        $layoutName = 'Default';
        $this->view->assign('extensionName', $extensionName);
        $this->view->assign('layoutName', $layoutName);
        $this->view->assign('layoutNames', $layoutNames);
        $this->view->assign('data', $data);
        $this->view->assign('templateFile', $templatePathAndFilename);
        $this->view->assign('relativeTemplateFile', substr($templatePathAndFilename, strlen(PATH_site)));
        $this->view->assign('mainContent', $matches[1][0]);
        $this->view->assign('backups', $this->fluxFormService->getBackupsForTemplateFile($templatePathAndFilename));
        $this->view->assign('view', 'FluxAdministration');
    }

    /**
     * @param string $templatePathAndFilename
     * @param string $extensionName
     * @param Form $form
     * @param string $mainContent
     * @param string $layoutName
     * @param Form\Container\Grid|null $grid
     * @return string
     */
    public function updateAction(
        $templatePathAndFilename,
        $extensionName,
        Form $form,
        $mainContent = null,
        $layoutName = null,
        Form\Container\Grid $grid = null
    ) {
        if ($grid === null) {
            $grid = Form\Container\Grid::create();
        }
        $source = $this->fluxFormService->convertDataToTemplate(['form' => $form, 'grid' => $grid], $mainContent, $layoutName);
        $this->fluxFormService->writeTemplateFileWithBackup($templatePathAndFilename, $source);
        $this->redirect('edit', null, null, ['templatePathAndFilename' => $templatePathAndFilename, 'extensionName' => $extensionName]);
    }

    /**
     * @param string $templatePathAndFilename
     * @param string $extensionName
     * @param Form $form
     * @param string $mainContent
     * @param string $layoutName
     * @param Form\Container\Grid|null $grid
     * @return string
     */
    public function createAction(
        $templatePathAndFilename,
        $extensionName,
        Form $form,
        $mainContent,
        $layoutName,
        Form\Container\Grid $grid
    ) {
        if ($grid === null) {
            $grid = Form\Container\Grid::create();
        }
        $source = $this->fluxFormService->convertDataToTemplate(['form' => $form, 'grid' => $grid], $mainContent, $layoutName);
        $this->fluxFormService->writeTemplateFileWithBackup($templatePathAndFilename, $source);
        $this->redirect('edit', null, null, ['templatePathAndFilename' => $templatePathAndFilename, 'extensionName' => $extensionName]);
    }

    /**
     * @param string $templatePathAndFilename
     * @param integer $backupTimestamp
     * @return void
     */
    public function restoreAction($templatePathAndFilename, $backupTimestamp)
    {
        $backupFilePath = pathinfo($templatePathAndFilename, PATHINFO_DIRNAME) . '/.backups/' . $backupTimestamp . '.' . pathinfo($templatePathAndFilename, PATHINFO_BASENAME);
        $backupFileSource = file_get_contents($backupFilePath);
        $this->fluxFormService->writeTemplateFileWithBackup($templatePathAndFilename, $backupFileSource);
        $this->redirect('edit', null, null, ['templatePathAndFilename' => $templatePathAndFilename]);
    }

    /**
     * Performs analysis of Flux-enabled templates with two levels
     * of details possible: simple analysis of file/template on its
     * own, optionally combined with an analysis (using expensive
     * record digging) of possible usages of the template file.
     *
     * The second level of detail requires an additional flag in
     * order to warn the user about potential long waits.
     *
     * @param string $templatePathAndFilename
     * @param boolean $usages
     * @return void
     */
    public function analysisAction($templatePathAndFilename, $usages = false)
    {

    }

    /**
     * @param string $templatePath
     * @param string $templateName
     * @param string $extensionName
     * @param string $layoutName
     * @param string $mainContent
     * @param Form|null $form
     * @param Form\Container\Grid|null $grid
     * @validate $templatePath NotEmpty
     * @validate $templateName NotEmpty
     * @validate $extensionName NotEmpty
     * @return void
     */
    public function newAction(
        $templatePath,
        $templateName,
        $extensionName,
        $layoutName = null,
        $mainContent = null,
        Form $form = null,
        Form\Container\Grid $grid = null
    ) {
        if ($form) {
            $structure = [
                'form' => $this->fluxFormService->convertFormToStructure($form),
                'grid' => $this->fluxFormService->convertGridToStructure($grid)
            ];
        } else {
            $structure = [
                'form' => [
                    'id' => strtolower(pathinfo($templateName, PATHINFO_FILENAME)),
                    'extensionName' => $extensionName,
                    'children' => [
                        [
                            'name' => 'options',
                            'label' => 'LLL:EXT:flux/Resources/Private/Language/locallang.xlf:tt_content.tx_flux_options',
                            'type' => 'FluidTYPO3\\Flux\\Form\\Container\\Sheet',
                            'children' => []
                        ]
                    ],
                    'name' => 'test',
                    'label' => $templateName,
                    'type' => 'FluidTYPO3\\Flux\\Form',
                ]
            ];
        }
        $format = pathinfo($templateName, PATHINFO_EXTENSION);
        $viewConfiguration = $this->fluxService->getViewConfigurationForExtensionName($extensionName);
        $templatePaths = new TemplatePaths($viewConfiguration);
        $layoutNames = $templatePaths->resolveAvailableLayoutFiles($format);
        $layoutNames = array_map(function($item) {
            return pathinfo($item, PATHINFO_FILENAME);
        }, $layoutNames);
        $layoutNames = ['' => ''] + array_combine($layoutNames, $layoutNames);

        $this->view->assign('layoutNames', $layoutNames);
        $this->view->assign('layoutName', $layoutName);
        $this->view->assign('extensionName', $extensionName);
        $this->view->assign('templatePath', $templatePath);
        $this->view->assign('templateName', $templateName);
        $this->view->assign('view', 'FluxAdministration');
        $this->view->assign('structure', json_encode($structure));
        $this->view->assign('mainContent', $mainContent);
        $this->view->assign('tempatePathAndFilename', $templatePath . $templateName);
    }

}
