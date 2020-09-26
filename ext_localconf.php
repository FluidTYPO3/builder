<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (!(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \FluidTYPO3\Builder\Command\BuilderCommandController::class;
}
