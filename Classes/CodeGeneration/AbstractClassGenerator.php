<?php

abstract class Tx_Builder_CodeGeneration_AbstractClassGenerator extends Tx_Builder_CodeGeneration_AbstractCodeGenerator implements Tx_Builder_CodeGeneration_ClassGeneratorInterface {

	/**
	 * @var string
	 */
	protected $name = NULL;

	/**
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * @var array
	 */
	protected $properties = array();

	/**
	 * @var array
	 */
	protected $methods = array();

	/**
	 * @param string $name
	 * @return void
	 * @abstract
	 */
	public function setClassName($name) {
		$this->name = $name;
	}

	/**
	 * @param $attributes
	 * @return void
	 * @abstract
	 */
	public function setClassAttributes($attributes) {
		$this->attributes = $attributes;
	}


	/**
	 * @param string $templateIdentifier
	 * @param array $variables
	 * @return void
	 * @abstract
	 */
	public function appendMethodFromSourceTemplate($templateIdentifier, $variables) {
		$name = $variables['name'];
		$template = $this->getPreparedCodeTemplate($templateIdentifier, $variables);
		$code = $template->render();
		$this->methods[$name] = $code;
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @return void
	 * @abstract
	 */
	public function appendProperty($name, $type) {
		$code = "\t/**\n\t * @var " . $name . " " . $type;
		$this->properties[$name] = $code;
	}

	/**
	 * @param string $template
	 * @param string $className
	 * @return string
	 */
	public function renderClass($template, $className) {
		if (NULL === $className) {
			return NULL;
		}
		$this->appendCommonTestMethods();
		$variables = array(
			'class' => $className,
			'properties' => implode("\n\n", $this->properties),
			'methods' => implode("\n\n", $this->methods)
		);
		$template = $this->getPreparedCodeTemplate($template, $variables);
		return $template->render();
	}

}
