<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Hoarder;
use objects\dogtraits\TreatLover;

class Dog320 extends DogCard {
    use Hoarder;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Chihuahua (Smooth Coat)');
        $this->breeds = [BREED_TOY];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TOY => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

