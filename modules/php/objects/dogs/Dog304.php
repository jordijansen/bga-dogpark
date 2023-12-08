<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\ShowOff;
use objects\dogtraits\TreatLover;

class Dog304 extends DogCard {
    use ShowOff;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Collie (Rough)');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_STICK => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

