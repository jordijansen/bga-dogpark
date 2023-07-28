<?php
namespace objects;

class BreedExpertCard extends Card {

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
    }

    /**
     * @param $dbCards
     * @return BreedExpertCard[]
     */
    public static function fromArray($dbCards): array
    {
        return array_map(fn($dbCard) => BreedExpertCard::from($dbCard), array_values($dbCards));
    }

    public static function from($dbCard): BreedExpertCard
    {
        return new BreedExpertCard($dbCard);
    }
}
