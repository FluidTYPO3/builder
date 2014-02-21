<?php
namespace FluidTYPO3\Builder\Controller;

use FluidTYPO3\Builder\Service\ExtensionService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;


/**
 * Class FrontendController
 */
class FrontendController extends ActionController {

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
		$git = FALSE;
		$travis = FALSE;
		$dry = FALSE;
		$verbose = FALSE;
		$temporaryBaseFolder = GeneralUtility::getFileAbsFileName('typo3temp/builder/' . uniqid('provider_'));
		$temporaryFolder =  $temporaryBaseFolder . '/' . $extensionKey;
		$archiveFilePathAndFilename = $temporaryBaseFolder . '/' . $extensionKey . '.zip';
		GeneralUtility::mkdir_deep($temporaryBaseFolder);
		$generator = $this->extensionService->buildProviderExtensionGenerator($extensionKey, $author, $title, $description, $controllers, $pages, $content, $backend, $vhs, $git, $travis, $dry, $verbose);
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
