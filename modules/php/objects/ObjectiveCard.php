<?php
namespace objects;

class ObjectiveCard extends Card {

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
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
        }
        return $objectiveCard;
    }
}
