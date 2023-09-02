<?php
namespace objects;

use DogPark;

class ObjectiveCard extends Card {

    public ?string $name;
    public ?string $description;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);

        $this->name = DogPark::$instance->OBJECTIVE_CARDS[$this->typeArg]['name'];
        $this->description = DogPark::$instance->OBJECTIVE_CARDS[$this->typeArg]['description'];
    }

    /**
     * @param $dbCards
     * @return ObjectiveCard[]
     */
    public static function fromArray($dbCards, $hideInfo = false): array
    {
        return array_map(fn($dbCard) => ObjectiveCard::from($dbCard, $hideInfo), array_values($dbCards));
    }

    public static function from($dbCard, $hideInfo = false): ObjectiveCard
    {
        $objectiveCard = new ObjectiveCard($dbCard);
        if ($hideInfo) {
            $objectiveCard->type = '';
            $objectiveCard->typeArg = null;
            $objectiveCard->name = null;
            $objectiveCard->description = null;
        }
        return $objectiveCard;
    }
}
