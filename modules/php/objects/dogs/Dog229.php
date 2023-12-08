<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\TreatLover;
use objects\dogtraits\WellTrained;

class Dog229 extends DogCard {
    use WellTrained;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Mastiff');
        $this->breeds = [BREED_WORKING];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_TREAT => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

