<?php
namespace FluidTYPO3\Builder\Analysis;

interface MessageInterface {

	/**
	 * @param string $message
	 * @return MessageInterface
	 */
	public function setMessage($message);

	/**
	 * @return string
	 */
	public function getMessage();

	/**
	 * @param mixed $payload
	 * @return MessageInterface
	 */
	public function setPayload($payload);

	/**
	 * @return mixed
	 */
	public function getPayload();

	/**
	 * @param integer $severity
	 * @return MessageInterface
	 */
	public function setSeverity($severity);

	/**
	 * @return integer
	 */
	public function getSeverity();

}
