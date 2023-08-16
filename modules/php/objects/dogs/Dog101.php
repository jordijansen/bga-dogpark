<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\StickChaser;

class Dog101 extends DogCard {

    use StickChaser;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Briard');
        $this->breeds = [BREED_UTILITY];
        $this->costs = [RESOURCE_STICK => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

