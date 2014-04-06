<?php
namespace FluidTYPO3\Builder\Build;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Christian Kuhn <lolli@schwarzbu.ch>
 *  (c) 2013 Helmut Hummel <helmut.hummel@typo3.org>
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This file is defined in UnitTests.xml and called by phpunit
 * before instantiating the test suites, it must also be included
 * with phpunit parameter --bootstrap if executing single test case classes.
 *
 * For easy access to the PHPUnit and VFS framework, it is recommended to install the phpunit TYPO3 Extension
 * It does not need to be activated, nor a cli user needs to be present.
 * But it is also possible to use other installations of PHPUnit and VFS
 *
 *  * Call whole unit test suite, example:
 * - cd /var/www/t3master/foo  # Document root of TYPO3 CMS instance (location of index.php)
 * - typo3conf/ext/phpunit/Composer/vendor/bin/phpunit -c typo3/sysext/core/Build/UnitTests.xml
 *
 * Call single test case, example:
 * - cd /var/www/t3master/foo  # Document root of TYPO3 CMS instance (location of index.php)
 * - typo3conf/ext/phpunit/Composer/vendor/bin/phpunit \
 *     --bootstrap typo3/sysext/core/Build/UnitTestsBootstrap.php \
 *     typo3/sysext/core/Tests/Uinit/DataHandling/DataHandlerTest.php
 */

/**
 * Be nice and give a hint if someone is executing the tests with cli dispatch
 */
if (defined('TYPO3_MODE')) {
	array_shift($_SERVER['argv']);
	echo 'Please run the unit tests using the following command:' . chr(10);
	echo sprintf(
			'typo3conf/ext/phpunit/Composer/vendor/bin/phpunit %s',
			implode(' ', $_SERVER['argv'])
		) . chr(10);
	echo chr(10);
	exit(1);
}

/**
 * Find out web path by environment variable or current working directory
 */
if (getenv('TYPO3_PATH_WEB')) {
	$webRoot = getenv('TYPO3_PATH_WEB') . '/';
} else {
	$webRoot = getcwd() . '/';
}

/**
 * Fail if configuration is not found
 */
if (!file_exists($webRoot . 'typo3conf/LocalConfiguration.php')) {
	throw new \Exception('TYPO3 web root not found. Call PHPUnit from that directory or set TYPO3_PATH_WEB to it.');
}

/**
 * Define basic TYPO3 constants
 */
define('PATH_site', $webRoot);
define('TYPO3_MODE', 'BE');
define('TYPO3_cliMode', TRUE);

unset($webRoot);

putenv('TYPO3_CONTEXT=Testing');

require PATH_site . '/typo3/sysext/core/Classes/Core/CliBootstrap.php';
require PATH_site . '/typo3/sysext/core/Classes/Core/Bootstrap.php';

$GLOBALS['MCONF']['name'] = '_CLI_phpunit';

\TYPO3\CMS\Core\Core\Bootstrap::getInstance()
	->baseSetup('typo3/')
	->loadConfigurationAndInitialize()
	->loadTypo3LoadedExtAndExtLocalconf(TRUE)
	->applyAdditionalConfigurationSettings()
	->initializeTypo3DbGlobal()
	->loadExtensionTables(TRUE)
	->initializeBackendUser()
	->initializeBackendAuthentication()
	->initializeBackendUserMounts()
	->initializeLanguageObject()
	->disableCoreAndClassesCache();

require PATH_site . 'typo3conf/ext/phpunit/Composer/vendor/autoload.php';

\TYPO3\CMS\Core\Core\Bootstrap::getInstance()->shutdown();
