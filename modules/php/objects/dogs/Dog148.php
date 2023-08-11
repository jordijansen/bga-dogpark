<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;

class Dog148 extends DogCard {

    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Canadian Inuit Dog (Qimmiq)');
        $this->breeds = [BREED_WORKING];
        $this->costs = [RESOURCE_BALL => 2, RESOURCE_TREAT => 1];
        $this->goFetchResource = RESOURCE_TOY;
        $this->goFetchBonusResource = RESOURCE_TREAT;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

