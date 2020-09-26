<?php
namespace FluidTYPO3\Builder\Analysis;

class Metric
{

    /**
     * Name of this Metric, refers to a constant defined in
     * the class which collected the Metric.
     *
     * @var string
     */
    protected $name;

    /**
     * Numeric or multivalue - the logic which consumes this
     * Metric must take care to handle each type.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Message: a message describing the evaluated result of
     * the final value of this Metric, fx "Warning! Node count
     * is very high, consider reducing or splitting into Partials."
     *
     * @var MessageInterface[]
     */
    protected $messages = [];

    /**
     * Payload: appended with data collected during metrics; data
     * which can further help determining causes for problematic
     * Metrics values.
     *
     * @var array
     */
    protected $payload = [];

    /**
     * @param MessageInterface[] $messages
     * @return Metric
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param MessageInterface $message
     * @return Metric
     */
    public function addMessage(MessageInterface $message)
    {
        array_push($this->messages, $message);
        return $this;
    }

    /**
     * @param string $name
     * @return Metric
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $value
     * @return Metric
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     * @return void
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Attempt to increment $this->value if it is numeric in any way.
     *
     * @param mixed $value
     * @return Metric
     */
    public function increment($value = 1)
    {
        if (true === ctype_digit($this->value) || true === is_float($value) || true === is_integer($value)) {
            $this->value += $value;
        }
    }

    /**
     * @param mixed $value
     * @return Metric
     */
    public function setOnlyIfHigher($value)
    {
        $this->value = max($this->value, $value);
    }

    /**
     * @param mixed $value
     * @return Metric
     */
    public function setOnlyIfLower($value)
    {
        $this->value = min($this->value, $value);
    }
}
