<?php
namespace objects;

class LocationBonus {

    public int $id;
    public int $locationId;
    public String $bonus;

    public function __construct($dbCard)
    {
        $this->id = intval($dbCard['id']);
        $this->locationId = intval($dbCard['location_id']);
        $this->bonus = $dbCard['bonus'];
    }

    /**
     * @param $dbCards
     * @return LocationBonus[]
     */
    public static function fromArray($dbCards): array
    {
        return array_map(fn($dbCard) => LocationBonus::from($dbCard), array_values($dbCards));
    }

    public static function from($dbCard): LocationBonus
    {
        return new LocationBonus($dbCard);
    }
}
