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

	/**
	 * @param string $path
	 * @param string $extensions
	 * @return array
	 */
	public static function getFilesRecursive($path, $extensions) {
		if ('/' !== substr($path, -1)) {
			$path .= '/';
		}
		$files = t3lib_div::getAllFilesAndFoldersInPath(array(), $path, $extensions);
		return array_values($files);
	}

	/**
	 * @param string $extension
	 * @param string $path
	 * @return string
	 */
	public static function getRealPathFromExtensionKeyAndPath($extension, $path) {
		if (NULL !== $extension) {
			$path = t3lib_extMgm::extPath($extension, $path);
		} elseif ('/' !== $path{0}) {
			$path = PATH_site . $path;
		}
		$path = realpath($path);
		return $path;
	}

}
