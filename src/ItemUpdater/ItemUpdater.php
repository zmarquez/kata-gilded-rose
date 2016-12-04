<?php
namespace GildedRose\ItemUpdater;

use GildedRose\Item;

abstract class ItemUpdater
{
    const MINIMUM_QUALITY = 0;
    const QUALITY_GRANULARITY = 1;
    const MINIMUM_SELLIN = 0;
    const MAXIMUM_QUALITY = 50;
    const SELLIN_GRANULARITY = 1;

    abstract public function update(Item $item);

    /**
     * @param Item $item
     */
    protected function increaseQuality(Item $item)
    {
        if ($item->quality >= self::MAXIMUM_QUALITY) {
            return;
        }
        $item->quality = $item->quality + self::QUALITY_GRANULARITY;

    }

    /**
     * @param $item
     */
    protected function decreaseSellin($item)
    {
        $item->sellIn = $item->sellIn - self::SELLIN_GRANULARITY;
    }

    /**
     * @param $item
     *
     * @return bool
     */
    protected function isExpired($item)
    {
        return $item->sellIn < self::MINIMUM_SELLIN;
    }
}