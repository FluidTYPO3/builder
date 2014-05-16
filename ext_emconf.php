<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "builder".
 *
 * Auto generated 26-04-2014 04:38
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
	'version' => '0.11.0',
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
	'author_email' => 'claus@namelesscoder.net',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.1.0-6.2.99',
			'cms' => '',
			'extbase' => '',
			'fluid' => '',
			'vhs' => ''
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:62:{s:13:"composer.json";s:4:"667a";s:12:"ext_icon.gif";s:4:"e922";s:17:"ext_localconf.php";s:4:"9a4f";s:14:"ext_tables.php";s:4:"af08";s:10:"LICENSE.md";s:4:"c813";s:9:"README.md";s:4:"f147";s:22:"Build/ImportSchema.sql";s:4:"2b8e";s:28:"Build/LocalConfiguration.php";s:4:"7fdb";s:23:"Build/PackageStates.php";s:4:"92ea";s:28:"Build/UnitTestsBootstrap.php";s:4:"414d";s:36:"Classes/Analysis/AbstractMessage.php";s:4:"c89d";s:37:"Classes/Analysis/MessageInterface.php";s:4:"dbd7";s:27:"Classes/Analysis/Metric.php";s:4:"5ab9";s:34:"Classes/Analysis/NoticeMessage.php";s:4:"a3bd";s:30:"Classes/Analysis/OkMessage.php";s:4:"ba1e";s:35:"Classes/Analysis/WarningMessage.php";s:4:"fbd7";s:38:"Classes/Analysis/Fluid/NodeCounter.php";s:4:"2491";s:43:"Classes/Analysis/Fluid/TemplateAnalyzer.php";s:4:"b1f5";s:54:"Classes/Analysis/Fluid/Message/UncompilableMessage.php";s:4:"5c91";s:49:"Classes/CodeGeneration/AbstractClassGenerator.php";s:4:"4258";s:48:"Classes/CodeGeneration/AbstractCodeGenerator.php";s:4:"a964";s:50:"Classes/CodeGeneration/ClassGeneratorInterface.php";s:4:"c9c0";s:49:"Classes/CodeGeneration/CodeGeneratorInterface.php";s:4:"55d5";s:39:"Classes/CodeGeneration/CodeTemplate.php";s:4:"31e7";s:55:"Classes/CodeGeneration/Extension/ExtensionGenerator.php";s:4:"6b5d";s:44:"Classes/Command/BuilderCommandController.php";s:4:"1611";s:40:"Classes/Controller/BackendController.php";s:4:"421e";s:41:"Classes/Controller/FrontendController.php";s:4:"092d";s:42:"Classes/Parser/ExposedTemplateCompiler.php";s:4:"cc42";s:40:"Classes/Parser/ExposedTemplateParser.php";s:4:"49d4";s:36:"Classes/Result/FluidParserResult.php";s:4:"8847";s:31:"Classes/Result/ParserResult.php";s:4:"666e";s:40:"Classes/Result/ParserResultInterface.php";s:4:"bb71";s:40:"Classes/Service/ClassAnalysisService.php";s:4:"99f2";s:36:"Classes/Service/ExtensionService.php";s:4:"a31c";s:33:"Classes/Service/SyntaxService.php";s:4:"eba7";s:36:"Classes/Utility/ExtensionUtility.php";s:4:"635d";s:31:"Classes/Utility/GlobUtility.php";s:4:"6da2";s:33:"Migrations/Code/ClassAliasMap.php";s:4:"f5f7";s:57:"Resources/Private/CodeTemplates/Extension/ext_emconf.phpt";s:4:"4500";s:57:"Resources/Private/CodeTemplates/Extension/ext_tables.phpt";s:4:"3d83";s:67:"Resources/Private/CodeTemplates/Extension/TypoScript/constants.phpt";s:4:"504d";s:63:"Resources/Private/CodeTemplates/Extension/TypoScript/setup.phpt";s:4:"d21d";s:54:"Resources/Private/CodeTemplates/Fluid/FluxContent.phpt";s:4:"e5f2";s:51:"Resources/Private/CodeTemplates/Fluid/FluxForm.phpt";s:4:"9ba5";s:49:"Resources/Private/CodeTemplates/Fluid/Layout.phpt";s:4:"5dcf";s:40:"Resources/Private/Language/locallang.xml";s:4:"93ff";s:47:"Resources/Private/Language/locallang_module.xml";s:4:"9219";s:38:"Resources/Private/Layouts/Backend.html";s:4:"d6ad";s:46:"Resources/Private/Templates/Backend/Build.html";s:4:"67ce";s:46:"Resources/Private/Templates/Backend/Index.html";s:4:"4ee9";s:47:"Resources/Private/Templates/Backend/Syntax.html";s:4:"e7e1";s:53:"Resources/Public/Javascript/jqplot.barRenderer.min.js";s:4:"a4e0";s:64:"Resources/Public/Javascript/jqplot.canvasAxisTickRenderer.min.js";s:4:"358d";s:60:"Resources/Public/Javascript/jqplot.canvasTextRenderer.min.js";s:4:"e1ba";s:62:"Resources/Public/Javascript/jqplot.categoryAxisRenderer.min.js";s:4:"4e6b";s:48:"Resources/Public/Javascript/jqplot.cursor.min.js";s:4:"72f4";s:41:"Resources/Public/Javascript/jqplot.min.js";s:4:"af7c";s:53:"Resources/Public/Javascript/jqplot.pointLabels.min.js";s:4:"9f0e";s:38:"Resources/Public/Javascript/plotter.js";s:4:"d09c";s:42:"Resources/Public/Stylesheet/jqplot.min.css";s:4:"310c";s:39:"Resources/Public/Stylesheet/plotter.css";s:4:"34f4";}',
	'suggests' => array(
	),
);

?>
