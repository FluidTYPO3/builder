<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin('builder', 'frontend', array('Frontend' => 'build'), array('Frontend' => 'build'));
