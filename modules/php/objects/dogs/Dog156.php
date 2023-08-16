<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\RaringToGo;

class Dog156 extends DogCard {

    use RaringToGo;

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Bernese Mountain Dog');
        $this->breeds = [BREED_WORKING];
        $this->costs = [RESOURCE_BALL => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

