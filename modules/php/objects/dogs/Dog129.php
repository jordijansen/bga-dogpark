<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;

class Dog129 extends DogCard {

    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Poodle (Toy)');
        $this->breeds = [BREED_UTILITY];
        $this->costs = [RESOURCE_STICK => 2, RESOURCE_TREAT => 1];
        $this->goFetchResource = RESOURCE_TOY;
        $this->goFetchBonusResource = RESOURCE_TREAT;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

