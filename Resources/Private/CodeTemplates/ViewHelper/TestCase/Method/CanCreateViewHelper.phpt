	/**
	 * @test
	 */
	public function canCreateViewHelperClassInstance() {
		$instance = $this->getPreparedInstance();
		$this->assertInstanceOf('###class###', $instance);
	}
