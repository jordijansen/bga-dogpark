<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;

class Dog24 extends DogCard {

    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Lagotto Romagnolo');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_BALL => 2, RESOURCE_TOY => 1];
        $this->goFetchResource = RESOURCE_TREAT;
        $this->goFetchBonusResource = RESOURCE_TOY;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

