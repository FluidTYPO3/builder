<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "builder".
 *
 * Auto generated 20-07-2013 18:39
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Builder: Development Support for Fluid Templates and Extbase extensions',
	'description' => 'Various development supports for building and working with Fluid templates and Extbase extensions',
	'category' => 'misc',
	'shy' => 0,
	'version' => '0.10.0',
	'dependencies' => 'cms,extbase,fluid',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Claus Due',
	'author_email' => 'claus@wildside.dk',
	'author_company' => 'Wildside A/S',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.7.0-6.2.99',
			'cms' => '',
			'extbase' => '',
			'fluid' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:30:{s:12:"ext_icon.gif";s:4:"e922";s:14:"ext_tables.php";s:4:"c0d8";s:9:"README.md";s:4:"96f4";s:49:"Classes/CodeGeneration/AbstractClassGenerator.php";s:4:"e17a";s:48:"Classes/CodeGeneration/AbstractCodeGenerator.php";s:4:"9d3a";s:50:"Classes/CodeGeneration/ClassGeneratorInterface.php";s:4:"4534";s:49:"Classes/CodeGeneration/CodeGeneratorInterface.php";s:4:"8343";s:39:"Classes/CodeGeneration/CodeTemplate.php";s:4:"931b";s:55:"Classes/CodeGeneration/Extension/ExtensionGenerator.php";s:4:"a1e3";s:62:"Classes/CodeGeneration/Testing/ViewHelperTestCaseGenerator.php";s:4:"d1b1";s:44:"Classes/Command/BuilderCommandController.php";s:4:"a139";s:36:"Classes/Result/FluidParserResult.php";s:4:"f317";s:31:"Classes/Result/ParserResult.php";s:4:"ffcc";s:40:"Classes/Result/ParserResultInterface.php";s:4:"7b6e";s:33:"Classes/Service/SyntaxService.php";s:4:"7989";s:31:"Classes/Utility/GlobUtility.php";s:4:"70be";s:57:"Resources/Private/CodeTemplates/Extension/ext_emconf.phpt";s:4:"8cff";s:57:"Resources/Private/CodeTemplates/Extension/ext_tables.phpt";s:4:"3d83";s:67:"Resources/Private/CodeTemplates/Extension/TypoScript/constants.phpt";s:4:"0711";s:63:"Resources/Private/CodeTemplates/Extension/TypoScript/setup.phpt";s:4:"9dcd";s:54:"Resources/Private/CodeTemplates/Fluid/FluxContent.phpt";s:4:"1564";s:51:"Resources/Private/CodeTemplates/Fluid/FluxForm.phpt";s:4:"e912";s:49:"Resources/Private/CodeTemplates/Fluid/Layout.phpt";s:4:"5dcf";s:62:"Resources/Private/CodeTemplates/ViewHelper/TestCase/Class.phpt";s:4:"e9fe";s:83:"Resources/Private/CodeTemplates/ViewHelper/TestCase/Method/CanCreateViewHelper.phpt";s:4:"63c5";s:87:"Resources/Private/CodeTemplates/ViewHelper/TestCase/Method/CanInitializeViewHelper.phpt";s:4:"13ee";s:83:"Resources/Private/CodeTemplates/ViewHelper/TestCase/Method/CanPrepareArguments.phpt";s:4:"eb57";s:84:"Resources/Private/CodeTemplates/ViewHelper/TestCase/Method/CanSetViewHelperNode.phpt";s:4:"481c";s:83:"Resources/Private/CodeTemplates/ViewHelper/TestCase/Method/InjectObjectManager.phpt";s:4:"0320";s:85:"Resources/Private/CodeTemplates/ViewHelper/TestCase/Method/PrepareInstanceMethod.phpt";s:4:"973d";}',
	'suggests' => array(
	),
);

?>
