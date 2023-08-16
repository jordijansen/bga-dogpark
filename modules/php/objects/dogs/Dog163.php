<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\TreatLover;

class Dog163 extends DogCard {

    use TreatLover;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Rottweiler');
        $this->breeds = [BREED_WORKING];
        $this->costs = [RESOURCE_TREAT => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

