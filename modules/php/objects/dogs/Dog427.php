<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Eager;
use objects\dogtraits\Friendly;

class Dog427 extends DogCard {
    use Friendly;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Danish Broholmer');
        $this->breeds = [BREED_WORKING];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TOY => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

