<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\StickChaser;

class Dog120 extends DogCard {

    use StickChaser;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('West Highland White Terrier');
        $this->breeds = [BREED_TERRIER];
        $this->costs = [RESOURCE_STICK => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

