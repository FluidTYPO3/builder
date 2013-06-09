<?php

class Tx_Builder_CodeGeneration_Testing_ViewHelperTestCaseGenerator extends Tx_Builder_CodeGeneration_AbstractClassGenerator {

	const TEMPLATE_CLASS = 'ViewHelper/TestCase/Class';

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
		$this->appendCommonTestMethods();
		return $this->renderClass(self::TEMPLATE_CLASS, $this->viewHelperClassName . 'Test');
	}

	/**
	 * @return void
	 */
	protected function appendCommonTestMethods() {
		/** @var $viewHelperInstance Tx_Fluid_Core_ViewHelper_AbstractViewHelper */
		$viewHelperInstance = $this->objectManager->get($this->viewHelperClassName);

	}

}
