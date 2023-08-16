<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\RaringToGo;

class Dog95 extends DogCard {

    use RaringToGo;

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Border Collie');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_BALL => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

