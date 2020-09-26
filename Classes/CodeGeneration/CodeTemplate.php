<?php
namespace FluidTYPO3\Builder\CodeGeneration;

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
