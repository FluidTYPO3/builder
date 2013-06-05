<?php

class Tx_Builder_Result_FluidParserResult extends Tx_Builder_Result_ParserResult {

	/**
	 * @var array
	 */
	protected $namespaces = array();

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
		$flat = array();
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
