<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('FluidTYPO3.Builder', 'frontend', array('Frontend' => 'build'), array('Frontend' => 'build'));
