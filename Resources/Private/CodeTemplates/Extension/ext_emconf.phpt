<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "###extensionKey###".
 *
 * Auto generated ###date###
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
	'title' => '###title###',
	'description' => '###description###',
	'category' => 'misc',
	'shy' => 0,
	'version' => '0.0.1',
	'dependencies' => 'cms,extbase,fluid,flux###dependenciesCsv###',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'experimental',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => '###author###',
	'author_email' => '###email###',
	'author_company' => '###company###',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => [
		'depends' => [
			'typo3' => '###coreMinor###-7.1.99',
			'cms' => '',
			'extbase' => '',
			'fluid' => '',
			'flux' => '',
			###dependenciesArray###
		],
		'conflicts' => [
		],
		'suggests' => [
		],
	],
	'_md5_values_when_last_written' => 'a:0:{}',
	'suggests' => [
	],
];
