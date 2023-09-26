<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Crafty;

class Dog49 extends DogCard {

    use Crafty;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Griffon Fauve De Bretagne');
        $this->breeds = [BREED_HOUND];
        $this->costs = [RESOURCE_BALL => 1];
        $this->craftyResource = RESOURCE_STICK;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

