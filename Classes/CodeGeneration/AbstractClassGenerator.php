<?php
namespace FluidTYPO3\Builder\CodeGeneration;

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

use FluidTYPO3\Builder\Service\ClassAnalysisService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractClassGenerator
 * @package FluidTYPO3\Builder\CodeGeneration
 */
abstract class AbstractClassGenerator extends AbstractCodeGenerator implements ClassGeneratorInterface
{

    /**
     * @var ClassAnalysisService
     */
    protected $classAnalysisService;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $author = null;

    /**
     * @var string
     */
    protected $package = null;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @var array
     */
    protected $methods = [];

    /**
     * @param ClassAnalysisService $classAnalysisService
     * @return void
     */
    public function injectClassAnalysisService(ClassAnalysisService $classAnalysisService)
    {
        $this->classAnalysisService = $classAnalysisService;
    }

    /**
     * @param string $name
     * @return void
     * @abstract
     */
    public function setClassName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @param string $package
     */
    public function setPackage($package)
    {
        $this->package = $package;
    }


    /**
     * @param $attributes
     * @return void
     * @abstract
     */
    public function setClassAttributes($attributes)
    {
        $this->attributes = $attributes;
    }


    /**
     * @param string $templateIdentifier
     * @param array $variables
     * @return void
     * @abstract
     */
    public function appendMethodFromSourceTemplate($templateIdentifier, $variables = [])
    {
        $name = true === isset($variables['name']) ? $variables['name'] : basename($templateIdentifier);
        $template = $this->getPreparedCodeTemplate($templateIdentifier, $variables);
        $code = $template->render();
        $this->methods[$name] = $code;
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $visibility
     * @return void
     * @abstract
     */
    public function appendProperty($name, $type, $visibility = 'protected')
    {
        $code = "\t/**\n\t * @var $" . $name . ' ' . $type . "\n\t */\n\t" . $visibility . ' $' . $name . ';';
        $this->properties[$name] = $code;
    }

    /**
     * @param string $filePathAndFilename
     * @return void
     */
    public function save($filePathAndFilename)
    {
        $code = $this->generate();
        $shouldBeWritten = false;
        if (false === file_exists($filePathAndFilename)) {
            $shouldBeWritten = true;
        } else {
            $contents = file_get_contents($filePathAndFilename);
            if (false !== strpos($contents, '@protection off')) {
                unlink($filePathAndFilename);
                // class file contains marker which allows overwriting without further ado
                $shouldBeWritten = true;
            }
        }
        if (true === $shouldBeWritten) {
            GeneralUtility::writeFile($filePathAndFilename, $code);
        }
    }

    /**
     * @param string $template
     * @param string $className
     * @return string
     */
    public function renderClass($template, $className)
    {
        if (null === $className) {
            return null;
        }
        $properties = array_map('trim', $this->properties);
        $methods = array_map('trim', $this->methods);
        // @todo check if needed
        // $this->appendCommonTestMethods();
        $variables = [
            'class' => $className,
            'author' => $this->author,
            'year' => date('Y', time()),
            'protection' => 'off',
            'package' => $this->package,
            'properties' => implode("\n\n\t", $properties),
            'methods' => implode("\n\n\t", $methods)
        ];
        $template = $this->getPreparedCodeTemplate($template, $variables);
        return $template->render();
    }
}
