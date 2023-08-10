<?php
namespace objects;
use DogPark;

include("dogtraits/Eager.php");

for($i = 1; $i<=163;$i++)
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

    public array $resourcesOnCard;

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $dogInfo = DogPark::$instance->DOG_CARDS[intval($this->typeArg)];

        $this->name = $dogInfo['name'];
        $this->breeds = $dogInfo['breeds'];
        $this->costs = $dogInfo['costs'];

        $this->resourcesOnCard = DogPark::$instance->dogManager->getDogResources($this->id);
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
        if (class_exists($class)) {
            return new $class($dbCard);
        }
        return new DogCard($dbCard);
    }
}
