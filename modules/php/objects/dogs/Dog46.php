<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\TreatLover;

class Dog46 extends DogCard {

    use TreatLover;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Retriever (Labrador)');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_TREAT => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

