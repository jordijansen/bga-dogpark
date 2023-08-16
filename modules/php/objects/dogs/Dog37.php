<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\RaringToGo;

class Dog37 extends DogCard {

    use RaringToGo;

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('German Wirehaired Pointer');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_BALL => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

