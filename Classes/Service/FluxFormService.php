<?php
namespace FluidTYPO3\Builder\Service;
use FluidTYPO3\Flux\Core;
use FluidTYPO3\Flux\Form;
use FluidTYPO3\Flux\Service\FluxService;
use FluidTYPO3\Flux\View\ViewContext;
use FluidTYPO3\Flux\ViewHelpers\Field\CheckboxViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Field\CustomViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Field\FileViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Field\InlineViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Field\InputViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Field\MultiRelationViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Field\RadioViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Field\RelationViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Field\SelectViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Field\TextViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Field\TreeViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Field\UserFuncViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Form\ContainerViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Form\ObjectViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Form\SectionViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Form\SheetViewHelper;
use FluidTYPO3\Flux\ViewHelpers\FormViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Grid\ColumnViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Grid\RowViewHelper;
use FluidTYPO3\Flux\ViewHelpers\GridViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Wizard\AddViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Wizard\ColorPickerViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Wizard\EditViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Wizard\LinkViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Wizard\ListViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Wizard\SliderViewHelper;
use FluidTYPO3\Flux\ViewHelpers\Wizard\SuggestViewHelper;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperInterface;
use TYPO3\CMS\Fluid\View\TemplatePaths;

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
     * @var FluxService
     */
    protected $fluxService;

    /**
     * @var array
     */
    protected $objectTypeMap = [
        // Containers:
        Form::class => FormViewHelper::class,
        Form\Container\Sheet::class => SheetViewHelper::class,
        Form\Container\Section::class => SectionViewHelper::class,
        Form\Container\Object::class => ObjectViewHelper::class,
        Form\Container\Container::class => ContainerViewHelper::class,
        // Fields:
        Form\Field\Input::class => InputViewHelper::class,
        Form\Field\Text::class => TextViewHelper::class,
        Form\Field\Checkbox::class => CheckboxViewHelper::class,
        Form\Field\Radio::class => RadioViewHelper::class,
        Form\Field\Custom::class => CustomViewHelper::class,
        Form\Field\UserFunction::class => UserFuncViewHelper::class,
        // TODO: Form\Field\DateTime::class => DateTimeViewHelper::class,
        Form\Field\File::class => FileViewHelper::class,
        // Type "flex" is not mappable.
        // Type "passthrough" is not mappable.
        Form\Field\Inline::class => InlineViewHelper::class,
        Form\Field\Relation::class => RelationViewHelper::class,
        Form\Field\MultiRelation::class => MultiRelationViewHelper::class,
        Form\Field\Tree::class => TreeViewHelper::class,
        Form\Field\Select::class => SelectViewHelper::class,
        // Wizards:
        Form\Wizard\Add::class => AddViewHelper::class,
        Form\Wizard\ColorPicker::class => ColorPickerViewHelper::class,
        Form\Wizard\Edit::class => EditViewHelper::class,
        Form\Wizard\Link::class => LinkViewHelper::class,
        Form\Wizard\ListWizard::class => ListViewHelper::class,
        Form\Wizard\Select::class => \FluidTYPO3\Flux\ViewHelpers\Wizard\SelectViewHelper::class,
        Form\Wizard\Slider::class => SliderViewHelper::class,
        Form\Wizard\Suggest::class => SuggestViewHelper::class,
        // Grid:
        Form\Container\Grid::class => GridViewHelper::class,
        Form\Container\Row::class => RowViewHelper::class,
        Form\Container\Column::class => ColumnViewHelper::class
    ];

    /**
     * @param ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param FluxService $fluxService
     * @return void
     */
    public function injectFluxService(FluxService $fluxService)
    {
        $this->fluxService = $fluxService;
    }

    /**
     * Returns a Form+Grid instance based on the template path
     * and filename, as ['form' => $form, 'grid' => $grid].
     *
     * @param string $templatePathAndFilename
     * @return array|null
     */
    public function getRegisteredFormAndGridByTemplateName($templatePathAndFilename)
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
     * Returns an array of associative arrays each containing
     * a "form" and a "grid":
     *
     * [$templateFile => ['form' => $form, 'grid' => $grid]]
     *
     * @return array
     */
    public function getAllRegisteredForms()
    {
        $formsAndGrids = [];

        // First, read all configured Provider instances. Special implementations such as fluidpages included.
        $providers = Core::getRegisteredFlexFormProviders();
        foreach ($providers as $provider) {
            if (is_string($provider)) {
                $provider = $this->objectManager->get($provider);
            }
            // Check if provider is able to return a template file directly, indicating it was hardcoded
            // or force-configured to work with that template file:
            try {
                $templatePathAndFilename = $provider->getTemplatePathAndFilename([]);
                if ($templatePathAndFilename) {
                    // The Provider returns a template filename - it most likely also can return a ViewContext which
                    // can render the template:
                    $viewContext = $provider->getViewContext([]);
                    $form = $this->fluxService->getFormFromTemplateFile($viewContext);
                    if ($form) {
                        $formsAndGrids[$templatePathAndFilename] = [
                            'form' => $form,
                            'grid' => $this->fluxService->getGridFromTemplateFile($viewContext)
                        ];
                    }
                } else {
                    // Provider is NOT able to return a template path and filename. We then check the
                    // class name of the provider which by convention should match a controller name
                    // which we can then scan for template files:
                    $controllerName = $provider->getControllerNameFromRecord([]);
                    foreach (Core::getRegisteredProviderExtensionKeys($controllerName) as $providerExtensionKey) {
                        /** @var TemplatePaths $templatePaths */
                        $templatePaths = $this->objectManager->get(TemplatePaths::class);
                        $templatePaths->fillDefaultsByPackageName($providerExtensionKey);
                        $viewContext = $provider->getViewContext([]);
                        $viewContext->setControllerName($controllerName);
                        $viewContext->setPackageName($providerExtensionKey);
                        $viewContext->setTemplatePaths($templatePaths);
                        foreach ($templatePaths->resolveAvailableTemplateFiles($controllerName) as $templateFile) {
                            $viewContext->setTemplatePathAndFilename($templateFile);
                            $formsAndGrids[$templateFile] = [
                                'form' => $this->fluxService->getFormFromTemplateFile($viewContext),
                                'grid' => $this->fluxService->getGridFromTemplateFile($viewContext)
                            ];
                        }
                    }
                }
            } catch (\RuntimeException $error) {
                // TODO: error messaging?
            }
        }

        return $formsAndGrids;
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
     * the corresponding Form component.
     *
     * @param string $viewHelperClassName
     * @return Form\FormInterface
     */
    public function convertViewHelperClassNameToFormComponentInstance($viewHelperClassName)
    {
        return call_user_func(array_search($viewHelperClassName, $this->objectTypeMap), 'create');
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
        return $this->objectManager->get($this->objectTypeMap[$componentClassName]);
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
