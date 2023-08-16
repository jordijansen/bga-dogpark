<?php
namespace objects;

class BreedExpertCard extends Card {

    public $reputation;

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->reputation = $this->determineReputation($this->locationArg);
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

    private function determineReputation(int $locationArg)
    {
        switch ($locationArg) {
            case 1:
                return 8;
            case 2:
                return 7;
            case 3:
                return 6;
            case 4:
                return 4;
            case 5:
                return 3;
            case 6:
            case 7:
            default:
                return 2;
        }
    }

}
