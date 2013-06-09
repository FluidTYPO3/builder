	/**
	 * @return ###class###
	 * @support
	 */
	protected function getPreparedInstance() {
		$objectManager = \t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$instance = $objectManager->get('###class###');
		return $instance;
	}
