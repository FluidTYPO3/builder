<?php
namespace FluidTYPO3\Builder\Analysis;

use TYPO3\CMS\Core\Messaging\FlashMessage;

/**
 * Class AbstractMessage
 * @package FluidTYPO3\Builder\Analysis
 */
abstract class AbstractMessage implements MessageInterface
{

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
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $payload
     * @return void
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param integer $severity
     * @return void
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;
    }

    /**
     * @return integer
     */
    public function getSeverity()
    {
        return $this->severity;
    }
}
