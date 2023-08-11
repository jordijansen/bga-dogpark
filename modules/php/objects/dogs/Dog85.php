<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;

class Dog85 extends DogCard {

    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Collie (Smooth)');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_STICK => 2, RESOURCE_TOY => 1];
        $this->goFetchResource = RESOURCE_TREAT;
        $this->goFetchBonusResource = RESOURCE_TOY;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

