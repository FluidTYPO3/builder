<?php


class Tx_Builder_Controller_BackendController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_Builder_Service_SyntaxService
	 */
	protected $syntaxService;

	/**
	 * @var Tx_Builder_Service_ExtensionService
	 */
	protected $extensionService;

	/**
	 * @param Tx_Builder_Service_SyntaxService $syntaxService
	 * @return void
	 */
	public function injectSyntaxService(Tx_Builder_Service_SyntaxService $syntaxService) {
		$this->syntaxService = $syntaxService;
	}

	/**
	 * @param Tx_Builder_Service_ExtensionService $extensionService
	 * @return void
	 */
	public function injectExtensionService(Tx_Builder_Service_ExtensionService $extensionService) {
		$this->extensionService = $extensionService;
	}

	/**
	 * @param string $view
	 * @return void
	 */
	public function indexAction($view = 'Index') {
		$extensions = Tx_Builder_Utility_ExtensionUtility::getAllInstalledFluidEnabledExtensions();
		$selectorOptions = Tx_Builder_Utility_ExtensionUtility::getAllInstalledFluidEnabledExtensionsAsSelectorOptions();
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
				/** @var \TYPO3\CMS\Extensionmanager\Utility\InstallUtility $service */
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
			$extensionFolder = t3lib_extMgm::extPath($extensionKey);
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
