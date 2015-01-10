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

/**
 * Class ParserResult
 */
class ParserResult implements ParserResultInterface {

	const PAYLOAD_FLASHMESSAGE = 'FlashMessage';
	const PAYLOAD_METRICS = 'Metrics';

	/**
	 * @var boolean
	 */
	protected $valid = TRUE;

	/**
	 * @var \Exception
	 */
	protected $error = NULL;

	/**
	 * @var array
	 */
	protected $payload = array();

	/**
	 * @var array
	 */
	protected $viewHelpers = array();

	/**
	 * @var string
	 */
	protected $payloadType = self::PAYLOAD_FLASHMESSAGE;

	/**
	 * @param \Exception $error
	 * @return void
	 */
	public function setError($error) {
		$this->error = $error;
	}

	/**
	 * @return \Exception
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

	/**
	 * @param array $payload
	 * @return void
	 */
	public function setPayload(array $payload) {
		$this->payload = $payload;
	}

	/**
	 * @return array
	 */
	public function getPayload() {
		return $this->payload;
	}

	/**
	 * @param string $payloadType
	 * @return void
	 */
	public function setPayloadType($payloadType) {
		$this->payloadType = $payloadType;
	}

	/**
	 * @return string
	 */
	public function getPayloadType() {
		return $this->payloadType;
	}

	/**
	 * @return array
	 */
	public function getViewHelpers() {
		return $this->viewHelpers;
	}

	/**
	 * @param array $viewHelpers
	 * @return void
	 */
	public function setViewHelpers(array $viewHelpers) {
		$this->viewHelpers = $viewHelpers;
	}

}
