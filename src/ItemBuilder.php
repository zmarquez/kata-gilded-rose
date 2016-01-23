<?php
namespace GildedRose;

class ItemBuilder
{
    private $name;
    private $sellIn;
    private $quality;

    private function __construct()
    {
        $this->name = "Default";
        $this->sellIn = 10;
        $this->quality = 10;
    }

    /**
     * @return ItemBuilder
     */
    public static function getInstance() {
        return new ItemBuilder();
    }

    public function withName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function withSellIn($sellIn)
    {
        $this->sellIn = $sellIn;

        return $this;
    }

    public function withQuality($quality)
    {
        $this->quality= $quality;

        return $this;
    }

    /**
     * @return Item
     */
    public function build()
    {
        return new Item(
            $this->name,
            $this->sellIn,
            $this->quality
        );
    }
}
