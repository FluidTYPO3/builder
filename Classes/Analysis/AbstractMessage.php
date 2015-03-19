<?php
namespace FluidTYPO3\Builder\Analysis;
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

use TYPO3\CMS\Core\Messaging\FlashMessage;

/**
 * Class AbstractMessage
 * @package FluidTYPO3\Builder\Analysis
 */
abstract class AbstractMessage implements MessageInterface {

	/**
	 * @var string
	 */
	protected $message;

	/**
	 * @var integer
	 */
	protected $severity = FlashMessage::OK;

	/**
	 * @var mixed
	 */
	protected $payload;

	/**
	 * @param string $message
	 * @return void
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param mixed $payload
	 * @return void
	 */
	public function setPayload($payload) {
		$this->payload = $payload;
	}

	/**
	 * @return mixed
	 */
	public function getPayload() {
		return $this->payload;
	}

	/**
	 * @param integer $severity
	 * @return void
	 */
	public function setSeverity($severity) {
		$this->severity = $severity;
	}

	/**
	 * @return integer
	 */
	public function getSeverity() {
		return $this->severity;
	}

}
