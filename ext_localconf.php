<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('FluidTYPO3.Builder', 'Frontend', array('Frontend' => 'build'), array('Frontend' => 'build'));
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('FluidTYPO3.Builder', 'Doodle', array('Frontend' => 'doodle,renderFluid'), array('Frontend' => 'renderFluid'));
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('FluidTYPO3.Builder', 'Render', array('Frontend' => 'renderFluid'), array('Frontend' => 'renderFluid'));

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
[GLOBAL]
builderDoodle = PAGE
builderDoodle {
	typeNum = 9967
	config {
		no_cache = 1
		disableAllHeaderCode = 1
	}
	9967 = USER
	9967 {
		userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
		vendorName = FluidTYPO3
		extensionName = Builder
		pluginName = Render
	}
}
[GLOBAL]
');
