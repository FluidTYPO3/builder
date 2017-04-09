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

use FluidTYPO3\Builder\Helpers\GridsObjectStorage;
use FluidTYPO3\Builder\Service\FluxFormService;
use FluidTYPO3\Flux\Form;
use FluidTYPO3\Flux\Service\FluxService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
        $form = $this->fluxFormService->convertFormToStructure($data['form']);
        $this->view->assign(
            'structure',
            [
                'form' => $form,
                'grids' => array_map([$this->fluxFormService, 'convertGridToStructure'], $data['grids'])
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
        $this->view->assign('snippets', $this->fluxFormService->generateFluidSnippetsFromFormAndGrids($data['form'], $data['grids']));
        $this->view->assign('form', $data['form']);
        $this->view->assign('register', (integer) $this->fluxFormService->isTemplateRegisteredAsContentType($extensionName, $templatePathAndFilename));
        $this->view->assign('extensionName', $extensionName);
        $this->view->assign('layoutName', $layoutName);
        $this->view->assign('layoutNames', $layoutNames);
        $this->view->assign('data', $data);
        $this->view->assign('templatePathAndFilename', $templatePathAndFilename);
        $this->view->assign('relativeTemplateFile', substr($templatePathAndFilename, strlen(PATH_site)));
        $this->view->assign('mainContent', $matches[1][0]);
        $this->view->assign('backups', $this->fluxFormService->getBackupsForTemplateFile($templatePathAndFilename));
        $this->view->assign('view', 'FluxAdministration');
        $this->view->assign('fieldTypes', $this->fluxFormService->getGlobalObjectAttributeList());
    }

    /**
     * @param string $templatePathAndFilename
     * @param string $extensionName
     * @param Form $form
     * @param string $mainContent
     * @param string $layoutName
     * @param boolean $register
     * @param GridsObjectStorage $grids
     * @return string
     */
    public function updateAction(
        $templatePathAndFilename,
        $extensionName,
        Form $form,
        $mainContent,
        $layoutName,
        $register,
        GridsObjectStorage $grids
    ) {
        $grids = $grids->toArray();
        $source = $this->fluxFormService->convertDataToTemplate(['form' => $form, 'grids' => $grids], $mainContent, $layoutName);
        $this->fluxFormService->writeTemplateFileWithBackup($templatePathAndFilename, $source);
        $this->fluxFormService->writeOrRemoveContentTypeRegistration($extensionName, $templatePathAndFilename, !$register);
        $this->redirect('edit', null, null, ['templatePathAndFilename' => $templatePathAndFilename, 'extensionName' => $extensionName]);
    }

    /**
     * @param string $templatePathAndFilename
     * @param string $extensionName
     * @param Form $form
     * @param string $mainContent
     * @param string $layoutName
     * @param GridsObjectStorage $grids
     * @return string
     */
    public function createAction(
        $templatePathAndFilename,
        $extensionName,
        Form $form,
        $mainContent,
        $layoutName,
        GridsObjectStorage $grids
    ) {
        $grids = $grids->toArray();
        $source = $this->fluxFormService->convertDataToTemplate(['form' => $form, 'grids' => $grids], $mainContent, $layoutName);
        $this->fluxFormService->writeTemplateFileWithBackup($templatePathAndFilename, $source);
        $this->redirect('edit', null, null, ['templatePathAndFilename' => $templatePathAndFilename, 'extensionName' => $extensionName]);
    }

    /**
     * @param string $templatePathAndFilename
     * @param string $extensionName
     * @param integer $backupTimestamp
     * @return void
     */
    public function restoreAction($templatePathAndFilename, $extensionName, $backupTimestamp)
    {
        $backupFilePath = pathinfo($templatePathAndFilename, PATHINFO_DIRNAME) . '/.backups/' . $backupTimestamp . '.' . pathinfo($templatePathAndFilename, PATHINFO_BASENAME);
        $backupFileSource = file_get_contents($backupFilePath);
        $this->fluxFormService->writeTemplateFileWithBackup($templatePathAndFilename, $backupFileSource);
        $this->redirect('edit', null, null, ['templatePathAndFilename' => $templatePathAndFilename, 'extensionName' => $extensionName]);
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
     * @param string $controllerName
     * @param string $templateName
     * @param string $extensionName
     * @param string $layoutName
     * @param boolean $register
     * @param string $mainContent
     * @param Form|null $form
     * @param GridsObjectStorage $grids
     * @validate $templatePath NotEmpty
     * @validate $templateName NotEmpty
     * @validate $extensionName NotEmpty
     * @return void
     */
    public function newAction(
        $templatePath,
        $controllerName,
        $templateName,
        $extensionName,
        $layoutName = null,
        $register = false,
        $mainContent = null,
        Form $form = null,
        GridsObjectStorage $grids = null
    ) {
        if ($form) {
            $structure = [
                'form' => $this->fluxFormService->convertFormToStructure($form),
                'grids' => array_map([$this->fluxFormService, 'convertGridToStructure'], $grids->toArray())
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
                            'type' => Form\Container\Sheet::class,
                            'children' => []
                        ]
                    ],
                    'name' => 'test',
                    'label' => $templateName,
                    'type' => Form::class,
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

        $this->view->assign('form', $form);
        $this->view->assign('register', $register);
        $this->view->assign('layoutNames', $layoutNames);
        $this->view->assign('layoutName', $layoutName);
        $this->view->assign('extensionName', $extensionName);
        $this->view->assign('templatePath', $templatePath);
        $this->view->assign('templateName', $templateName);
        $this->view->assign('view', 'FluxAdministration');
        $this->view->assign('objects', $this->fluxFormService->getGlobalObjectAttributeList());
        $this->view->assign('structure', $structure);
        $this->view->assign('mainContent', $mainContent);
        $this->view->assign('templatePathAndFilename', $templatePath . $controllerName . '/' . $templateName);
    }

}
