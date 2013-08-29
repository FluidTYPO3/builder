<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Cedric Ziel <cedric@cedric-ziel.com>, Internetdienstleistungen & EDV
 *
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

/**
 * Service to compute Extension Manager api
 *
 * @author Cedric Ziel <cedric@cedric-ziel.com>, Cedric Ziel - Internetdienstleistungen & EDV
 * @package builder
 * @subpackage Service
 */
class Tx_Builder_Service_ExtensionService implements t3lib_Singleton {

	const STATE_INACTIVE = 0;
	const STATE_ACTIVE = 1;
	const STATE_ALL = 2;

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
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
	public function getPrintableInformation($format = 'text', $detail = FALSE, $state = self::STATE_ALL) {
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
	public function getComputableInformation() {
		$extensionInformation = $this->gatherInformation();

		return $extensionInformation;
	}

	/**
	 * Gathers Extension Information
	 *
	 * @return array
	 */
	private function gatherInformation() {
		/** @var \TYPO3\CMS\Extensionmanager\Utility\ListUtility $service */
		$service = $this->objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\ListUtility');

		$extensionInformation = $service->getAvailableExtensions();
		foreach ($extensionInformation as $extensionKey => $info) {
			$extensionInformation[$extensionKey]['version'] = t3lib_extMgm::getExtensionVersion($extensionKey);
			if (TRUE === array_key_exists($extensionKey, $GLOBALS['TYPO3_LOADED_EXT'])) {
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
	private function printAsText($extensionInformation, $detail = FALSE, $state = self::STATE_ALL) {
		$output = '';
		foreach ($extensionInformation as $extension => $info) {
			if (FALSE === $detail) {
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
			} elseif (TRUE === $detail) {
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
	private function concatStringOutput($extension, $info) {
		return $output = $extension . ':' . LF .
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
	public function printAsJson($extensionInformation) {
		$json = json_encode($extensionInformation) . LF;

		return $json;
	}

}