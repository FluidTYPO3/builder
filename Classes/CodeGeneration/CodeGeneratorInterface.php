<?php
namespace FluidTYPO3\Builder\CodeGeneration;

interface CodeGeneratorInterface
{

    /**
     * @param boolean $dry
     * @return void
     * @abstract
     */
    public function setDry($dry);

    /**
     * @param boolean $verbose
     * @return void
     * @abstract
     */
    public function setVerbose($verbose);

    /**
     * @return string
     * @abstract
     */
    public function generate();

    /**
     * @param string $filePathAndFilename
     * @return void
     * @abstract
     */
    public function save($filePathAndFilename);
}
