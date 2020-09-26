<?php
namespace FluidTYPO3\Builder\Result;

class FluidParserResult extends ParserResult
{

    /**
     * @var array
     */
    protected $namespaces = [];

    /**
     * @var string
     */
    protected $layoutName = null;

    /**
     * @var boolean
     */
    protected $compilable = false;

    /**
     * @param string $layoutName
     * @return void
     */
    public function setLayoutName($layoutName)
    {
        $this->layoutName = $layoutName;
    }

    /**
     * @return string
     */
    public function getLayoutName()
    {
        return $this->layoutName;
    }

    /**
     * @param array $namespaces
     * @return void
     */
    public function setNamespaces($namespaces)
    {
        $this->namespaces = $namespaces;
    }

    /**
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * @return string
     */
    public function getNamespacesFlattened()
    {
        $flat = [];
        foreach ($this->namespaces as $namespaceAlias => $classPath) {
            array_push($flat, $namespaceAlias . '=[' . implode(', ', $classPath) . ']');
        }
        return implode(', ', $flat);
    }

    /**
     * @param boolean $compilable
     */
    public function setCompilable($compilable)
    {
        $this->compilable = $compilable;
    }

    /**
     * @return boolean
     */
    public function getCompilable()
    {
        return $this->compilable;
    }
}
