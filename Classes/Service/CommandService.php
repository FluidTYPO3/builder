<?php
namespace FluidTYPO3\Builder\Service;

use FluidTYPO3\Builder\Utility\GlobUtility;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Response;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class CommandService implements SingletonInterface
{
    /**
     * @var Response
     */
    private $response;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var SyntaxService
     */
    private $syntaxService;

    /**
     * @var ExtensionService
     */
    private $extensionService;

    public function __construct(?SyntaxService $syntaxService = null, ?ExtensionService $extensionService = null)
    {
        $this->syntaxService = $syntaxService ?? GeneralUtility::makeInstance(ObjectManager::class)->get(SyntaxService::class);
        $this->extensionService = $extensionService ?? GeneralUtility::makeInstance(ObjectManager::class)->get(ExtensionService::class);
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * Lists installed Extensions. The output defaults to text and is new-line separated.
     *
     * @param boolean $detail If TRUE, the command will give detailed information such as version and state
     * @param boolean $active If TRUE, the command will give information about active extensions only
     * @param boolean $inactive If TRUE, the command will give information about inactive extensions only
     * @param boolean $json If TRUE, the command will return a json object-string
     * @throws \Exception
     * @return int
     */
    public function listExtensions(bool $detail = false, bool $active = true, bool $inactive = false, bool $json = false): int
    {
        $format = 'text';
        if (true === $json) {
            $format = 'json';
        }
        if ($active && $inactive) {
            $state = ExtensionService::STATE_ALL;
        } elseif ($active && !$inactive) {
            $state = ExtensionService::STATE_ACTIVE;
        } elseif ($inactive && !$active) {
            $state = ExtensionService::STATE_INACTIVE;
        } else {
            $state = ExtensionService::STATE_ALL;
        }

        $this->setContent(
            $this->extensionService->getPrintableInformation($format, $detail, $state)
        );

        return 0;
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
     * @return int
     */
    public function checkFluidSyntax(?String $extension = null, ?string $path = null, string $extensions = 'html,xml,txt', bool $verbose = false): int
    {
        if (null !== $extension) {
            $this->assertEitherExtensionKeyOrPathOrBothAreProvidedOrExit($extension, $path);
            $path = GlobUtility::getRealPathFromExtensionKeyAndPath($extension, $path);
            $files = GlobUtility::getFilesRecursive($path, $extensions);
        } else {
            // no extension key given, let's lint it all
            $files = [];
            /** @var ExtensionService $extensionService */
            $extensionInformation = $this->extensionService->getComputableInformation();
            foreach ($extensionInformation as $extensionName => $extensionInfo) {
                // Syntax service declines linting of inactive extensions
                if (0 === intval($extensionInfo['installed']) || 'System' === $extensionInfo['type']) {
                    continue;
                }
                $path = GlobUtility::getRealPathFromExtensionKeyAndPath($extensionName, null);
                $files = array_merge($files, GlobUtility::getFilesRecursive($path, $extensions));
            }
        }
        $files = array_values($files);
        $errors = false;
        $this->setContent(
            'Performing a syntax check on fluid templates (types: ' . $extensions . '; path: ' . $path . ')' . LF
        );
        $this->send();
        foreach ($files as $filePathAndFilename) {
            $basePath = str_replace(Environment::getProjectPath(), '', $filePathAndFilename);
            $result = $this->syntaxService->syntaxCheckFluidTemplateFile($filePathAndFilename);
            if (null !== $result->getError()) {
                $this->appendContent('[ERROR] File ' . $basePath . ' has an error: ' . LF);
                $this->appendContent(
                    $result->getError()->getMessage() . ' (' . $result->getError()->getCode() . ')' . LF
                );
                $this->send();
                $errors = true;
            } elseif (true === $verbose) {
                $namespaces = $result->getNamespaces();
                $this->appendContent(
                    'File is compilable: ' . ($result->getCompilable() ? 'YES' : 'NO (WARNING)') . LF
                );
                if ($result->getLayoutName()) {
                    $this->appendContent('File has layout (' . $result->getLayoutName() . ')' . LF);
                } else {
                    $this->appendContent('File DOES NOT reference a Layout' . LF);
                }
                $this->appendContent(
                    'File has ' . count($namespaces) . ' namespace(s)' .
                    (0 < count($namespaces) ? ': ' . $result->getNamespacesFlattened() : '') . LF
                );
            }
            if (null === $result->getError()) {
                $this->appendContent('[OK] File  ' . $basePath . ' is valid.' . LF);
                $this->send();
            }
        }
        return $this->stop($files, $errors, $verbose);
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
     * @return int
     */
    public function checkPhpSyntax(?string $extension = null, ?string $path = null, bool $verbose = false): int
    {
        $verbose = (boolean) $verbose;
        $this->assertEitherExtensionKeyOrPathOrBothAreProvidedOrExit($extension, $path);
        if (null !== $extension) {
            $results = $this->syntaxService->syntaxCheckPhpFilesInExtension($extension);
        } else {
            $results = $this->syntaxService->syntaxCheckPhpFilesInPath($path);
        }
        $errors = false;
        foreach ($results as $filePathAndFilename => $result) {
            $result = $this->syntaxService->syntaxCheckPhpFile($filePathAndFilename);
            if (null !== $result->getError()) {
                $errors = true;
                $this->setContent(
                    '[ERROR] ' . $result->getError()->getMessage() . ' (' . $result->getError()->getCode() . ')' . LF
                );
            }
        }
        return $this->stop($results, $errors, $verbose);
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
     * @param string $extensionKey The extension key which should be generated. Must not exist.
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
     * @return int
     */
    public function generateProviderExtension(
        string $extensionKey,
        string $author,
        ?string $title = null,
        ?string $description = null,
        bool $useVhs = true,
        bool $pages = true,
        bool $content = true,
        bool $controllers = true,
        bool $dry = false,
        bool $verbose = true
    ):int {
        $extensionGenerator = $this->extensionService->buildProviderExtensionGenerator(
            $extensionKey,
            $author,
            $title,
            $description,
            $controllers,
            $pages,
            $content,
            $useVhs
        );
        $extensionGenerator->setDry($dry);
        $extensionGenerator->setVerbose($verbose);
        $this->setContent($extensionGenerator->generate() . PHP_EOL);
        return 0;
    }

    private function assertEitherExtensionKeyOrPathOrBothAreProvidedOrExit(?string $extension, ?string $path): void
    {
        if (null === $extension && null === $path) {
            $this->setContent('Either "extension" or "path" or both must be specified' . LF);
            $this->send();
            $this->setExitCode(128);
        }
    }

    private function send(): void
    {
        if ($this->response instanceof ResponseInterface) {
            $this->response->send();
        }
    }

    private function setContent(string $content): void
    {
        if ($this->output instanceof OutputInterface) {
            $this->output->write($content);
        } elseif ($this->response instanceof ResponseInterface) {
            $this->response->setContent($content);
        }
    }

    private function appendContent(string $content): void
    {
        if ($this->output instanceof OutputInterface) {
            $this->output->write($content);
        } elseif ($this->response instanceof ResponseInterface) {
            $this->response->appendContent($content);
        }
    }

    private function setExitCode(int $code): void
    {
        if ($this->response instanceof ResponseInterface) {
            $this->response->setExitCode($code);
        }
    }

    private function stop(array $files, bool $errors, bool $verbose): int
    {
        $code = (int) $errors;
        if (true === (boolean) $verbose) {
            if (false === $errors) {
                $this->setContent('No errors encountered - ' . count($files) . ' file(s) are all okay' . LF);
            } else {
                $this->setContent('Errors were detected - review the summary above' . LF);
                $this->setExitCode($code);
            }
        }
        $this->send();
        return $code;
    }
}
