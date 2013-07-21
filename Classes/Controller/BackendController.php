<?php


class Tx_Builder_Controller_BackendController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_Builder_Service_SyntaxService
	 */
	protected $syntaxService;

	/**
	 * @param Tx_Builder_Service_SyntaxService $syntaxService
	 * @return void
	 */
	public function injectSyntaxService(Tx_Builder_Service_SyntaxService $syntaxService) {
		$this->syntaxService = $syntaxService;
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
