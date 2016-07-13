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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class GlobUtility
 * @package FluidTYPO3\Builder\Utility
 */
class GlobUtility
{

    /**
     * Glob() a path inside an extension
     *
     * @param string $extensionKey
     * @param string $path
     * @return array
     */
    public static function globExtensionAndPath($extensionKey, $path)
    {
        $realPath = ExtensionManagementUtility::extPath($extensionKey, $path);
        return self::globPath($realPath);
    }

    /**
     * @param string $path
     * @return array
     */
    public static function globPath($path)
    {
        return glob($path);
    }

    /**
     * @param string $path
     * @param string $extensions
     * @return array
     */
    public static function getFilesRecursive($path, $extensions)
    {
        if ('/' !== substr($path, -1)) {
            $path .= '/';
        }
        $files = GeneralUtility::getAllFilesAndFoldersInPath([], $path, $extensions);
        return array_values($files);
    }

    /**
     * @param string $extension
     * @param string $path
     * @return string
     */
    public static function getRealPathFromExtensionKeyAndPath($extension, $path)
    {
        if (null !== $extension) {
            $path = ExtensionManagementUtility::extPath($extension, $path);
        } elseif ('/' !== $path{0}) {
            $path = PATH_site . $path;
        }
        $path = realpath($path);
        return $path;
    }
}
