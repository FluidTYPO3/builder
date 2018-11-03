<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}


if (!(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {

    if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['builder']['setup']['enableDoodlePlugin'])) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('FluidTYPO3.Builder', 'Doodle', 'Builder: Doodle plugin', 'EXT:builder/ext_icon.gif');
    }
    if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['builder']['setup']['enableFrontendPlugin'])) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('FluidTYPO3.Builder', 'Frontend', 'Builder: Frontend access to builder', 'EXT:builder/ext_icon.gif');
    }
}
