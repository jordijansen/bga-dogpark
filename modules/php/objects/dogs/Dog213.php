<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\LoneWolf;
use objects\dogtraits\TreatLover;

class Dog213 extends DogCard {
    use LoneWolf;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Pyrenean Sheepdog (Long Haired)');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

