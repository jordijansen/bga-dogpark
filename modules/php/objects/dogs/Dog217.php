<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;
use objects\dogtraits\TreatLover;

class Dog217 extends DogCard {
    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Glen of Imaal Terrier');
        $this->breeds = [BREED_TERRIER];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TREAT => 1];
        $this->goFetchResource = RESOURCE_TREAT;
        $this->goFetchBonusResource = RESOURCE_BALL;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

