<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\SocialButterfly;

class Dog160 extends DogCard {

    use SocialButterfly;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Boxer');
        $this->breeds = [BREED_WORKING];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_TREAT => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

