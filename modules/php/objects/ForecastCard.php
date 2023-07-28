<?php
namespace objects;

class ForecastCard extends Card {

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
    }

    /**
     * @param $dbCards
     * @return ForecastCard[]
     */
    public static function fromArray($dbCards): array
    {
        return array_map(fn($dbCard) => ForecastCard::from($dbCard), array_values($dbCards));
    }

    public static function from($dbCard): ForecastCard
    {
        return new ForecastCard($dbCard);
    }
}
