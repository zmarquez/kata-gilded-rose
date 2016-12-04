<?php
namespace GildedRose\ItemUpdater;

use GildedRose\Item;

class BackstagePassUpdater extends ItemUpdater
{
    const LIMIT_SELLIN_TWICE_FAST_QUALITY = 10;
    const LIMIT_SELLIN_THRICE_FAST_QUALITY = 5;

    public function update(Item $item)
    {
        $this->increaseQuality($item);
        $this->increaseExtraQuality($item);
        $this->decreaseSellin($item);
        if ($this->isExpired($item)) {
            $this->resetQuality($item);
        }
    }

    /**
     * @param $item
     */
    private function increaseExtraQuality($item)
    {
        if ($item->quality >= self::MAXIMUM_QUALITY) {
            return;
        }
        if ($item->sellIn <= self::LIMIT_SELLIN_TWICE_FAST_QUALITY) {
            $this->increaseQuality($item);
        }
        if ($item->sellIn <= self::LIMIT_SELLIN_THRICE_FAST_QUALITY) {
            $this->increaseQuality($item);
        }
    }

    /**
     * @param $item
     */
    private function resetQuality($item)
    {
        $item->quality = self::MINIMUM_QUALITY;
    }
}
