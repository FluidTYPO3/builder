<?php
namespace FluidTYPO3\Builder\Command;

use FluidTYPO3\Builder\Service\CommandService;
use FluidTYPO3\Builder\Service\ExtensionService;
use FluidTYPO3\Builder\Service\SyntaxService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class BuilderCommandController extends CommandController
{

    /**
     * @var SyntaxService
     */
    protected $syntaxService;

    /**
     * @var ExtensionService
     */
    protected $extensionService;

    /**
     * @var CommandService
     */
    private $commandService;

    public function __construct(?CommandService $commandService = null)
    {
        $this->commandService = $commandService ?? GeneralUtility::makeInstance(ObjectManager::class)->get(CommandService::class);
    }

    /**
     * @param SyntaxService $syntaxService
     * @return void
     */
    public function injectSyntaxService(SyntaxService $syntaxService)
    {
        $this->syntaxService = $syntaxService;
    }

    /**
     * @param ExtensionService $extensionService
     * @return void
     */
    public function injectExtensionService(ExtensionService $extensionService)
    {
        $this->extensionService = $extensionService;
    }

    /**
     * Syntax check Fluid template
     *
     * Checks one template file, all templates in
     * an extension or a sub-path (which can be used
     * with an extension key for a relative path).
     * If left out, it will lint ALL templates in
     * EVERY local extension.
     *
     * @param string $extension Optional extension key (if path is set too it will apply to sub-folders in extension)
     * @param string $path file or folder path (if extensionKey is included, path is relative to this extension)
     * @param string $extensions If provided, this CSV list of file extensions are considered Fluid templates
     * @param boolean $verbose If TRUE outputs more information about each file check - default is to only output errors
     * @throws \RuntimeException
     * @return void
     */
    public function fluidSyntaxCommand($extension = null, $path = null, $extensions = 'html,xml,txt', $verbose = false)
    {
        $this->commandService->setResponse($this->response);
        $this->commandService->checkFluidSyntax($extension, $path, $extensions, $verbose);
    }

    /**
     * Syntax check PHP code
     *
     * Checks PHP source files in $path, if extension
     * key is also given, only files in that path relative
     * to that extension are checked.
     *
     * @param string $extension Optional extension key (if path is set too it will apply to sub-folders in extension)
     * @param string $path file or folder path (if extensionKey is included, path is relative to this extension)
     * @param boolean $verbose If TRUE outputs more information about each file check - default is to only output errors
     * @return void
     */
    public function phpsyntaxCommand($extension = null, $path = null, $verbose = false)
    {
        $this->commandService->setResponse($this->response);
        $this->commandService->checkPhpSyntax($extension, $path, $verbose);
    }

    /**
     * Lists installed Extensions. The output defaults to text and is new-line separated.
     *
     * @param boolean $detail If TRUE, the command will give detailed information such as version and state
     * @param boolean $active If TRUE, the command will give information about active extensions only
     * @param boolean $inactive If TRUE, the command will give information about inactive extensions only
     * @param boolean $json If TRUE, the command will return a json object-string
     * @throws \Exception
     * @return void
     */
    public function listCommand($detail = false, $active = null, $inactive = false, $json = false)
    {
        $detail = (boolean) $detail;
        $active = (boolean) $active;
        $inactive = (boolean) $inactive;
        $json = (boolean) $json;

        $this->commandService->setResponse($this->response);
        $this->commandService->listExtensions($detail, $active, $inactive, $json);
    }

    /**
     * Builds a ProviderExtension
     *
     * The resulting extension will contain source code
     * and configuration options needed by the various
     * toggles. Each of these toggles enable/disable
     * generation of source code and configuration for
     * that particular feature.
     *
     * @param string $extensionKey The extension identity which should be generated. Must not exist. Must be in the format "VendorName.ExtensionName".
     * @param string $author The author of the extension, in the format "Name Lastname <name@example.com>" with optional
     *                       company name, in which case form is "Name Lastname <name@example.com>, Company Name"
     * @param string $title Title of the resulting extension, by default "Provider extension for $enabledFeaturesList"
     * @param string $description Description for extension, by default "Provider extension for $enabledFeaturesList"
     * @param boolean $useVhs If TRUE, adds the VHS extension as dependency - recommended, on by default
     * @param boolean $pages If TRUE, generates basic files for implementing Fluid Page templates
     * @param boolean $content IF TRUE, generates basic files for implementing Fluid Content templates
     * @param boolean $controllers If TRUE, generates controllers for each enabled feature. Enabling $backend will
     *                             always generate a controller regardless of this toggle.
     * @param boolean $dry If TRUE performs a dry run without writing files;reports which files would have been written
     * @param boolean $verbose If FALSE, suppresses a lot of the otherwise output messages (to STDOUT)
     * @return void
     */
    public function providerExtensionCommand(
        $extensionKey,
        $author,
        $title = null,
        $description = null,
        $useVhs = true,
        $pages = true,
        $content = true,
        $controllers = true,
        $dry = false,
        $verbose = true
    ) {
        $useVhs = (boolean) $useVhs;
        $pages = (boolean) $pages;
        $content = (boolean) $content;
        $controllers = (boolean) $controllers;
        $verbose = (boolean) $verbose;
        $dry = (boolean) $dry;
        $this->commandService->setResponse($this->response);
        $this->commandService->generateProviderExtension(
            $extensionKey,
            $author,
            $title,
            $description,
            $controllers,
            $pages,
            $content,
            $useVhs,
            $dry,
            $verbose
        );
    }

    /**
     * Black hole
     *
     * @return void
     */
    protected function errorCommand()
    {
    }
}
