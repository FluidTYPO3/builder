<?php
namespace FluidTYPO3\Builder\CodeGeneration;

interface ClassGeneratorInterface extends CodeGeneratorInterface {

	/**
	 * @param string $name
	 * @return void
	 * @abstract
	 */
	public function setClassName($name);

	/**
	 * @param $attributes
	 * @return void
	 * @abstract
	 */
	public function setClassAttributes($attributes);

	/**
	 * @param string $templateIdentifier
	 * @param array $variables
	 * @return void
	 * @abstract
	 */
	public function appendMethodFromSourceTemplate($templateIdentifier, $variables);

	/**
	 * @param string $name
	 * @param string $type
	 * @return void
	 * @abstract
	 */
	public function appendProperty($name, $type);

}
