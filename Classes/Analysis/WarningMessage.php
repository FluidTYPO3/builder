<?php
namespace FluidTYPO3\Builder\Analysis;

use FluidTYPO3\Builder\Analysis\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;

class WarningMessage extends AbstractMessage {

	/**
	 * @var integer
	 */
	protected $severity = FlashMessage::WARNING;

	/**
	 * @var string
	 */
	protected $message = 'Metric value (%s) is above tolerance level (notice: %s, warning: %s) - you should take measures to lower this metric.';

}