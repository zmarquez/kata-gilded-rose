<?php
namespace GildedRose;

use GildedRose\ItemUpdater\AgedBrieUpdater;
use GildedRose\ItemUpdater\BackstagePassUpdater;
use GildedRose\ItemUpdater\DefaultItemUpdater;
use GildedRose\ItemUpdater\ItemUpdater;
use GildedRose\ItemUpdater\SulfurasUpdater;

class ItemUpdaterFactory
{
    /**
     * @param $itemType
     *
     * @return ItemUpdater
     */
    public static function createInstance($itemType)
    {
        $updater = new DefaultItemUpdater();
        switch ($itemType) {
            case ItemType::AGED_BRIE:
                $updater = new AgedBrieUpdater();
                break;
            case ItemType::BACKSTAGE_PASSES:
                $updater = new BackstagePassUpdater();
                break;
            case ItemType::SULFURAS:
                $updater = new SulfurasUpdater();
                break;
        }

        return $updater;
    }
}
