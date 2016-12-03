<?php
namespace GildedRose;

class Inn
{
    const MINIMUM_QUALITY = 0;
    const MAXIMUM_QUALITY = 50;
    const QUALITY_GRANULARITY = 1;
    const SELLIN_GRANULARITY = 1;
    const MINIMUM_SELLIN = 0;
    const LIMIT_SELLIN_TWICE_FAST_QUALITY = 10;
    const LIMIT_SELLIN_THRICE_FAST_QUALITY = 5;

    public function updateQuality($items)
    {
        foreach ($items as $item) {
            if ($this->isAgedBrie($item)) {
                $this->increaseQuality($item);
                $this->decreaseSellin($item);
                if ($this->isExpired($item)) {
                    $this->increaseQuality($item);
                }
            }
            if ($this->isBackstagePass($item)) {
                $this->increaseQuality($item);
                $this->increaseExtraQuality($item);
                $this->decreaseSellin($item);
                if ($this->isExpired($item)) {
                    $this->resetQuality($item);
                }
            }
            if (!$this->isAgedBrie($item) && !$this->isBackstagePass($item)) {
                $this->decreaseQuality($item);
                if (!$this->isSulfuras($item)) {
                    $this->decreaseSellin($item);
                }
                if ($this->isExpired($item)) {
                    if (!$this->isSulfuras($item)) {
                        $this->decreaseQuality($item);
                    }
                }
            }
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
        if ($this->isSulfuras($item)) {
            return;
        }
        $item->quality = $item->quality - self::QUALITY_GRANULARITY;

    }

    /**
     * @param Item $item
     */
    private function increaseQuality(Item $item)
    {
        if ($item->quality >= self::MAXIMUM_QUALITY) {
            return;
        }
        $item->quality = $item->quality + self::QUALITY_GRANULARITY;

    }

    /**
     * @param Item $item
     *
     * @return bool
     */
    private function isAgedBrie(Item $item)
    {
        return $item->name == ItemType::AGED_BRIE;
    }

    /**
     * @param Item $item
     *
     * @return bool
     */
    private function isBackstagePass(Item $item)
    {
        return $item->name == ItemType::BACKSTAGE_PASSES;
    }

    /**
     * @param Item $item
     *
     * @return bool
     */
    private function isSulfuras(Item $item)
    {
        return $item->name == ItemType::SULFURAS;
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
    private function decreaseSellin($item)
    {
        $item->sellIn = $item->sellIn - self::SELLIN_GRANULARITY;
    }

    /**
     * @param $item
     */
    private function resetQuality($item)
    {
        $item->quality = self::MINIMUM_QUALITY;
    }

    /**
     * @param $item
     *
     * @return bool
     */
    private function isExpired($item)
    {
        return $item->sellIn < self::MINIMUM_SELLIN;
    }
}
