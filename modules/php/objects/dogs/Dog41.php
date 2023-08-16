<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Sociable;

class Dog41 extends DogCard {

    use Sociable;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Braque D’Auvergne');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_STICK => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

