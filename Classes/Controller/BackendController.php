<?php
namespace FluidTYPO3\Builder\Controller;

use FluidTYPO3\Builder\Service\ExtensionService;
use FluidTYPO3\Builder\Service\SyntaxService;
use FluidTYPO3\Builder\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extensionmanager\Utility\InstallUtility;

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
				$service = $this->objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
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
	 * @param array $syntax
	 * @param array $extensions
	 * @param array $formats
	 */
	public function syntaxAction(array $syntax, array $extensions, array $formats) {
		$reports = array();
		$csvFormats = implode(',', $formats);
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
				if ('php' === $syntaxName) {
					$reportsForSyntaxName = $this->syntaxService->syntaxCheckPhpFilesInPath($extensionFolder . '/Classes');
				} elseif ('fluid' === $syntaxName) {
					$reportsForSyntaxName = $this->syntaxService->syntaxCheckFluidTemplateFilesInPath($extensionFolder . '/Resources', $csvFormats);
				} else {
					$reportsForSyntaxName = array();
				}
				$reports[$extensionKey][$syntaxName]['reports'] = $reportsForSyntaxName;
				$reports[$extensionKey][$syntaxName]['errors'] = $this->syntaxService->countErrorsInResultCollection($reportsForSyntaxName);
			}
		}
		$this->view->assign('reports', $reports);
	}

}
