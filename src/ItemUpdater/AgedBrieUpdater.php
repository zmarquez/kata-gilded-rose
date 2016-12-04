<?php
namespace GildedRose\ItemUpdater;

use GildedRose\Item;

class AgedBrieUpdater extends ItemUpdater
{
    public function update(Item $item)
    {
        $this->increaseQuality($item);
        $this->decreaseSellin($item);
        if ($this->isExpired($item)) {
            $this->increaseQuality($item);
        }
    }
}
