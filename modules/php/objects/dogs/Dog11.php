<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\RaringToGo;

class Dog11 extends DogCard {

    use RaringToGo;

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Pekingese');
        $this->breeds = [BREED_TOY];
        $this->costs = [RESOURCE_BALL => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

