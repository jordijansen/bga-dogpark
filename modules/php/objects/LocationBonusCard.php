<?php
namespace objects;

use DogPark;

class LocationBonusCard extends Card {

    public $bonusesOnLocation = [];

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->bonusesOnLocation = DogPark::$instance->LOCATION_BONUS_CARDS[$this->type][intval($this->typeArg)];
    }

    /**
     * @param $dbCards
     * @return LocationBonusCard[]
     */
    public static function fromArray($dbCards): array
    {
        return array_map(fn($dbCard) => LocationBonusCard::from($dbCard), array_values($dbCards));
    }

    public static function from($dbCard): LocationBonusCard
    {
        return new LocationBonusCard($dbCard);
    }
}
