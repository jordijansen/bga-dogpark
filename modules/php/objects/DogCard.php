<?php
namespace objects;
use DogPark;

include("dogtraits/Eager.php");
include("dogtraits/Crafty.php");

include("dogtraits/GoFetch.php");
include("dogtraits/Obedient.php");


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
    public string $ability;
    public string $abilityTitle;
    public string $abilityText;

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);

        $this->name = clienttranslate('');
        $this->breeds = [];
        $this->costs = [];
        $this->ability = '';
        $this->abilityTitle = '';
        $this->abilityText = '';

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

    protected function getAbilityTitle() : string
    {
        return '';
    }

    protected function getAbilityText() : string
    {
        return '';
    }
    protected function getAbility() : string
    {
        return '';
    }

    public function isAbilityOptional() : bool
    {
        return false;
    }
}
