<?php
namespace FluidTYPO3\Builder\Controller;
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

use FluidTYPO3\Builder\Analysis\Fluid\TemplateAnalyzer;
use FluidTYPO3\Builder\Result\ParserResult;
use FluidTYPO3\Builder\Service\ExtensionService;
use FluidTYPO3\Builder\Service\SyntaxService;
use FluidTYPO3\Builder\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extensionmanager\Utility\InstallUtility;
use TYPO3\CMS\Backend\Template\DocumentTemplate;

/**
 * Class BackendController
 * @package FluidTYPO3\Builder\Controller
 */
class BackendController extends ActionController {

	/**
	 * @var SyntaxService
	 */
	protected $syntaxService;

	/**
	 * @var ExtensionService
	 */
	protected $extensionService;

	/**
	 * @param SyntaxService $syntaxService
	 * @return void
	 */
	public function injectSyntaxService(SyntaxService $syntaxService) {
		$this->syntaxService = $syntaxService;
	}

	/**
	 * @param ExtensionService $extensionService
	 * @return void
	 */
	public function injectExtensionService(ExtensionService $extensionService) {
		$this->extensionService = $extensionService;
	}

	/**
	 * @param string $view
	 * @return void
	 */
	public function indexAction($view = 'Index') {
		$extensions = ExtensionUtility::getAllInstalledFluidEnabledExtensions();
		$selectorOptions = ExtensionUtility::getAllInstalledFluidEnabledExtensionsAsSelectorOptions();
		$formats = array(
			'html' => TRUE,
			'xml' => FALSE,
			'txt' => FALSE,
			'eml' => FALSE,
			'yaml' => FALSE,
			'css' => FALSE,
			'js' => FALSE,
		);
		$this->view->assign('view', $view);
		$this->view->assign('extensions', $extensions);
		$this->view->assign('extensionSelectorOptions', $selectorOptions);
		$this->view->assign('formats', $formats);
		$this->view->assign('author', $GLOBALS['BE_USER']->user['realName'] . ' <' . $GLOBALS['BE_USER']->user['email'] . '>');
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
	 * @param boolean $git
	 * @param boolean $travis
	 * @param boolean $dry
	 * @param boolean $verbose
	 * @param boolean $install
	 * @return void
	 */
	public function buildAction($name, $author, $title, $description, $controllers, $pages, $content, $backend, $vhs, $git, $travis, $dry, $verbose, $install) {
		$generator = $this->extensionService->buildProviderExtensionGenerator($name, $author, $title, $description, $controllers, $pages, $content, $backend, $vhs, $git, $travis, $dry, $verbose);
		$generator->setVerbose($verbose);
		$generator->setDry($dry);
		if (FALSE === $dry) {
			$generator->generate();
			if (TRUE === $install) {
				/** @var InstallUtility $service */
				$service = $this->objectManager->get('TYPO3\CMS\Extensionmanager\Utility\InstallUtility');
				$service->install($name);
			}
		}
		$this->view->assign('boolean', TRUE);
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
	public function syntaxAction(array $syntax, array $extensions, array $formats, array $filteredFiles = array()) {
		/** @var DocumentTemplate $document */
		$document = &$GLOBALS['TBE_TEMPLATE'];
		$resourcePath = $document->backPath . ExtensionManagementUtility::extRelPath('builder') . 'Resources/Public/';
		$pageRenderer = $document->getPageRenderer();
		$pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.min.js');
		$pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.canvasTextRenderer.min.js');
		$pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.canvasAxisTickRenderer.min.js');
		$pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.cursor.min.js');
		$pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.categoryAxisRenderer.min.js');
		$pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.barRenderer.min.js');
		$pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.pointLabels.min.js');
		$pageRenderer->addJsFile($resourcePath . 'Javascript/plotter.js');
		$reports = array();
		$csvFormats = trim(implode(',', $formats), ',');
		foreach ($extensions as $extensionKey) {
			if (TRUE === empty($extensionKey)) {
				continue;
			}
			$extensionFolder = ExtensionManagementUtility::extPath($extensionKey);
			$reports[$extensionKey] = array();
			foreach ($syntax as $syntaxName) {
				if (TRUE === empty($syntaxName)) {
					continue;
				}
				$reportsForSyntaxName = array();
				if ('php' === $syntaxName) {
					$reportsForSyntaxName = $this->syntaxService->syntaxCheckPhpFilesInPath($extensionFolder . '/Classes', $filteredFiles);
				} elseif ('fluid' === $syntaxName) {
					$reportsForSyntaxName = $this->syntaxService->syntaxCheckFluidTemplateFilesInPath($extensionFolder . '/Resources', $csvFormats, $filteredFiles);
				} elseif ('profile' === $syntaxName) {
					$files = GeneralUtility::getAllFilesAndFoldersInPath(array(), $extensionFolder, $csvFormats);
					if (0 === count($filteredFiles)) {
						$filteredFiles = $files;
					}
					$this->view->assign('files', $files);
					$this->view->assign('basePathLength', strlen($extensionFolder . '/Resources/Private'));
					foreach ($files as $file) {
						if (0 < count($filteredFiles) && FALSE === in_array($file, $filteredFiles)) {
							continue;
						}
						$shortFilename = substr($file, strlen($extensionFolder . '/Resources/Private'));
						/** @var TemplateAnalyzer $templateAnalyzer */
						$templateAnalyzer = $this->objectManager->get('FluidTYPO3\Builder\Analysis\Fluid\TemplateAnalyzer');
						$reportsForSyntaxName[$shortFilename] = $templateAnalyzer->analyze($file);
					}
					$reports[$extensionKey][$syntaxName]['json'] = $this->encodeMetricsToJson($reportsForSyntaxName);
				}
				$reports[$extensionKey][$syntaxName]['reports'] = $reportsForSyntaxName;
				$reports[$extensionKey][$syntaxName]['errors'] = $this->syntaxService->countErrorsInResultCollection($reportsForSyntaxName);
			}
		}
		$this->view->assign('filteredFiles', $filteredFiles);
		$this->view->assign('reports', $reports);
		$this->view->assign('extensions', $extensions);
		$this->view->assign('formats', $formats);
		$this->view->assign('syntax', $syntax);
	}

	/**
	 * @param ParserResult[] $metrics
	 * @return string
	 */
	protected function encodeMetricsToJson($metrics) {
		foreach ($metrics as $index => $metric) {
			$values = $metric->getPayload();
			$metrics[$index] = array();
			foreach ($values as $value) {
				$metrics[$index][$value->getName()] = $value->getValue();
			}
		}
		return json_encode($metrics);
	}

}
