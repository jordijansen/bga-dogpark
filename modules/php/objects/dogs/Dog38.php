<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\BallHog;

class Dog38 extends DogCard {

    use BallHog;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Pointer');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_BALL => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

