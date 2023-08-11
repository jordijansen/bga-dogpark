<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;

class Dog28 extends DogCard {

    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Retriever (Flat Coated)');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_STICK => 2, RESOURCE_TREAT => 1];
        $this->goFetchResource = RESOURCE_BALL;
        $this->goFetchBonusResource = RESOURCE_STICK;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

