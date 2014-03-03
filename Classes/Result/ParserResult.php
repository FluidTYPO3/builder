<?php
namespace FluidTYPO3\Builder\Result;

class ParserResult implements ParserResultInterface {

	const PAYLOAD_FLASHMESSAGE = 'FlashMessage';
	const PAYLOAD_METRICS = 'Metrics';

	/**
	 * @var boolean
	 */
	protected $valid = TRUE;

	/**
	 * @var Exception
	 */
	protected $error = NULL;

	/**
	 * @var array
	 */
	protected $payload = array();

	/**
	 * @var string
	 */
	protected $payloadType = self::PAYLOAD_FLASHMESSAGE;

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

}
