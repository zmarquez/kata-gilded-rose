<?php
namespace GildedRose;

class Inn
{
    public function updateQuality($items)
    {
        foreach ($items as $item) {
            $updater = ItemUpdaterFactory::createInstance($item->name);
            $updater->update($item);
        }
    }
}
