<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Hoarder;
use objects\dogtraits\TreatLover;

class Dog324 extends DogCard {
    use Hoarder;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Schipperke');
        $this->breeds = [BREED_UTILITY];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TOY => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

