<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\SearchAndRescue;

class Dog157 extends DogCard {

    use SearchAndRescue;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Newfoundland');
        $this->breeds = [BREED_WORKING];
        $this->costs = [RESOURCE_STICK => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

