<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Sociable;

class Dog98 extends DogCard {

    use Sociable;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Swedish Vallhund');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_STICK => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

