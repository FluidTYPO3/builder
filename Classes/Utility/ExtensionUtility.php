<?php

class Tx_Builder_Utility_ExtensionUtility {

	/**
	 * @var array
	 */
	private static $cache = NULL;

	/**
	 * @param boolean $includeCoreExtensions
	 * @return array
	 */
	public static function getAllInstalledFluidEnabledExtensions($includeCoreExtensions = FALSE) {
		if (TRUE === is_array(self::$cache)) {
			return self::$cache;
		}
		$allExtensions = t3lib_extMgm::getLoadedExtensionListArray();
		$fluidExtensions = array();
		foreach ($allExtensions as $extensionKey) {
			$fluidTemplateFolderPath = t3lib_extMgm::extPath($extensionKey, 'Resources/Private/Templates');
			$isCore = strpos($fluidTemplateFolderPath, 'sysext');
			$hasTemplates = file_exists($fluidTemplateFolderPath);
			if (TRUE === $hasTemplates) {
				if (FALSE === $isCore || (TRUE === $includeCoreExtensions && TRUE === $isCore)) {
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
	public static function getAllInstalledFluidEnabledExtensionsAsSelectorOptions($includeCoreExtensions = FALSE) {
		$extensions = self::getAllInstalledFluidEnabledExtensions($includeCoreExtensions);
		return array_combine($extensions, $extensions);
	}

}
