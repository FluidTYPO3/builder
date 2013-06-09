<?php

class Tx_Builder_CodeGeneration_Testing_ViewHelperTestCaseGenerator extends Tx_Builder_CodeGeneration_AbstractClassGenerator {

	const TEMPLATE_CLASS = 'ViewHelper/TestCase/Class';
	const TEMPLATE_SUPPORT_PREPARE_INSTANCE = 'ViewHelper/TestCase/Method/PrepareInstanceMethod';
	const TEMPLATE_SUPPORT_INJECT_OBJECTMANAGER = 'ViewHelper/TestCase/Method/InjectObjectManager';
	const TEMPLATE_TEST_CREATE_INSTANCE  = 'ViewHelper/TestCase/Method/CanCreateViewHelperClassInstance';

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
		$variables = array(
			'class' => $this->viewHelperClassName
		);
		$this->appendMethodFromSourceTemplate(self::TEMPLATE_SUPPORT_INJECT_OBJECTMANAGER, $variables);
		$this->appendMethodFromSourceTemplate(self::TEMPLATE_SUPPORT_PREPARE_INSTANCE, $variables);
		$this->appendMethodFromSourceTemplate(self::TEMPLATE_TEST_CREATE_INSTANCE, $variables);

	}

}
