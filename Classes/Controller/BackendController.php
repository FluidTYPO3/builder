<?php
namespace FluidTYPO3\Builder\Controller;

use FluidTYPO3\Builder\Analysis\Fluid\TemplateAnalyzer;
use FluidTYPO3\Builder\Analysis\Metric;
use FluidTYPO3\Builder\Result\ParserResult;
use FluidTYPO3\Builder\Service\ExtensionService;
use FluidTYPO3\Builder\Service\SyntaxService;
use FluidTYPO3\Builder\Utility\ExtensionUtility;
use FluidTYPO3\Builder\Utility\GlobUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Backend\Template\DocumentTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility;

class BackendController extends ActionController
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
     * @param string $view
     * @return void
     */
    public function indexAction($view = 'Index')
    {
        $extensions = ExtensionUtility::getAllInstalledFluidEnabledExtensions();
        $selectorOptions = ExtensionUtility::getAllInstalledFluidEnabledExtensionsAsSelectorOptions();
        $formats = [
            'html' => true,
            'xml' => false,
            'txt' => false,
            'eml' => false,
            'yaml' => false,
            'css' => false,
            'js' => false,
        ];
        $this->view->assign('csh', BackendUtility::wrapInHelp('builder', 'modules'));
        $this->view->assign('view', $view);
        $this->view->assign('extensions', $extensions);
        $this->view->assign('extensionSelectorOptions', $selectorOptions);
        $this->view->assign('formats', $formats);
    }

    /**
     * @param string $view
     * @return void
     */
    public function buildFormAction($view = 'BuildForm')
    {
        $author = '';
        if (!empty($GLOBALS['BE_USER']->user['realName']) && !empty($GLOBALS['BE_USER']->user['email'])) {
            $author = $GLOBALS['BE_USER']->user['realName'] . ' <' . $GLOBALS['BE_USER']->user['email'] . '>';
        }
        $this->view->assign('csh', BackendUtility::wrapInHelp('builder', 'modules'));
        $this->view->assign('view', $view);
        $this->view->assign('author', $author);
        $isFluidcontentCoreInstalled = ExtensionManagementUtility::isLoaded('fluidcontent_core') ? "'checked'" : null;
        $this->view->assign('isFluidcontentCoreInstalled', $isFluidcontentCoreInstalled);
    }

    /**
     * @param string $name
     * @param string $author
     * @param string $title
     * @param string $description
     * @param boolean $controllers
     * @param boolean $pages
     * @param boolean $content
     * @param boolean $vhs
     * @param boolean $dry
     * @param boolean $verbose
     * @return void
     */
    public function buildAction(
        $name,
        $author,
        $title,
        $description,
        $controllers,
        $pages,
        $content,
        $vhs,
        $dry,
        $verbose
    ) {
        $generator = $this->extensionService->buildProviderExtensionGenerator(
            $name,
            $author,
            $title,
            $description,
            $controllers,
            $pages,
            $content,
            $vhs
        );
        $generator->setVerbose($verbose);
        $generator->setDry($dry);
        if (false === $dry) {
            $generator->generate();
        }
        $this->view->assign('boolean', true);
        $this->view->assign('view', 'BuildForm');
        $this->view->assign('attributes', $this->arguments->getArrayCopy());
    }

    /**
     * @param array $syntax
     * @param array $extensions
     * @param array $formats
     * @param array $filteredFiles
     */
    public function syntaxAction(array $syntax, array $extensions, array $formats, array $filteredFiles = [])
    {
        if (class_exists(Environment::class)) {
            $resourcePath = substr(ExtensionManagementUtility::extPath('builder', 'Resources/Public/'), strlen(Environment::getPublicPath()));
        } else {
            /** @var DocumentTemplate $document */
            $document = &$GLOBALS['TBE_TEMPLATE'];
            $resourcePath = $document->backPath . ExtensionManagementUtility::extRelPath('builder') . 'Resources/Public/';
        }
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.canvasTextRenderer.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.canvasAxisTickRenderer.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.cursor.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.categoryAxisRenderer.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.barRenderer.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/jqplot.pointLabels.min.js');
        $pageRenderer->addJsFile($resourcePath . 'Javascript/plotter.js');
        $pageRenderer->addCssFile($resourcePath . 'Stylesheet/analysis.css');
        $pageRenderer->addCssFile($resourcePath . 'Stylesheet/jqplot.min.css');
        $pageRenderer->addCssFile($resourcePath . 'Stylesheet/plotter.css');
        $reports = [];
        $csvFormats = trim(implode(',', $formats), ',');
        foreach ($extensions as $extensionKey) {
            if (true === empty($extensionKey)) {
                continue;
            }
            $extensionFolder = realpath(ExtensionManagementUtility::extPath($extensionKey));
            $reports[$extensionKey] = [];
            foreach ($syntax as $syntaxName) {
                if (true === empty($syntaxName)) {
                    continue;
                }
                $reportsForSyntaxName = [];
                if ('php' === $syntaxName) {
                    $reportsForSyntaxName = $this->syntaxService->syntaxCheckPhpFilesInPath(
                        $extensionFolder . '/Classes'
                    );
                } elseif ('profile' === $syntaxName) {
                    $files = GlobUtility::getFilesRecursive($extensionFolder, $csvFormats);
                    if (0 === count($filteredFiles)) {
                        $filteredFiles = $files;
                    }
                    $this->view->assign('files', $files);
                    $this->view->assign('basePathLength', strlen($extensionFolder));
                    foreach ($files as $file) {
                        if (0 < count($filteredFiles) && false === in_array($file, $filteredFiles)) {
                            continue;
                        }
                        /** @var TemplateAnalyzer $templateAnalyzer */
                        $templateAnalyzer = $this->objectManager->get(TemplateAnalyzer::class);
                        $shortFilename = substr($file, strpos($file, '/' . $extensionKey . '/') + strlen($extensionKey) + 2);
                        $reportsForSyntaxName[$shortFilename] = $templateAnalyzer->analyzePathAndFilename($file);
                    }
                    $reports[$extensionKey][$syntaxName]['json'] = $this->encodeMetricsToJson($reportsForSyntaxName);
                }
                $reports[$extensionKey][$syntaxName]['reports'] = $reportsForSyntaxName;
                $reports[$extensionKey][$syntaxName]['errors'] = $this->syntaxService->countErrorsInResultCollection(
                    $reportsForSyntaxName
                );
            }
        }
        $this->view->assign('filteredFiles', $filteredFiles);
        $this->view->assign('reports', $reports);
        $this->view->assign('extensions', $extensions);
        $this->view->assign('formats', $formats);
        $this->view->assign('syntax', $syntax);
        $this->view->assign('view', 'Index');
    }

    /**
     * @param ParserResult[] $metrics
     * @return string
     */
    protected function encodeMetricsToJson($metrics)
    {
        foreach ($metrics as $index => $metric) {
            $values = $metric->getPayload();
            $metrics[$index] = [];
            /** @var Metric $value */
            foreach ($values as $value) {
                $metrics[$index][$value->getName()] = $value->getValue();
            }
        }
        return json_encode($metrics);
    }
}
