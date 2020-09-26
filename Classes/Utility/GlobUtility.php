<?php
namespace FluidTYPO3\Builder\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $files = array_filter($files, function(string $item) {
            return (bool) !(strpos($item, '/vendor/') || strpos($item, '/sysext/') || strpos($item, '/public/') || strpos($item, '/Tests/'));
        });
        $files = array_map('realpath', $files);
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
        } elseif ('/' !== $path[0]) {
            $path = PATH_site . $path;
        }
        $path = realpath($path);
        return $path;
    }
}
