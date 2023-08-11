<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;

class Dog108 extends DogCard {

    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Soft-Coated Wheaten Terrier');
        $this->breeds = [BREED_TERRIER];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TOY => 1];
        $this->goFetchResource = RESOURCE_BALL;
        $this->goFetchBonusResource = RESOURCE_TOY;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

