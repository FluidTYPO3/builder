<?php
namespace FluidTYPO3\Builder\Result;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
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

class FluidParserResult extends ParserResult {

	/**
	 * @var array
	 */
	protected $namespaces = [];

	/**
	 * @var string
	 */
	protected $layoutName = NULL;

	/**
	 * @var boolean
	 */
	protected $compilable = FALSE;

	/**
	 * @param string $layoutName
	 * @return void
	 */
	public function setLayoutName($layoutName) {
		$this->layoutName = $layoutName;
	}

	/**
	 * @return string
	 */
	public function getLayoutName() {
		return $this->layoutName;
	}

	/**
	 * @param array $namespaces
	 * @return void
	 */
	public function setNamespaces($namespaces) {
		$this->namespaces = $namespaces;
	}

	/**
	 * @return array
	 */
	public function getNamespaces() {
		return $this->namespaces;
	}

	/**
	 * @return string
	 */
	public function getNamespacesFlattened() {
		$flat = [];
		foreach ($this->namespaces as $namespaceAlias => $classPath) {
			array_push($flat, $namespaceAlias . '=' . $classPath);
		}
		return implode(', ', $flat);
	}

	/**
	 * @param boolean $compilable
	 */
	public function setCompilable($compilable) {
		$this->compilable = $compilable;
	}

	/**
	 * @return boolean
	 */
	public function getCompilable() {
		return $this->compilable;
	}

}
