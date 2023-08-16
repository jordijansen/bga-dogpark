<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\PackDog;

class Dog92 extends DogCard {

    use PackDog;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Australian Cattle Dog');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_TREAT => 1, RESOURCE_TOY => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

