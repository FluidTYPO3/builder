<?php
namespace FluidTYPO3\Builder\Analysis;

use FluidTYPO3\Builder\Analysis\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;

class NoticeMessage extends AbstractMessage {

	/**
	 * @var integer
	 */
	protected $severity = FlashMessage::NOTICE;

	/**
	 * @var string
	 */
	protected $message = 'Metric value (%s) is above safe base level (notice: %s, warning: %s) value but not high enough to cause major concern.';

}