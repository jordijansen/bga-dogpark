<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Eager;
use objects\dogtraits\Globetrotter;

class Dog404 extends DogCard {
    use Globetrotter;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Czechoslovakian Wolfdog');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_STICK => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

