<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;

class Dog55 extends DogCard {

    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Cirneco Dell’ Etna');
        $this->breeds = [BREED_HOUND];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TOY => 1];
        $this->goFetchResource = RESOURCE_STICK;
        $this->goFetchBonusResource = RESOURCE_TOY;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

