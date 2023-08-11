<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;

class Dog1 extends DogCard {

    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Australian Silky Terrier');
        $this->breeds = [BREED_TOY];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TREAT => 1];
        $this->goFetchResource = RESOURCE_TREAT;
        $this->goFetchBonusResource = RESOURCE_STICK;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

