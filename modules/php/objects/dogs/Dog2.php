<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\BallHog;

class Dog2 extends DogCard {

    use BallHog;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Lowchen (Little Lion Dog)');
        $this->breeds = [BREED_TOY];
        $this->costs = [RESOURCE_BALL => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

