<?php
namespace FluidTYPO3\Builder\Parser;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use TYPO3\CMS\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3\CMS\Fluid\Core\Parser\ParsingState;

/**
 * Class ExposedTemplateCompiler
 * @package FluidTYPO3\Builder\Parser
 */
class ExposedTemplateCompiler extends TemplateCompiler {

	/**
	 * @param ParsingState $parsingState
	 * @return string
	 */
	public function compile(ParsingState $parsingState) {
		$identifier = spl_object_hash($parsingState);
		$identifier = $this->sanitizeIdentifier($identifier);
		$this->variableCounter = 0;
		$generatedRenderFunctions = '';

		if ($parsingState->getVariableContainer()->exists('sections')) {
			$sections = $parsingState->getVariableContainer()->get('sections');
			// TODO: refactor to $parsedTemplate->getSections()
			foreach ($sections as $sectionName => $sectionRootNode) {
				$generatedRenderFunctions .= $this->generateCodeForSection($this->convertListOfSubNodes($sectionRootNode), 'section_' . sha1($sectionName), 'section ' . $sectionName);
			}
		}
		$generatedRenderFunctions .= $this->generateCodeForSection($this->convertListOfSubNodes($parsingState->getRootNode()), 'render', 'Main Render function');
		$convertedLayoutNameNode = $parsingState->hasLayout() ? $this->convert($parsingState->getLayoutNameNode()) : array('initialization' => '', 'execution' => 'NULL');

		$classDefinition = 'class FluidCache_' . $identifier . ' extends \TYPO3\CMS\Fluid\Core\Compiler\AbstractCompiledTemplate';

		$templateCode = '
%s {

public function getVariableContainer() {
	// TODO
	return new \TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer();
}
public function getLayoutName(\TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface \$renderingContext) {
%s
return %s;
}
public function hasLayout() {
return %s;
}

%s

}
';
		$templateCode = sprintf($templateCode,
			$classDefinition,
			$convertedLayoutNameNode['initialization'],
			$convertedLayoutNameNode['execution'],
			($parsingState->hasLayout() ? 'TRUE' : 'FALSE'),
			$generatedRenderFunctions);
		return $templateCode;
	}


}
