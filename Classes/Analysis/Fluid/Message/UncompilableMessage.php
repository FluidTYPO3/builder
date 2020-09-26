<?php
namespace FluidTYPO3\Builder\Analysis\Fluid\Message;

use FluidTYPO3\Builder\Analysis\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;

class UncompilableMessage extends AbstractMessage
{

    /**
     * @var integer
     */
    protected $severity = FlashMessage::WARNING;

    /**
     * @var string
     */
    protected $message = 'Template contains uncompilable ViewHelpers - 
        avoiding these is very likely to increase performance';
}
