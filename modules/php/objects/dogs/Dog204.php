<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\TreatLover;
use objects\dogtraits\WellTrained;

class Dog204 extends DogCard {
    use WellTrained;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Retriever (Curly Coated)');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_BALL => 1, RESOURCE_TREAT => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

