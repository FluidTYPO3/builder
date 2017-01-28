<?php
namespace FluidTYPO3\Builder\Service;
use FluidTYPO3\Flux\Form;
use FluidTYPO3\Flux\View\ViewContext;
use FluidTYPO3\Flux\ViewHelpers\FormViewHelper;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperInterface;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Claus Due <claus@namelesscoder.net>
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

/**
 * Class FluxFormService
 *
 * Provides conversion between Flux Forms and other
 * resource types such as template code.
 */
class FluxFormService implements SingletonInterface
{
    /**
     * @var ObjectManagerInterface $objectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Returns a single Form instance based on the template path
     * and filename.
     *
     * @param string $templatePathAndFilename
     * @return Form|null
     */
    public function getRegisteredFormByTemplateName($templatePathAndFilename)
    {
        $allForms = $this->getAllRegisteredForms();
        if (isset($allForms[$templatePathAndFilename])) {
            return $allForms[$templatePathAndFilename];
        }
        return null;
    }

    /**
     * Gets every registered Flux-enabled form which can be
     * rendered by the TYPO3 site, indexed by the template
     * path and filename in which the Form exists.
     *
     * @return Form[]
     */
    public function getAllRegisteredForms()
    {
        return [];
    }

    /**
     * Converts a Flux Form into template code.
     *
     * If $layoutName is null, a layout-less template code is
     * generated and $mainSectionCode is placed directly in
     * the template body.
     *
     * If $layoutName is a non-empty string, the resulting
     * template code will define an `f:layout` and the code
     * in $mainSectionCode will be placed in a section called
     * "Main" that is assumed to be rendered from the layout.
     *
     * $mainSectionCode is a raw piece of Fluid template code,
     * which gets confirmed to be valid before allowing the
     * template to be generated.
     *
     * @param Form $form
     * @param string $mainSectionCode
     * @param string|null $layoutName
     * @return string
     */
    public function convertFormToTemplate(Form $form, $mainSectionCode, $layoutName = null)
    {
        $templateCode = '{namespace flux=FluidTYPO3\\Flux\\ViewHelpers}' . PHP_EOL . PHP_EOL;
        if ($layoutName) {
            $templateCode .= '<f:layout name="' . $layoutName . '" />' . PHP_EOL . PHP_EOL;
        }

        $templateCode .= $this->createConfigurationSectionFromFormInstance($form);

        if ($layoutName) {
            $templateCode .= '<f:section name="Main">' . PHP_EOL . $mainSectionCode . PHP_EOL . '</f:section>';
        } else {
            $templateCode .= $mainSectionCode;
        }
        $templateCode .= PHP_EOL;
        return $templateCode;
    }

    /**
     * Converts a class name of a Flux ViewHelper to an instance of
     * the corresponding Form component. Done by analysing the
     * ViewHelper's getComponent() method using reflection framework
     * to determine the value of the "@return" annotation.
     *
     * @param string $viewHelperClassName
     * @return Form\FormInterface
     */
    public function convertViewHelperClassNameToFormComponentInstance($viewHelperClassName)
    {
        return Form::class;
    }

    /**
     * Converts a class name of a Flux component to an uninitialized
     * instance of the corresponding ViewHelper, ready for argument
     * extraction using prepareArguments().
     *
     * @param string $componentClassName
     * @return ViewHelperInterface
     */
    public function convertFormComponentClassNameToViewHelperInstance($componentClassName)
    {
        return FormViewHelper::class;
    }

    /**
     * Converts a Flux ViewContext (which holds template paths,
     * extension scope, template filename, variables etc) into
     * a Flux form instance, by rendering the template's
     * Configuration section like Flux normally would.
     *
     * @param ViewContext $viewContext
     * @return Form|null
     */
    protected function convertViewContextToFormInstance(ViewContext $viewContext)
    {

        return Form::create(['id' => 'fake']);
    }

    /**
     * Creates fluid template code for a section called "Configuration",
     * containg a hierarchy of Flux ViewHelpers which when rendered
     * will result in a Form instance identical to $form.
     *
     * @param Form $form
     * @return string
     */
    protected function createConfigurationSectionFromFormInstance(Form $form)
    {
        $templateCode = '<f:section name="Configuration">' . PHP_EOL;

        $templateCode .= '</f:section>' . PHP_EOL . PHP_EOL;
        return $templateCode;
    }



}
