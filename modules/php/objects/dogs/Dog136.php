<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\SearchAndRescue;

class Dog136 extends DogCard {

    use SearchAndRescue;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Canaan Dog');
        $this->breeds = [BREED_UTILITY];
        $this->costs = [RESOURCE_BALL => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

