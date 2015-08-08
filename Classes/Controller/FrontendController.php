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

use FluidTYPO3\Builder\Analysis\Metric;
use FluidTYPO3\Builder\Service\ExtensionService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class FrontendController
 */
class FrontendController extends ActionController {

	const MAX_VARIABLE_STRING_BYTES = 100;
	const MAX_FLUID_TEMPLATE_BYTES = 2048;
	const ERROR_SECURITY = 1;

	/**
	 * @var ExtensionService
	 */
	protected $extensionService;

	/**
	 * @param ExtensionService $extensionService
	 * @return void
	 */
	public function injectExtensionService(ExtensionService $extensionService) {
		$this->extensionService = $extensionService;
	}

	/**
	 * @return void
	 */
	public function doodleAction() {

	}

	/**
	 * @param string $fluid
	 * @param array $variables
	 * @return void
	 * @throws \RuntimeException
	 */
	public function renderFluidAction($fluid = NULL, array $variables = array()) {
		$rejection = 'Your variables or fluid template code was rejected for security reasons. You may have performed ';
		$rejection .= 'one of the following unsafe actions: variables containing an unsafe value or variable size too large, ';
		$rejection .= 'fluid template attempts to import a namespace (this happens automatically for Fluid, VHS and Flux in';
		$rejection .= 'this code doodler), fluid template is too large for reasonable handling - or fluid template uses ';
		$rejection .= 'one or more of an undisclosed list of ViewHelpers which are considered unsafe for public execution.';
		header('Content-type: application/json');
		if (FALSE === $this->assertSecureVariables($variables) || FALSE === $this->assertSecureFluidTemplate($fluid)) {
			$response = array(
				'message' => $rejection,
				'code' => self::ERROR_SECURITY
			);
			echo json_encode($response);
		} elseif (FALSE === empty($this->settings['doodle']['renderer']) && !GeneralUtility::_GET('type')) {
			// Undocumented setting: use an off-site renderer to do the actual Fluid rendering in a protected scope
			$renderer = $this->settings['doodle']['renderer'];
			$url = $renderer . '?type=9967';
			$query = array('tx_builder_render' => $this->request->getArguments());
			$context = stream_context_create(array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content' => http_build_query($query)
				)
			));
			echo file_get_contents($url, FALSE, $context);
		} else {
			$fluid = '{namespace flux=FluidTYPO3\\Flux\\ViewHelpers}' . PHP_EOL . $fluid;
			$fluid = '{namespace v=FluidTYPO3\\Vhs\\ViewHelpers}' . PHP_EOL . $fluid;
			try {
				$templateParser = $this->objectManager->get('FluidTYPO3\Builder\Analysis\Fluid\TemplateAnalyzer');
				$start = microtime(TRUE);
				$memoryBefore = memory_get_usage();
				/** @var \FluidTYPO3\Builder\Result\ParserResult $analysis */
				$analysis = $templateParser->analyze($fluid);
				if (FALSE === $this->assertSecureViewHelpers($analysis->getViewHelpers())) {
					throw new \RuntimeException($rejection, self::ERROR_SECURITY);
				}
				$memoryUsage = memory_get_usage() - $memoryBefore;
				$parseTime = microtime(TRUE) - $start;
				/** @var StandaloneView $view */
				$view = $this->objectManager->get('TYPO3\CMS\Fluid\View\StandaloneView');
				$view->setTemplateSource($fluid);
				$view->assignMultiple($variables);
				$memoryBefore = memory_get_usage();
				$start = microtime(TRUE);
				$rendered = trim($view->render(), PHP_EOL . "\t");
				$memoryUsageRender = memory_get_usage() - $memoryBefore;
				$renderTime = microtime(TRUE) - $start;
				$response = array(
					'source' => htmlentities($rendered),
					'preview' => $rendered,
					'viewhelpers' => $analysis->getViewHelpers(),
					'analysis' => array_map(function($item) {
						/** @var Metric $item */
						return $item->getValue();
					}, $analysis->getPayload()),
					'timing' => array(
						'parse' => (float) number_format($parseTime * 1000, 2),
						'render' => (float) number_format($renderTime * 1000, 2)
					),
					'memory' => array(
						'parse' => (float) number_format($memoryUsage / 1024, 1),
						'render' => (float) number_format($memoryUsageRender / 1024, 1)
					),
					'variables' => $variables
				);
			} catch (\Exception $error) {
				$response = array(
					'message' => $error->getMessage(),
					'code' => $error->getCode()
				);
			}
			echo json_encode($response, JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG);
		}
		exit();
	}

	/**
	 * @param array $ids
	 * @return boolean
	 */
	protected function assertSecureViewHelpers(array $ids) {
		foreach ($ids as $id) {
			// only necessary check: is ViewHelper ID blacklisted?
			if (TRUE === in_array($id, (array) GeneralUtility::trimExplode(',', $this->settings['doodle']['blacklistedViewHelpers']))) {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * @param string $fluid
	 * @return boolean
	 */
	protected function assertSecureFluidTemplate($fluid) {
		if (self::MAX_FLUID_TEMPLATE_BYTES < strlen($fluid)) {
			return FALSE;
		}
		// check 1: no namespace imports, old school
		if (FALSE !== strpos($fluid, '{namespace')) {
			return FALSE;
		}
		// check 2: no namespace imports, any occurrence(s) of xmlns imports
		if (FALSE !== strpos($fluid, 'xmlns:')) {
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * @param array $variables
	 * @return boolean
	 */
	protected function assertSecureVariables(array $variables) {
		foreach ($variables as $variable) {
			if (FALSE === $this->assertSecureVariable($variable)) {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * @param mixed $variable
	 * @return boolean
	 */
	protected function assertSecureVariable($variable) {
		if (TRUE === is_array($variable)) {
			return $this->assertSecureVariables($variable);
		}
		if (TRUE === is_string($variable)) {
			// check 1: can variable pass for a standard float-in-string?
			if (FALSE === (boolean) preg_match('/[^0-9\.]+/i', $variable) && (float) $variable == $variable) {
				return TRUE;
			}
			// check 2: enforce maximum string length of 100 characters
			if (self::MAX_VARIABLE_STRING_BYTES < strlen($variable)) {
				return FALSE;
			}
			// check 3: no paths or escapes whatsoever are allowed, absolute or otherwise
			if (TRUE === (boolean) preg_match('/[\.\/\\]/i', $variable)) {
				return FALSE;
			}
			// check 4: absolutely no class names of any kind!
			if (TRUE === class_exists($variable)) {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * @param string $filename
	 * @return string
	 */
	public function buildAction($filename) {
		$parts = pathinfo($filename);
		$name = str_replace('..', '.', $parts['basename']);
		$name = trim($name, '.');
		$extensionKey = pathinfo($filename, PATHINFO_FILENAME);
		$author = 'Your Name <you@domain.com>';
		$title = 'Provider extension for Fluid Powered TYPO3';
		$description = 'Provides templates for pages and content';
		$controllers = TRUE;
		$pages = TRUE;
		$content = TRUE;
		$backend = FALSE;
		$vhs = TRUE;
		$dry = FALSE;
		$verbose = FALSE;
		$temporaryBaseFolder = GeneralUtility::getFileAbsFileName('typo3temp/builder/' . uniqid('provider_'));
		$temporaryFolder = $temporaryBaseFolder . '/' . $extensionKey;
		$archiveFilePathAndFilename = $temporaryBaseFolder . '/' . $extensionKey . '.zip';
		GeneralUtility::mkdir_deep($temporaryBaseFolder);
		$generator = $this->extensionService->buildProviderExtensionGenerator($extensionKey, $author, $title, $description, $controllers, $pages, $content, $backend, $vhs);
		$generator->setVerbose($verbose);
		$generator->setDry($dry);
		$generator->setTargetFolder($temporaryFolder);
		$generator->generate();
		$packCommand = 'cd ' . $temporaryBaseFolder . ' && zip -r "' . $extensionKey . '.zip" "' . $extensionKey . '"';
		exec($packCommand);
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename=' . $name);
		header('Content-Length: ' . filesize($archiveFilePathAndFilename));
		readfile($archiveFilePathAndFilename);
		exec('rm -rf ' . $temporaryBaseFolder);
		exit();
	}

}
