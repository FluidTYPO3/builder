<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (!(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {
    (function() {
        if ('BE' === TYPO3_MODE) {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'FluidTYPO3.Builder',
                'tools',
                'txbuilderM1',
                '',
                [
                    'Backend' => 'index,syntax,build,buildForm',
                ],
                [
                    'access' => 'user,group',
                    'icon' => 'EXT:builder/Resources/Public/Icons/module_builder.png',
                    'labels' => 'LLL:EXT:builder/Resources/Private/Language/locallang.xlf'
                ]
            );

            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
                'builder',
                'EXT:builder/Resources/Private/Language/locallang.xlf'
            );
        }

    })();
}
