<?php
namespace FluidTYPO3\Builder\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Claus Due <claus@namelesscoder.net>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Class ClassAnalysisService
 * @package FluidTYPO3\Builder\Service
 */
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
