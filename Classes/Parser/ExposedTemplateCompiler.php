<?php
namespace FluidTYPO3\Builder\Parser;

use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\Parser\ParsingState;

class ExposedTemplateCompiler extends TemplateCompiler
{
    /**
     * Overridden "store" method does not store - instead, it returns
     * the compiled result.
     *
     * @param string $identifier
     * @param ParsingState $parsingState
     * @return string
     */
    public function store($identifier, ParsingState $parsingState)
    {
        $identifier = $this->sanitizeIdentifier($identifier);
        $this->variableCounter = 0;
        $generatedRenderFunctions = '';

        if ($parsingState->getVariableContainer()->exists('sections')) {
            $sections = $parsingState->getVariableContainer()->get('sections');
            // @todo refactor to $parsedTemplate->getSections()
            foreach ($sections as $sectionName => $sectionRootNode) {
                $generatedRenderFunctions .= $this->generateCodeForSection(
                    $this->nodeConverter->convertListOfSubNodes($sectionRootNode),
                    'section_' . sha1($sectionName),
                    'section ' . $sectionName
                );
            }
        }
        $generatedRenderFunctions .= $this->generateCodeForSection(
            $this->nodeConverter->convertListOfSubNodes($parsingState->getRootNode()),
            'render',
            'Main Render function'
        );
        if ($parsingState->hasLayout() && method_exists($parsingState, 'getLayoutNameNode')) {
            $convertedLayoutNameNode = $this->nodeConverter->convert($parsingState->getLayoutNameNode());
        } elseif ($parsingState->hasLayout() && method_exists($parsingState, 'getLayoutName')) {
            $convertedLayoutNameNode = $parsingState->getLayoutName($this->renderingContext);
        } else {
            $convertedLayoutNameNode = ['initialization' => '', 'execution' => 'NULL'];
        }

        $classDefinition = 'class FluidCache_' . $identifier .
            ' extends \\TYPO3\\CMS\\Fluid\\Core\\Compiler\\AbstractCompiledTemplate';

        $templateCode = <<<EOD
%s {

public function getVariableContainer() {
	// @todo
	return new \TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer();
}
public function getLayoutName(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface \$renderingContext) {
\$currentVariableContainer = \$renderingContext->getTemplateVariableContainer();
%s
return %s;
}
public function hasLayout() {
return %s;
}

%s

}
EOD;
        return sprintf(
            $templateCode,
            $classDefinition,
            $convertedLayoutNameNode['initialization'],
            $convertedLayoutNameNode['execution'],
            ($parsingState->hasLayout() ? 'TRUE' : 'FALSE'),
            $generatedRenderFunctions
        );
    }
}
