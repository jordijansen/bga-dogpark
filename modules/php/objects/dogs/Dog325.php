<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\ShowOff;
use objects\dogtraits\TreatLover;

class Dog325 extends DogCard {
    use ShowOff;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Dalmatian');
        $this->breeds = [BREED_UTILITY];
        $this->costs = [RESOURCE_BALL => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

