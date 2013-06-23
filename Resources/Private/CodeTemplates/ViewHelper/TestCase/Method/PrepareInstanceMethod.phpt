	/**
	 * @return ###class###
	 * @support
	 */
	protected function getPreparedInstance() {
		$viewHelperClassName = '###class###';
		$arguments = array();
		$nodeClassName = (FALSE !== strpos($viewHelperClassName, '_') ? 'Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode' : '\\TYPO3\\CMS\\Fluid\\Core\\Parser\\SyntaxTree\\ViewHelperNode');
        $renderingContextClassName = (FALSE !== strpos($viewHelperClassName, '_') ? 'Tx_Fluid_Core_Rendering_RenderingContext' : '\\TYPO3\\CMS\\Fluid\\Core\\Rendering\\RenderingContext');
        $controllerContextClassName = (FALSE !== strpos($viewHelperClassName, '_') ? 'Tx_Extbase_MVC_Controller_ControllerContext' : '\\TYPO3\\CMS\\Extbase\\MVC\\Controller\\ControllerContext');
        $requestClassName = (FALSE !== strpos($viewHelperClassName, '_') ? 'Tx_Extbase_MVC_Web_Request' : '\\TYPO3\\CMS\\Extbase\\MVC\\Web\\Request');

        /** @var Tx_Extbase_MVC_Web_Request $request */
        $request = $this->objectManager->get($requestClassName);
        /** @var $viewHelperInstance Tx_Fluid_Core_ViewHelper_AbstractViewHelper */
        $viewHelperInstance = $this->objectManager->get($viewHelperClassName);
        /** @var Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode $node */
        $node = $this->objectManager->get($nodeClassName, $viewHelperInstance, $arguments);
        /** @var Tx_Extbase_MVC_Controller_ControllerContext $controllerContext */
        $controllerContext = $this->objectManager->get($controllerContextClassName);
        $controllerContext->setRequest($request);
        /** @var Tx_Fluid_Core_Rendering_RenderingContext $renderingContext */
        $renderingContext = $this->objectManager->get($renderingContextClassName);
        $renderingContext->setControllerContext($controllerContext);

        $viewHelperInstance->setRenderingContext($renderingContext);
        $viewHelperInstance->setViewHelperNode($node);
        return $viewHelperInstance;
	}
