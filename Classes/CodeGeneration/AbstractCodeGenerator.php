<?php

abstract class Tx_Builder_CodeGeneration_AbstractCodeGenerator {

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param string $filePathAndFilename
	 * @return void
	 */
	public function save($filePathAndFilename) {
		$code = $this->generate();
		t3lib_div::writeFile($filePathAndFilename, $code);
	}

	/**
	 * @param string $identifier
	 * @param array $variables
	 * @return Tx_Builder_CodeGeneration_CodeTemplate
	 */
	protected function getPreparedCodeTemplate($identifier, $variables) {
		/** @var $template Tx_Builder_CodeGeneration_CodeTemplate */
		$template = $this->objectManager->get('Tx_Builder_CodeGeneration_CodeTemplate');
		$template->setIdentifier($identifier);
		$template->setVariables($variables);
		return $template;
	}

}
