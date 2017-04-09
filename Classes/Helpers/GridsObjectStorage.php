<?php
namespace FluidTYPO3\Builder\Helpers;

/**
 * Class GridsObjectStorage
 */
class GridsObjectStorage extends \SplObjectStorage
{
    /**
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this);
    }
}
