<?php
namespace FluidTYPO3\Builder\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class ClassAnalysisService implements SingletonInterface
{

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param mixed $classOrInstance
     * @param string $methodName
     * @return boolean
     */
    public function assertClassMethodHasRequiredArguments($classOrInstance, $methodName)
    {
        if (false === is_object($classOrInstance)) {
            $classOrInstance = $this->objectManager->get($classOrInstance);
        }
        $reflection = new \ReflectionClass($classOrInstance);
        $methodReflection = $reflection->getMethod($methodName);
        $arguments = $methodReflection->getParameters();
        foreach ($arguments as $argumentReflection) {
            if (false === $argumentReflection->isOptional()) {
                return true;
            }
        }
        return false;
    }
}
