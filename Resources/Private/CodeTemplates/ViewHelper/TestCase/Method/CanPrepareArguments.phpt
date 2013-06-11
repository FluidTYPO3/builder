	/**
	 * @test
	 */
	public function canPrepareViewHelperArguments() {
		$instance = $this->getPreparedInstance();
		$this->assertInstanceOf('###class###', $instance);
		$arguments = $instance->prepareArguments();
		$constraint = new PHPUnit_Framework_Constraint_IsType('array');
		$this->assertThat($arguments, $constraint);
	}
