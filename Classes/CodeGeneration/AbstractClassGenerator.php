<?php
namespace FluidTYPO3\Builder\CodeGeneration;

use FluidTYPO3\Builder\Service\ClassAnalysisService;

abstract class AbstractClassGenerator extends AbstractCodeGenerator implements ClassGeneratorInterface {

	/**
	 * @var ClassAnalysisService
	 */
	protected $classAnalysisService;

	/**
	 * @var string
	 */
	protected $name = NULL;

	/**
	 * @var string
	 */
	protected $author = NULL;

	/**
	 * @var string
	 */
	protected $package = NULL;

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
	 * @param ClassAnalysisService $classAnalysisService
	 * @return void
	 */
	public function injectClassAnalysisService(ClassAnalysisService $classAnalysisService) {
		$this->classAnalysisService = $classAnalysisService;
	}

	/**
	 * @param string $name
	 * @return void
	 * @abstract
	 */
	public function setClassName($name) {
		$this->name = $name;
	}

	/**
	 * @param string $author
	 */
	public function setAuthor($author) {
		$this->author = $author;
	}

	/**
	 * @param string $package
	 */
	public function setPackage($package) {
		$this->package = $package;
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
	public function appendMethodFromSourceTemplate($templateIdentifier, $variables = array()) {
		$name = TRUE === isset($variables['name']) ? $variables['name'] : basename($templateIdentifier);
		$template = $this->getPreparedCodeTemplate($templateIdentifier, $variables);
		$code = $template->render();
		$this->methods[$name] = $code;
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @param string $visibility
	 * @return void
	 * @abstract
	 */
	public function appendProperty($name, $type, $visibility = 'protected') {
		$code = "\t/**\n\t * @var $" . $name . ' ' . $type . "\n\t */\n\t" . $visibility . ' $' . $name . ';';
		$this->properties[$name] = $code;
	}

	/**
	 * @param string $filePathAndFilename
	 * @return void
	 */
	public function save($filePathAndFilename) {
		$code = $this->generate();
		$shouldBeWritten = FALSE;
		if (FALSE === file_exists($filePathAndFilename)) {
			$shouldBeWritten = TRUE;
		} else {
			$contents = file_get_contents($filePathAndFilename);
			if (FALSE !== strpos($contents, '@protection off')) {
				unlink($filePathAndFilename);
				// class file contains marker which allows overwriting without further ado
				$shouldBeWritten = TRUE;
			}
		}
		if (TRUE === $shouldBeWritten) {
			t3lib_div::writeFile($filePathAndFilename, $code);
		}
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
		$properties = array_map('trim', $this->properties);
		$methods = array_map('trim', $this->methods);
		$this->appendCommonTestMethods();
		$variables = array(
			'class' => $className,
			'author' => $this->author,
			'year' => date('Y', time()),
			'protection' => 'off',
			'package' => $this->package,
			'properties' => implode("\n\n\t", $properties),
			'methods' => implode("\n\n\t", $methods)
		);
		$template = $this->getPreparedCodeTemplate($template, $variables);
		return $template->render();
	}

}
