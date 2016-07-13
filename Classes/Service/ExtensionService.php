<?php
namespace FluidTYPO3\Builder\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Claus Due <claus@namelesscoder.net>
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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extensionmanager\Utility\ListUtility;
use FluidTYPO3\Builder\CodeGeneration\Extension\ExtensionGenerator;

/**
 * Service to compute Extension Manager api
 *
 * @author Cedric Ziel <cedric@cedric-ziel.com>, Cedric Ziel - Internetdienstleistungen & EDV
 * @package builder
 * @subpackage Service
 */
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
     * @param string $extensionKey Extension key, traditional format
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
        $backend = false,
        $useVhs = true,
        $useFluidcontentCore = true
    ) {
        $defaultTitle = 'Provider extension for ' .
            (true === $pages ? 'pages ' : '') .
            (true === $content ? 'content ' : '') .
            (true === $backend ? 'backend' : '');
        ;
        if (null === $title) {
            $title = $defaultTitle;
        }
        if (null === $description) {
            $description = $defaultTitle;
        }
        $dependencies = [];
        if (true === $pages) {
            array_push($dependencies, 'fluidpages');
        }
        if (true === $content) {
            array_push($dependencies, 'fluidcontent');
        }
        if (true === $backend) {
            array_push($dependencies, 'fluidbackend');
        }
        if (true === $useVhs) {
            array_push($dependencies, 'vhs');
        }
        if (true === $useFluidcontentCore) {
            array_push($dependencies, 'fluidcontent_core');
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
        $minimumVersion = 4.5;
        $extensionVariables = [
            'extensionKey' => $extensionKey,
            'title' => $title,
            'description' => $description,
            'date' => date('d-m-Y H:i'),
            'author' => $name,
            'email' => $email,
            'company' => $companyName,
            'coreMinor' => $minimumVersion,
            'controllers' => $controllers,
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
            if (true === array_key_exists($extensionKey, $GLOBALS['TYPO3_LOADED_EXT'])) {
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
