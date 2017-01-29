<?php
namespace FluidTYPO3\Builder\Service;

use FluidTYPO3\Flux\Core;
use FluidTYPO3\Flux\Form;
use FluidTYPO3\Flux\Service\FluxService;
use FluidTYPO3\Flux\Utility\ExtensionNamingUtility;
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
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
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
     * Array containing mapping of ViewHelper argument names to
     * Form component property names. Lookups happen by first
     * looking for a [$class][$property] value, then [$property].
     * If a certain argument applies only to some (or one) but
     * not all, they must only be specified as [$class][$property].
     *
     * @var array
     */
    protected $argumentToPropertyMap = [
        'displayCond' => 'displayCondition',
        'eval' => 'validate',
        ColumnViewHelper::class => [
            'colPos' => 'columnPosition'
        ]
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
                        $form->setOption(Form::OPTION_TEMPLATEFILE, $templatePathAndFilename);
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
                        $viewContext = $provider->getViewContext([]);
                        $viewContext->setPackageName($providerExtensionKey);

                        $providerExtensionKey = ExtensionNamingUtility::getExtensionKey($providerExtensionKey);

                        /** @var TemplatePaths $templatePaths */
                        $templatePaths = $this->objectManager->get(TemplatePaths::class);
                        $templatePaths->fillDefaultsByPackageName($providerExtensionKey);
                        $viewContext->setControllerName($controllerName);
                        $viewContext->setTemplatePaths(new \FluidTYPO3\Flux\View\TemplatePaths($providerExtensionKey));
                        foreach ($templatePaths->resolveAvailableTemplateFiles($controllerName) as $templateFile) {
                            $viewContext->setTemplatePathAndFilename($templateFile);
                            $form = $this->fluxService->getFormFromTemplateFile($viewContext);
                            if ($form) {
                                $form->setOption(Form::OPTION_TEMPLATEFILE, $templatePathAndFilename);
                                $formsAndGrids[$templateFile] = [
                                    'form' => $form,
                                    'grid' => $this->fluxService->getGridFromTemplateFile($viewContext)
                                ];
                            }
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
     * Converts a Flux Form and Grid into template code.
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
     * @param array $data
     * @param string $mainSectionCode
     * @param string|null $layoutName
     * @return string
     */
    public function convertDataToTemplate(array $data, $mainSectionCode, $layoutName = null)
    {
        $form = $data['form'];
        $grid = $data['grid'];

        $templateCode = '{namespace flux=FluidTYPO3\\Flux\\ViewHelpers}' . PHP_EOL . PHP_EOL;
        if ($layoutName) {
            $templateCode .= '<f:layout name="' . $layoutName . '" />' . PHP_EOL . PHP_EOL;
        }

        $templateCode .= $this->createConfigurationSectionFromFormAndGridInstances($form, $grid);

        if ($layoutName) {
            $templateCode .= '<f:section name="Main">' . PHP_EOL . $mainSectionCode . PHP_EOL . '</f:section>';
        } else {
            $templateCode .= $mainSectionCode;
        }
        $templateCode .= PHP_EOL;
        return $templateCode;
    }

    /**
     * Converts a Form instance to an array structure which
     * when passed to Form::create() results in a Form that
     * is identical to the input Form.
     *
     * @param Form $form
     * @return array
     */
    public function convertFormToStructure(Form $form)
    {
        return $this->extractPropertiesFromFormComponent($form);
    }

    /**
     * @param Form\Container\Grid $grid
     * @return array
     */
    public function convertGridToStructure(Form\Container\Grid $grid)
    {
        return $this->extractPropertiesFromObject($grid);
    }

    /**
     * Extracts properties from a FormInterface implementer,
     * returning an array of all properties which are NOT
     * the same value as the default value.
     *
     * @param Form\FormInterface $component
     * @return array
     */
    protected function extractPropertiesFromFormComponent(Form\FormInterface $component)
    {
        $structure = $this->extractPropertiesFromObject($component);
        $structure['type'] = get_class($component);
        return $structure;
    }

    /**
     * @param object $object
     * @return array
     */
    protected function extractPropertiesFromObject($object) {
        $classReflection = new \ReflectionClass(get_class($object));
        $defaultProperties = $classReflection->getDefaultProperties();
        $properties = [];
        foreach ($classReflection->getProperties() as $propertyReflection) {
            $name = $propertyReflection->getName();

            if ($name === 'parent') {
                continue;
            }

            $value = ObjectAccess::getProperty($object, $name, true);

            if (array_key_exists($name, $defaultProperties) && $defaultProperties[$name] === $value) {
                continue;
            }

            if ($value instanceof \SplObjectStorage) {
                $value = array_map([$this, 'extractPropertiesFromFormComponent'], iterator_to_array($value));
            } elseif ($value instanceof Form\FormInterface) {
                $value = $this->extractPropertiesFromFormComponent($value);
            } elseif (is_object($value)) {
                $value = $this->extractPropertiesFromObject($value);
            }

            if (is_array($value) && !count($value)) {
                continue;
            } elseif (is_null($value)) {
                continue;
            }

            $properties[$name] = $value;
        }

        return $properties;
    }

    /**
     * Converts a class name of a Flux ViewHelper to an instance of
     * the corresponding Form component.
     *
     * @param string|ViewHelperInterface $viewHelperClassName
     * @return Form\FormInterface
     */
    public function convertViewHelperClassNameToFormComponentInstance($viewHelperClassNameOrInstance)
    {
        $className = is_string($viewHelperClassNameOrInstance) ? $viewHelperClassNameOrInstance : get_class($viewHelperClassNameOrInstance);
        return call_user_func(array_search($className, $this->objectTypeMap), 'create');
    }

    /**
     * Converts a class name of a Flux component to an uninitialized
     * instance of the corresponding ViewHelper, ready for argument
     * extraction using prepareArguments().
     *
     * @param string|FormInterface $componentClassNameOrInstance
     * @return ViewHelperInterface
     */
    public function convertFormComponentClassNameToViewHelperInstance($componentClassNameOrInstance)
    {
        $className = is_string($componentClassNameOrInstance) ? $componentClassNameOrInstance : get_class($componentClassNameOrInstance);
        return $this->objectManager->get($this->objectTypeMap[$className]);
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
     * @param Form\Container\Grid $grid
     * @return string
     */
    protected function createConfigurationSectionFromFormAndGridInstances(Form $form, Form\Container\Grid $grid)
    {
        $templateCode = '<f:section name="Configuration">' . PHP_EOL;
        $templateCode .= $this->createFluidNodeFromFormObject($form);
        if (count($grid->getRows())) {
            $templateCode .= $this->createFluidNodeFromFormObject($grid);
        }
        $templateCode .= '</f:section>' . PHP_EOL . PHP_EOL;
        return $templateCode;
    }

    /**
     * @param Form\FormInterface $object
     * @param integer $indentation
     * @return string
     */
    protected function createFluidNodeFromFormObject(
        Form\FormInterface $object,
        $indentation = 1
    ) {
        $children = $this->extractChildrenFromFormObject($object);
        $viewHelper = $this->convertFormComponentClassNameToViewHelperInstance($object);
        $viewHelperClass = get_class($viewHelper);
        $viewHelperName = substr($viewHelperClass, strpos($viewHelperClass, '\\ViewHelpers\\') + 13, -10);
        $viewHelperName = implode('.', array_map('lcfirst', explode('\\', $viewHelperName)));
        $space = str_repeat('  ', $indentation);
        $fluidTemplateCode = $space . '<flux:' . $viewHelperName;
        $arguments = $viewHelper->prepareArguments();
        foreach ($arguments as $name => $argumentDefinition) {
            // "label" is extracted directly, bypassing the getter. This ensures that only when the label was
            // actually specified will it be reflected in the Fluid template. In other words: skips the attribute
            // if the generated value is an automatic LLL reference, or if label was copied from object name.
            $propertyName = $name;
            if (isset($this->argumentToPropertyMap[$viewHelperClass][$name])) {
                $propertyName = $this->argumentToPropertyMap[$viewHelperClass][$name];
            } elseif (isset($this->argumentToPropertyMap[$name])) {
                $propertyName = $this->argumentToPropertyMap[$name];
            }
            if ($name === 'label' || $name === 'description' || $name === 'shortDescription') {
                $value = ObjectAccess::getProperty($object, $propertyName, true);
                if ($value === ObjectAccess::getProperty($object, $name)) {
                    continue;
                }
            } elseif ($name === 'extensionName' && $object->getParent()) {// && $object->getParent() && $object->getParent()->getExtensionName() === $object->getExtensionName()) {
                continue;
            } elseif ($name === 'clear') {
                if (!$object->has($name)) {
                    continue;
                }
                $value = true;
            } else {
                $value = ObjectAccess::getProperty($object, $propertyName);
            }
            if ($name === 'options') {
                unset(
                    $value[Form::OPTION_TEMPLATEFILE],
                    $value[Form::OPTION_RECORD],
                    $value[Form::OPTION_RECORD_TABLE],
                    $value[Form::OPTION_RECORD_FIELD]
                );
            }
            if ($argumentDefinition->getType() === 'string' && $value === null) {
                continue;
            }
            if (is_array($value) && empty($value)) {
                continue;
            }
            // Note: loose comparison is intentional
            if ($value == $argumentDefinition->getDefaultValue()) {
                continue;
            }
            $fluidTemplateCode .= ' ' . $name . '="' . $this->convertValueToFluidVariable($value) . '"';
        }
        if (!count($children)) {
            $fluidTemplateCode .= ' />';
        } else {
            $fluidTemplateCode .= '>' . PHP_EOL;
            foreach ($children as $child) {
                $fluidTemplateCode .= $this->createFluidNodeFromFormObject($child, $indentation + 1);
            }
            $fluidTemplateCode .= $space . '</flux:' . $viewHelperName . '>';
        }
        return $fluidTemplateCode  . PHP_EOL;
    }

    /**
     * @param Form\FormInterface $object
     * @return array
     */
    protected function extractChildrenFromFormObject(Form\FormInterface $object) {
        if ($object instanceof Form) {
            $sheets = $object->getSheets();
            return (!empty($sheets) ? $sheets : $object->getFields());
        }
        if ($object instanceof Form\Container\Grid) {
            return $object->getRows();
        }
        if ($object instanceof Form\Container\Row) {
            return $object->getColumns();
        }
        if ($object instanceof Form\FieldInterface) {
            return ObjectAccess::getProperty($object, 'wizards', true);
        }
        if ($object instanceof Form\FieldContainerInterface) {
            return $object->getFields();
        }
        return [];
    }

    /**
     * @param mixed $value
     * @param integer $depth
     * @return mixed
     */
    protected function convertValueToFluidVariable($value, $depth = 0)
    {
        if ($depth && is_string($value)) {
            return sprintf("'%s'", str_replace('\'', '\\', $value));
        } elseif (is_bool($value)) {
            return (integer) $value;
        } elseif (is_scalar($value) || is_null($value)) {
            return $value;
        } elseif (is_array($value) && count($value)) {
            $fluidTemplateCode = '{';
            foreach ($value as $name => $subValue) {
                $converted = $this->convertValueToFluidVariable($subValue, $depth + 1);
                if ($converted === null) {
                    continue;
                }
                $fluidTemplateCode .= $name . ': ' . $converted . ', ';
            }
            $fluidTemplateCode = rtrim($fluidTemplateCode, ', ');
            $fluidTemplateCode .= '}';
            return $fluidTemplateCode;
        }
    }

}
