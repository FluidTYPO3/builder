<?php
namespace FluidTYPO3\Builder\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extensionmanager\Utility\ListUtility;
use FluidTYPO3\Builder\CodeGeneration\Extension\ExtensionGenerator;

class ExtensionService implements SingletonInterface
{

    const STATE_INACTIVE = 0;
    const STATE_ACTIVE = 1;
    const STATE_ALL = 2;

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
     * @param string $extensionKey Extension key, VendorName.ExtensionName format.
     * @param string $author Author, format "Name Lastname <email@domain.com>"
     * @param string $title Title of extension (NULL for auto title)
     * @param string $description
     * @param boolean $controllers Generate controllers for each FluidTYPO3 feature
     * @param boolean $pages Include use of fluidpages
     * @param boolean $content Include use of fluidcontent
     * @param boolean $backend Include use of fluidbackend
     * @param boolean $useVhs Include VHS as dependency
     * @param boolean $useFluidcontentCore Include FluidcontentCore as dependency
     * @return ExtensionGenerator
     */
    public function buildProviderExtensionGenerator(
        $extensionKey,
        $author,
        $title = null,
        $description = null,
        $controllers = false,
        $pages = true,
        $content = true,
        $useVhs = true
    ) {
        if (!preg_match('/[a-zA-Z]+\\.[a-zA-Z]/', $extensionKey)) {
            throw new \InvalidArgumentException('Extension identity must be in VendorName.ExtensionName format', 1601125275);
        }
        $defaultTitle = 'Provider extension for ' .
            (true === $pages ? 'pages ' : '') .
            (true === $content ? 'content ' : '');
        ;
        if (null === $title) {
            $title = $defaultTitle;
        }
        if (null === $description) {
            $description = $defaultTitle;
        }
        $dependencies = [];
        if (true === $useVhs) {
            array_push($dependencies, 'vhs');
        }
        $dependenciesArrayString = '';
        foreach ($dependencies as $dependency) {
            $dependenciesArrayString .= "\n\t\t\t'" . $dependency . "' => '',";
        }
        list ($nameAndEmail, $companyName) = GeneralUtility::trimExplode(',', $author);
        list ($name, $email) = GeneralUtility::trimExplode('<', $nameAndEmail);
        $email = trim($email, '>');
        $name = addslashes($name);
        $companyName = addslashes($companyName);
        $description = addslashes($description);
        $title = addslashes($title);
        $baseVersion = substr(TYPO3_version, 0, strrpos(TYPO3_version, '.'));
        $minimumVersion = $baseVersion . '.0';
        $maximumVersion = $baseVersion . '.99';
        $extensionVariables = [
            'extensionName' => $extensionKey,
            'extensionNamespace' => str_replace('.', '\\', $extensionKey) . '\\',
            'extensionKey' => GeneralUtility::camelCaseToLowerCaseUnderscored(substr($extensionKey, strpos($extensionKey, '.') + 1)),
            'title' => $title,
            'description' => $description,
            'date' => date('d-m-Y H:i'),
            'author' => $name,
            'email' => $email,
            'company' => $companyName,
            'coreMinor' => $minimumVersion,
            'coreMajor' => $maximumVersion,
            'controllers' => $controllers,
            'content' => $content,
            'pages' => $pages,
            'dependencies' => $dependencies,
            'dependenciesCsv' => 0 === count($dependencies) ? '' : ',' . implode(',', $dependencies),
            'dependenciesArray' => $dependenciesArrayString
        ];
        /** @var ExtensionGenerator $extensionGenerator */
        $extensionGenerator = $this->objectManager->get(ExtensionGenerator::class);
        $extensionGenerator->setConfiguration($extensionVariables);
        return $extensionGenerator;
    }

    /**
     * Prints information according to the instances properties
     *
     * @param string $format Desired format. Currently 'text' or 'json'
     * @param boolean $detail Detailed Output? Mandatory if $format == 'json'
     * @param integer $state Extension state
     *
     * @return string
     */
    public function getPrintableInformation($format = 'text', $detail = false, $state = self::STATE_ALL)
    {
        $extensionInformation = $this->gatherInformation();

        if ('text' === $format) {
            $printedInfo = $this->printAsText($extensionInformation, $detail, $state);
            return $printedInfo;
        } elseif ('json' === $format) {
            $printedJsonInfo = $this->printAsJson($extensionInformation);
            return $printedJsonInfo;
        } else {
            return '';
        }
    }

    /**
     * Returns an array of extension information. Which is the same as the --detail switch.
     *
     * A facade is used as more behaviour might be hidden.
     *
     * @return array
     */
    public function getComputableInformation()
    {
        $extensionInformation = $this->gatherInformation();

        return $extensionInformation;
    }

    /**
     * Gathers Extension Information
     *
     * @return array
     */
    private function gatherInformation()
    {
        /** @var ListUtility $service */
        $service = $this->objectManager->get('TYPO3\CMS\Extensionmanager\Utility\ListUtility');

        $extensionInformation = $service->getAvailableExtensions();
        foreach ($extensionInformation as $extensionKey => $info) {
            $extensionInformation[$extensionKey]['version'] = ExtensionManagementUtility::getExtensionVersion(
                $extensionKey
            );
            if (ExtensionManagementUtility::isLoaded($extensionKey)) {
                $extensionInformation[$extensionKey]['installed'] = 1;
            } else {
                $extensionInformation[$extensionKey]['installed'] = 0;
            }
        }
        return $extensionInformation;
    }

    /**
     * Prints text-output
     *
     * @param array $extensionInformation
     * @param boolean $detail
     * @param int $state
     *
     * @return string
     */
    private function printAsText($extensionInformation, $detail = false, $state = self::STATE_ALL)
    {
        $output = '';
        foreach ($extensionInformation as $extension => $info) {
            if (false === $detail) {
                switch ($state) {
                    case self::STATE_INACTIVE:
                        if (0 === intval($info['installed'])) {
                            $output .= $extension . LF;
                        }
                        break;
                    case self::STATE_ACTIVE:
                        if (1 === intval($info['installed'])) {
                            $output .= $extension . LF;
                        }
                        break;
                    default:
                        $output .= $extension . LF;
                        break;
                }
            } elseif (true === $detail) {
                switch ($state) {
                    case self::STATE_INACTIVE:
                        if (0 === intval($info['installed'])) {
                            $output .= $this->concatStringOutput($extension, $info);
                        }
                        break;
                    case self::STATE_ACTIVE:
                        if (1 === intval($info['installed'])) {
                            $output .= $this->concatStringOutput($extension, $info);
                        }
                        break;
                    default:
                        $output .= $this->concatStringOutput($extension, $info);
                        break;
                }
            }
        }
        return $output;
    }

    /**
     * Concats the detailed output to yaml
     *
     * @param string $extension Extension Name
     * @param array $info Extension Information
     * @return string
     */
    private function concatStringOutput($extension, $info)
    {
        return $extension . ':' . LF .
                '  version: ' . $info['version'] . LF .
                '  installed: ' . $info['installed'] . LF .
                '  type: ' . $info['type'] . LF .
                '  path: ' . $info['siteRelPath'] .
                LF;
    }

    /**
     * @param array $extensionInformation
     *
     * @return string
     */
    public function printAsJson($extensionInformation)
    {
        $json = json_encode($extensionInformation) . LF;

        return $json;
    }
}
