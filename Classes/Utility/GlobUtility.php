<?php

class Tx_Builder_Utility_GlobUtility {

	/**
	 * Glob() a path inside an extension
	 *
	 * @param string $extensionKey
	 * @param string $path
	 * @return array
	 */
	public static function globExtensionAndPath($extensionKey, $path) {
		$realPath = t3lib_extMgm::extPath($extensionKey, $path);
		return self::globPath($realPath);
	}

	/**
	 * @param string $path
	 * @return array
	 */
	public static function globPath($path) {
		return glob($path);
	}

}
