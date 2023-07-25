<?php
namespace objects;
use DogPark;

for($i = 1; $i<=18;$i++)
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
        $this->name = DogPark::$instance->DOG_CARDS[$this->type][intval($this->typeArg)]['name'];
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
        $cardTypeArg = array_key_exists('card_type_arg', $dbCard) || array_key_exists('type_arg', $dbCard) ? intval($dbCard['card_type_arg'] ?? $dbCard['type_arg']) : null;
        $class = "objects\dogs\Dog" .$cardTypeArg;
        return new $class($dbCard);
    }
}
