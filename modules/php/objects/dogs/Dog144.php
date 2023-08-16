<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\BallHog;

class Dog144 extends DogCard {

    use BallHog;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Doberman');
        $this->breeds = [BREED_WORKING];
        $this->costs = [RESOURCE_BALL => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

