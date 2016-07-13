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

/**
 * Class CodeTemplate
 * @package FluidTYPO3\Builder\CodeGeneration
 */
class CodeTemplate
{

    /**
     * @var string
     */
    protected $path = null;

    /**
     * @var string
     */
    protected $suffix = '.phpt';

    /**
     * @var string
     */
    protected $identifier = null;

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @param string $identifier
     * @return void
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param array $variables
     * @return void
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     * @return void
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * @return string
     */
    public function render()
    {
        $identifier = $this->getIdentifier();
        $variables = $this->getVariables();
        if (null === $identifier) {
            return null;
        }
        $filePathAndFilename = $this->getPath() . $identifier . $this->getSuffix();
        $content = file_get_contents($filePathAndFilename);
        foreach ($variables as $name => $value) {
            $content = str_replace('###' . $name . '###', $value, $content);
        }
        return $content;
    }
}
