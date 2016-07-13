<?php
namespace FluidTYPO3\Builder\Tests\Fixtures\Classes;

use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class DummyConfigurationManager
 */
class DummyConfigurationManager extends BackendConfigurationManager implements ConfigurationManagerInterface
{

    /**
     * @return ContentObjectRenderer
     */
    public function getContentObject()
    {
        $builder = new \PHPUnit_Framework_MockObject_Generator();
        $renderer = $builder->getMock('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer', array('RECORDS'));
        return $renderer;
    }

    /**
     * @param string $type
     * @param string $extensionName
     * @param string $pluginName
     * @return array
     */
    public function getConfiguration($type, $extensionName = null, $pluginName = null)
    {
        return array(
            'plugin.' => array(
                'tx_builder.' => array(
                    'view.' => array(),
                    'settings.' => array(),
                )
            )
        );
    }

    /**
     * @param string $featureName
     * @return boolean
     */
    public function isFeatureEnabled($featureName)
    {
        true;
    }
}
