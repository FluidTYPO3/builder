<?php
namespace FluidTYPO3\Builder\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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
