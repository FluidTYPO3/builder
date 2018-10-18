<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}


if (!(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['builder']['setup'] = unserialize($_EXTCONF);

    if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['builder']['setup']['enableDoodlePlugin'])) {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('FluidTYPO3.Builder', 'Doodle', 'Builder: Doodle plugin', 'EXT:builder/ext_icon.gif');

        if ('BE' === TYPO3_MODE) {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'FluidTYPO3.Builder',
                'tools',
                'txbuilderM1',
                '',
                array(
                    'Backend' => 'index,syntax,build,buildForm',
                ),
                array(
                    'access' => 'user,group',
                    'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/' .
                        (6.2 === (float) substr(TYPO3_version, 0, 3) ? 'builder.gif' : 'module_builder.png'),
                    'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf'
                )
            );

            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
                'builder',
                'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf'
            );

        }

    }
    if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['builder']['setup']['enableFrontendPlugin'])) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('FluidTYPO3.Builder', 'Frontend', 'Builder: Frontend access to builder', 'EXT:builder/ext_icon.gif');
    }
}
