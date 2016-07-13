<?php
namespace FluidTYPO3\Builder\Utility;

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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class ExtensionUtility
 * @package FluidTYPO3\Builder\Utility
 */
class ExtensionUtility
{

    /**
     * @var array
     */
    private static $cache = null;

    /**
     * @param boolean $includeCoreExtensions
     * @return array
     */
    public static function getAllInstalledFluidEnabledExtensions($includeCoreExtensions = false)
    {
        if (true === is_array(self::$cache)) {
            return self::$cache;
        }
        $allExtensions = ExtensionManagementUtility::getLoadedExtensionListArray();
        $fluidExtensions = [];
        foreach ($allExtensions as $extensionKey) {
            $fluidTemplateFolderPath = ExtensionManagementUtility::extPath(
                $extensionKey,
                'Resources/Private/Templates'
            );
            $isCore = strpos($fluidTemplateFolderPath, 'sysext');
            $hasTemplates = file_exists($fluidTemplateFolderPath);
            if (true === $hasTemplates) {
                if (false === $isCore || (true === $includeCoreExtensions && true === $isCore)) {
                    array_push($fluidExtensions, $extensionKey);
                }
            }
        }
        self::$cache = $fluidExtensions;
        return self::$cache;
    }

    /**
     * @param boolean $includeCoreExtensions
     * @return array
     */
    public static function getAllInstalledFluidEnabledExtensionsAsSelectorOptions($includeCoreExtensions = false)
    {
        $extensions = self::getAllInstalledFluidEnabledExtensions($includeCoreExtensions);
        return array_combine($extensions, $extensions);
    }
}
