<?php

class Tx_Builder_Service_ClassAnalysisService implements t3lib_Singleton {

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
	 * @param mixed $classOrInstance
	 * @param string $methodName
	 * @return boolean
	 */
	public function assertClassMethodHasRequiredArguments($classOrInstance, $methodName) {
		if (FALSE === is_object($classOrInstance)) {
			$classOrInstance = $this->objectManager->get($classOrInstance);
		}
		$reflection = new ReflectionClass($classOrInstance);
		$methodReflection = $reflection->getMethod($methodName);
		$arguments = $methodReflection->getParameters();
		foreach ($arguments as $argumentReflection) {
			if (FALSE === $argumentReflection->isOptional()) {
				return TRUE;
			}
		}
		return FALSE;
	}

}
