<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\SocialButterfly;

class Dog139 extends DogCard {

    use SocialButterfly;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Eurasier');
        $this->breeds = [BREED_UTILITY];
        $this->costs = [RESOURCE_BALL => 1, RESOURCE_TREAT => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

