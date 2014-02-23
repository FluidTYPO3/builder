<?php
namespace FluidTYPO3\Builder\Analysis;

use FluidTYPO3\Builder\Analysis\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;

class OkMessage extends AbstractMessage {

	/**
	 * @var integer
	 */
	protected $severity = FlashMessage::OK;

	/**
	 * @var string
	 */
	protected $message = 'Metric value (%s) is within tolerance levels (notice: %s, warning: %s) but there may be other, more specific messages.';

}