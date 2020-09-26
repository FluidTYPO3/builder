<?php
namespace FluidTYPO3\Builder\CodeGeneration;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

abstract class AbstractCodeGenerator implements CodeGeneratorInterface
{

    /**
     * @var boolean
     */
    protected $verbose = true;

    /**
     * @var boolean
     */
    protected $dry = false;

    /**
     * @var ObjectManagerInterface
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
     * @param boolean $dry
     * @return void
     */
    public function setDry($dry)
    {
        $this->dry = $dry;
    }

    /**
     * @param boolean $verbose
     * @return void
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }

    /**
     * @param string $folderPath
     * @return boolean
     * @throws \Exception
     */
    public function createFolder($folderPath)
    {
        if (true === $this->dry) {
            return true;
        }
        try {
            GeneralUtility::mkdir_deep($folderPath);
        } catch (\InvalidArgumentException $exception) {
            throw new \Exception('Unable to create directory "' . $folderPath . '"', 1371692697);
        }
        return true;
    }

    /**
     * @param string $filePathAndFilename
     * @param string $content
     * @return boolean
     * @throws \Exception
     */
    public function createFile($filePathAndFilename, $content)
    {
        if (true === $this->dry) {
            return true;
        }
        $folderPath = pathinfo($filePathAndFilename, PATHINFO_DIRNAME);
        if (false === is_dir($folderPath)) {
            $this->createFolder($folderPath);
        }
        $createdFile = GeneralUtility::writeFile($filePathAndFilename, $content);
        if (false === $createdFile) {
            throw new \Exception('Unable to create file "' . $filePathAndFilename . '"', 1371695066);
        }
        return true;
    }

    /**
     * @param string $localRelativePathAndFilename
     * @param string $destinationPathAndFilename
     * @return boolean
     * @throws \Exception
     */
    public function copyFile($localRelativePathAndFilename, $destinationPathAndFilename)
    {
        if (true === $this->dry) {
            return true;
        }
        $folderPath = pathinfo($destinationPathAndFilename, PATHINFO_DIRNAME);
        if (false === is_dir($folderPath)) {
            $this->createFolder($folderPath);
        }
        $localFile = $this->getBuilderExtensionPath() . $localRelativePathAndFilename;
        $fileCopied = copy($localFile, $destinationPathAndFilename);
        if (false === $fileCopied) {
            throw new \Exception(
                'Unable to copy file "' . $localFile . '" to "' . $destinationPathAndFilename . '"',
                1371695897
            );
        }
        return true;
    }

    /**
     * @return string
     */
    protected function getBuilderExtensionPath()
    {
        return rtrim(ExtensionManagementUtility::extPath('builder'), '/') . '/';
    }

    /**
     * @param string $filePathAndFilename
     * @return void
     */
    public function save($filePathAndFilename)
    {
        $code = $this->generate();
        $this->createFile($filePathAndFilename, $code);
    }

    /**
     * @param string $identifier
     * @param array $variables
     * @return CodeTemplate
     */
    protected function getPreparedCodeTemplate($identifier, $variables)
    {
        /** @var CodeTemplate $template */
        $template = $this->objectManager->get('FluidTYPO3\Builder\CodeGeneration\CodeTemplate');
        $template->setPath(ExtensionManagementUtility::extPath('builder', 'Resources/Private/CodeTemplates/'));
        $template->setIdentifier($identifier);
        $template->setVariables($variables);
        return $template;
    }
}
