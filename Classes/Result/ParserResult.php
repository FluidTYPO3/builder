<?php
namespace FluidTYPO3\Builder\Result;

class ParserResult implements ParserResultInterface {

	/**
	 * @var boolean
	 */
	protected $valid = TRUE;

	/**
	 * @var Exception
	 */
	protected $error = NULL;

	/**
	 * @param Exception $error
	 * @return void
	 */
	public function setError($error) {
		$this->error = $error;
	}

	/**
	 * @return Exception
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * @param boolean $valid
	 * @return void
	 */
	public function setValid($valid) {
		$this->valid = $valid;
	}

	/**
	 * @return boolean
	 */
	public function getValid() {
		return $this->valid;
	}

}
