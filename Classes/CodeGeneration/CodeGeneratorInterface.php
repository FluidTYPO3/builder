<?php

interface Tx_Builder_CodeGeneration_CodeGeneratorInterface {

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