<?php
namespace objects\dogs;

use objects\DogCard;

class Dog47 extends DogCard {

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('');
        $this->breeds = [];
        $this->costs = [];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

