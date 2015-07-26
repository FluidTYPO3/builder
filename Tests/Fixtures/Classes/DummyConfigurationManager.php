<?php
namespace FluidTYPO3\Builder\Tests\Fixtures\Classes;

use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class DummyConfigurationManager
 */
class DummyConfigurationManager extends BackendConfigurationManager implements ConfigurationManagerInterface {

	/**
	 * @return ContentObjectRenderer
	 */
	public function getContentObject() {
		$builder = new \PHPUnit_Framework_MockObject_Generator();
		$renderer = $builder->getMock('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer', ['RECORDS']);
		return $renderer;
	}

	/**
	 * @param string $type
	 * @param string $extensionName
	 * @param string $pluginName
	 * @return array
	 */
	public function getConfiguration($type, $extensionName = NULL, $pluginName = NULL) {
		return [
			'plugin.' => [
				'tx_builder.' => [
					'view.' => [],
					'settings.' => [],
				]
			]
		];
	}

	/**
	 * @param string $featureName
	 * @return boolean
	 */
	public function isFeatureEnabled($featureName) {
		TRUE;
	}

}
