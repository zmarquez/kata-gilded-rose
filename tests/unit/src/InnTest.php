<?php
namespace Tests;

use GildedRose\Inn;
use GildedRose\Item;
use GildedRose\ItemBuilder;

class InnTest extends \PHPUnit_Framework_TestCase
{
    const MINIMUM_SELLIN = 0;
    const SELLIN_GRANULARITY = 1;
    const MINIMUM_QUALITY = 0;
    const QUALITY_GRANULARITY = 1;
    const MAXIMUM_QUALITY = 50;
    const SULFURAS_QUALITY = 80;
    const LIMIT_SELLIN_TWICE_FAST_QUALITY = 10;
    const LIMIT_SELLIN_THRICE_FAST_QUALITY = 5;

    /** @var  Inn */
    private $inn;

    protected function setUp()
    {
        $this->inn = new Inn();
    }

    /**
     * @test
     */
    public function each_day_the_sellin_value_and_quality_value_degrade()
    {
        $item = $this->given_a_normal_item();
        $item->setSellIn(self::MINIMUM_SELLIN + self::SELLIN_GRANULARITY);
        $item->setQuality(self::MINIMUM_QUALITY + self::QUALITY_GRANULARITY);

        $this->inn->updateQuality([$item]);

        $this->assertEquals(self::MINIMUM_QUALITY, $item->getQuality(), 'Quality must be degrade every day');
        $this->assertEquals(self::MINIMUM_SELLIN, $item->getSellIn(), 'Sellin must be degrade every day');
    }

    /**
     * @test
     */
    public function once_the_sell_by_date_has_passed_quality_degrades_twice_as_fast()
    {
        $item = $this->given_a_normal_item();
        $item->setQuality(self::MINIMUM_QUALITY + $this->getQualityGranularityWhenIsExpired());
        $this->expire($item);

        $this->inn->updateQuality([$item]);

        $this->assertEquals(
            self::MINIMUM_QUALITY,
            $item->getQuality(),
            'Quality degrade twice as fast when sell by date is passed'
        );
    }

    /**
     * @test
     */
    public function the_quality_of_any_item_is_never_less_than_minimum()
    {
        $item = $this->given_a_normal_item();
        $item->setSellIn(self::MINIMUM_SELLIN + self::SELLIN_GRANULARITY);
        $item->setQuality(self::MINIMUM_QUALITY);

        $this->inn->updateQuality([$item]);

        $this->assertEquals(self::MINIMUM_QUALITY, $item->getQuality(), 'Quality can\'t be less than minimum');
    }

    /**
     * @test
     */
    public function the_quality_of_an_item_is_never_more_then_maximum()
    {
        $item = $this->given_aged_brie_item();
        $item->setSellIn(self::MINIMUM_SELLIN + self::SELLIN_GRANULARITY);
        $item->setQuality(self::MAXIMUM_QUALITY);

        $this->inn->updateQuality([$item]);

        $this->assertEquals(self::MAXIMUM_QUALITY, $item->getQuality(), 'Quality can\'t be more than maximum');
    }

    /**
     * @test
     */
    public function aged_brie_increase_in_quality_the_older_it_gets()
    {
        $item = $this->given_aged_brie_item();
        $item->setSellIn(self::MINIMUM_SELLIN + self::SELLIN_GRANULARITY);
        $item->setQuality(self::MINIMUM_QUALITY);

        $this->inn->updateQuality([$item]);

        $this->assertEquals(
            self::MINIMUM_QUALITY + self::QUALITY_GRANULARITY,
            $item->getQuality(),
            'Aged Brie must increase in quality the older it gets'
        );
    }

    /**
     * @test
     */
    public function sulfuras_is_legendary_item_never_has_to_be_sold_or_decreases_in_quality()
    {
        $item = $this->given_a_sulfuras_item();
        $item->setSellIn(self::MINIMUM_SELLIN);

        $this->inn->updateQuality([$item]);

        $this->assertEquals(self::SULFURAS_QUALITY, $item->getQuality(), 'Sulfuras never decrease in quality');
        $this->assertEquals(self::MINIMUM_SELLIN, $item->getSellIn(), 'Sulfuras can\'t be sold');
    }

    /**
     * @test
     */
    public function backstage_passes_increase_in_quality_the_older_it_gets()
    {
        $item = $this->given_a_backstage_passes_item();
        $item->setQuality(self::MINIMUM_QUALITY);
        $item->setSellIn(self::MINIMUM_SELLIN + self::LIMIT_SELLIN_TWICE_FAST_QUALITY * self::SELLIN_GRANULARITY + 1);

        $this->inn->updateQuality([$item]);

        $this->assertEquals(
            self::MINIMUM_QUALITY + self::QUALITY_GRANULARITY,
            $item->getQuality(),
            'Backstage Pass quality must increase the older it gets'
        );
    }

    /**
     * @test
     */
    public function backstage_passes_increase_twice_as_fast_in_quality_when_there_are_ten_days_or_less()
    {
        $item = $this->given_a_backstage_passes_item();
        $item->setQuality(self::MINIMUM_QUALITY);
        $item->setSellIn(self::LIMIT_SELLIN_TWICE_FAST_QUALITY);

        $this->inn->updateQuality([$item]);

        $this->assertEquals(
            self::MINIMUM_QUALITY + 2 * self::QUALITY_GRANULARITY,
            $item->getQuality(),
            'Backstage Pass quality must increase twice as fast when there are ten days or less to concert'
        );
    }

    /**
     * @test
     */
    public function backstage_passes_increase_thrice_as_fast_in_quality_when_there_are_five_days_or_less()
    {
        $item = $this->given_a_backstage_passes_item();
        $item->setQuality(self::MINIMUM_QUALITY);
        $item->setSellIn(self::LIMIT_SELLIN_THRICE_FAST_QUALITY);

        $this->inn->updateQuality([$item]);

        $this->assertEquals(
            self::MINIMUM_QUALITY + 3 * self::QUALITY_GRANULARITY,
            $item->getQuality(),
            'Backstage Pass quality must increase thrice as fast when there are five days or less to concert'
        );
    }

    /**
     * @test
     */
    public function backstage_passes_goes_to_minimum_quality_after_the_concert()
    {
        $item = $this->given_a_backstage_passes_item();
        $this->expire($item);

        $this->inn->updateQuality([$item]);

        $this->assertEquals(
            self::MINIMUM_QUALITY,
            $item->getQuality(),
            'Backstage Pass quality must go to minimum after the concert'
        );
    }

    /**
     * @return Item
     */
    private function given_a_normal_item()
    {
        return ItemBuilder::getInstance()->build();
    }

    /**
     * @return Item
     */
    private function given_a_backstage_passes_item()
    {
        return ItemBuilder::getInstance()->withName('Backstage passes')->build();
    }

    /**
     * @return Item
     */
    private function given_aged_brie_item()
    {
        return ItemBuilder::getInstance()->withName('Aged Brie')->build();
    }

    /**
     * @return Item
     */
    private function given_a_sulfuras_item()
    {
        return ItemBuilder::getInstance()->withName('Sulfuras')->withQuality(self::SULFURAS_QUALITY)->build();
    }

    /**
     * @param $item
     */
    private function expire(Item $item)
    {
        $item->setSellIn(self::MINIMUM_SELLIN);
    }

    /**
     * @return int
     */
    private function getQualityGranularityWhenIsExpired()
    {
        return 2 * self::QUALITY_GRANULARITY;
    }
}

