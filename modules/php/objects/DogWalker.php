<?php
namespace objects;
class DogWalker extends Card {
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
    }

    public static function fromArray($dbCards): array
    {
        return array_map(fn($dbCard) => DogWalker::from($dbCard), array_values($dbCards));
    }

    public static function from($dbCard): DogWalker
    {
        return new DogWalker($dbCard);
    }
}
