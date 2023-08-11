<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;

class Dog125 extends DogCard {

    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Boston Terrier');
        $this->breeds = [BREED_UTILITY];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TREAT => 1];
        $this->goFetchResource = RESOURCE_STICK;
        $this->goFetchBonusResource = RESOURCE_TREAT;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }

}

