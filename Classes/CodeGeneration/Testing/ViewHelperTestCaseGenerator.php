<?php

class Tx_Builder_CodeGeneration_Testing_ViewHelperTestCaseGenerator extends Tx_Builder_CodeGeneration_AbstractClassGenerator {

	const TEMPLATE_CLASS = 'ViewHelper/TestCase/Class';
	const TEMPLATE_SUPPORT_PREPARE_INSTANCE = 'ViewHelper/TestCase/Method/PrepareInstanceMethod';
	const TEMPLATE_SUPPORT_INJECT_OBJECTMANAGER = 'ViewHelper/TestCase/Method/InjectObjectManager';
	const TEMPLATE_TEST_CREATE_INSTANCE  = 'ViewHelper/TestCase/Method/CanCreateViewHelper';
	const TEMPLATE_TEST_INITIALIZE_INSTANCE  = 'ViewHelper/TestCase/Method/CanInitializeViewHelper';
	const TEMPLATE_TEST_PREPARE_ARGUMENTS  = 'ViewHelper/TestCase/Method/CanPrepareArguments';
	const TEMPLATE_TEST_SET_VIEWHELPERNODE  = 'ViewHelper/TestCase/Method/CanSetViewHelperNode';

	/**
	 * @var string
	 */
	protected $viewHelperClassName = NULL;

	/**
	 * @param string $viewHelperClassName
	 * @return void
	 */
	public function setViewHelperClassName($viewHelperClassName) {
		$this->viewHelperClassName = $viewHelperClassName;
	}

	/**
	 * @return string
	 */
	public function generate() {
		if (NULL === $this->viewHelperClassName) {
			return NULL;
		}
		$this->appendCommonProperties();
		$this->appendCommonTestMethods();
		return $this->renderClass(self::TEMPLATE_CLASS, $this->viewHelperClassName . 'Test');
	}

	/**
	 * @return void
	 */
	protected function appendCommonProperties() {
		$this->appendProperty('objectManager', 'Tx_Extbase_Object_ObjectManagerInterface');
	}

	/**
	 * @return void
	 */
	protected function appendCommonTestMethods() {
		/** @var $viewHelperInstance Tx_Fluid_Core_ViewHelper_AbstractViewHelper */
		$viewHelperInstance = $this->objectManager->get($this->viewHelperClassName);
		$nodeClassName = (FALSE === strpos($this->viewHelperClassName, '_') ? 'Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode' : '\\TYPO3\\CMS\\Fluid\\Core\\Parser\\SyntaxTree\\ViewHelperNode');
		$variables = array(
			'class' => $this->viewHelperClassName,
			'nodeclass' => $nodeClassName,
		);
		$this->appendMethodFromSourceTemplate(self::TEMPLATE_SUPPORT_INJECT_OBJECTMANAGER, $variables);
		$this->appendMethodFromSourceTemplate(self::TEMPLATE_SUPPORT_PREPARE_INSTANCE, $variables);
		$this->appendMethodFromSourceTemplate(self::TEMPLATE_TEST_CREATE_INSTANCE, $variables);
		$this->appendMethodFromSourceTemplate(self::TEMPLATE_TEST_INITIALIZE_INSTANCE, $variables);
		$this->appendMethodFromSourceTemplate(self::TEMPLATE_TEST_PREPARE_ARGUMENTS, $variables);
		$this->appendMethodFromSourceTemplate(self::TEMPLATE_TEST_SET_VIEWHELPERNODE, $variables);

	}

}
