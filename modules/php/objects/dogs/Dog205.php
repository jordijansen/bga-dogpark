<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\TreatLover;
use objects\dogtraits\WellTrained;

class Dog205 extends DogCard {
    use WellTrained;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Small Munsterlander');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_TOY => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

