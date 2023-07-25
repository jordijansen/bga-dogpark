<?php
namespace objects;
use DogPark;

for($i = 1; $i<=1;$i++)
{
    include("dogs/Dog{$i}.php");
}

class DogCard extends Card {

    public string $name;
    /**
     * @var string[]
     */
    public array $breeds;
    public array $costs;

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
    }

    /**
     * @param $dbCards
     * @return DogCard[]
     */
    public static function fromArray($dbCards): array
    {
        return array_map(fn($dbCard) => DogCard::from($dbCard), array_values($dbCards));
    }

    public static function from($dbCard): DogCard
    {
//        $class = "objects\dogs\Dog" .$dbCard['card_type_arg'];
        $class = "objects\dogs\Dog1";
        return new $class($dbCard);
    }
}
