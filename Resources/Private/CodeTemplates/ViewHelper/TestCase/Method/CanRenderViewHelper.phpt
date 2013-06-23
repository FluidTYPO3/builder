	/**
	 * @test
	 */
	public function canRenderWithoutProvidedArguments() {
		$instance = $this->getPreparedInstance();
		$this->assertInstanceOf('###class###', $instance);
		$instance->render();
	}
