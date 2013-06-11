	/**
	 * @test
	 */
	public function canSetViewHelperNode() {
		$instance = $this->getPreparedInstance();
		$arguments = $instance->prepareArguments();
		$node = new ###nodeclass###($instance, $arguments);
		$instance->setViewHelperNode($node);
	}
