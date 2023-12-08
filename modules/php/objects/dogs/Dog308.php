<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Hoarder;
use objects\dogtraits\TreatLover;

class Dog308 extends DogCard {
    use Hoarder;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Italian Spinone');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TREAT => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

