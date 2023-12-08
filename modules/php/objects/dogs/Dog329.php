<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\ShowOff;
use objects\dogtraits\TreatLover;

class Dog329 extends DogCard {
    use ShowOff;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Neapolitan Mastiff');
        $this->breeds = [BREED_WORKING];
        $this->costs = [RESOURCE_STICK => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

