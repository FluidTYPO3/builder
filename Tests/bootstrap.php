<?php
// Register composer autoloader
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
	throw new \RuntimeException(
		'Could not find vendor/autoload.php, make sure you ran composer.'
	);
}

/** @var Composer\Autoload\ClassLoader $autoloader */
$autoloader = require __DIR__ . '/../vendor/autoload.php';
$autoloader->addPsr4('FluidTYPO3\\Builder\\Tests\\Fixtures\\', __DIR__ . '/Fixtures/');

define('PATH_thisScript', realpath('vendor/typo3/cms/typo3/index.php'));
define('TYPO3_MODE', 'BE');
putenv('TYPO3_CONTEXT=Testing');

$nullCache = array(
	'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\VariableFrontend',
	'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\NullBackend'
);
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'] = array(
	'extbase_typo3dbbackend_queries' => $nullCache,
	'extbase_typo3dbbackend_tablecolumns' => $nullCache,
	'extbase_datamapfactory_datamap' => $nullCache,
	'extbase_object' => $nullCache,
	'extbase_reflection' => $nullCache
);

\TYPO3\CMS\Core\Core\Bootstrap::getInstance()
	->baseSetup('typo3/')
	->initializeClassLoader()

	->initializeCachingFramework();
/** @var $extbaseObjectContainer \TYPO3\CMS\Extbase\Object\Container\Container */
$extbaseObjectContainer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\Container\\Container');
$extbaseObjectContainer->registerImplementation('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface', 'FluidTYPO3\\Builder\\Tests\\Fixtures\\Classes\\DummyConfigurationManager');
$extbaseObjectContainer->registerImplementation(
	'TYPO3\\CMS\\Extbase\\Persistence\\PersistenceManagerInterface',
	'FluidTYPO3\\Builder\\Tests\\Fixtures\\Classes\\DummyPersistenceManager'
);
$extbaseObjectContainer->registerImplementation(
	'TYPO3\\CMS\\Extbase\\Persistence\\Generic\\BackendInterface',
	'FluidTYPO3\\Builder\\Tests\\Fixtures\\Classes\\DummyPersistenceBackend'
);

unset($extbaseObjectContainer);
