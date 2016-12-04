<?php
namespace GildedRose\ItemUpdater;

use GildedRose\Item;

class DefaultItemUpdater extends ItemUpdater
{
    public function update(Item $item)
    {
        $this->decreaseQuality($item);
        $this->decreaseSellin($item);
        if ($this->isExpired($item)) {
            $this->decreaseQuality($item);
        }
    }

    /**
     * @param Item $item
     */
    private function decreaseQuality(Item $item)
    {
        if ($item->quality <= self::MINIMUM_QUALITY) {
            return;
        }
        $item->quality = $item->quality - self::QUALITY_GRANULARITY;
    }
}
