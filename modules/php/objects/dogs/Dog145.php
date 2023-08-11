<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Crafty;

class Dog145 extends DogCard {

    use Crafty;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Giant Schnauzer');
        $this->breeds = [BREED_WORKING];
        $this->costs = [RESOURCE_STICK => 1];
        $this->craftyResource = RESOURCE_BALL;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

