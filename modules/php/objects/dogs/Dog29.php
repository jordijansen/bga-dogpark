<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;

class Dog29 extends DogCard {

    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Spaniel (Field)');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_BALL => 2, RESOURCE_TREAT => 1];
        $this->goFetchResource = RESOURCE_STICK;
        $this->goFetchBonusResource = RESOURCE_BALL;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

