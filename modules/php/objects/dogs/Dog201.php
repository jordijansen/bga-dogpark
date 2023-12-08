<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Crafty;
use objects\dogtraits\TreatLover;

class Dog201 extends DogCard {
    use Crafty;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Brittany');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_TOY => 1];
        $this->craftyResource = RESOURCE_TREAT;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

