<?php
namespace objects;

use DogPark;

class ForecastCard extends Card {

    public ?string $description;

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->description = DogPark::$instance->FORECAST_CARDS[$this->type][$this->typeArg]['description'];
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
