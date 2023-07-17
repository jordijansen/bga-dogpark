<?php
namespace objects;
use DogPark;

class DogCard extends Card {
    public function __construct($dbCard, $DOG_CARDS)
    {
        parent::__construct($dbCard);
    }

    public static function fromArray($dbCards): array
    {
        return array_map(fn($dbCard) => DogCard::from($dbCard), array_values($dbCards));
    }

    public static function from($dbCard): DogCard
    {
        return new DogCard($dbCard, DogPark::$instance->DOG_CARDS);
    }
}
