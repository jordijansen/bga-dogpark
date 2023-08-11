<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Eager;

class Dog4 extends DogCard {

    use Eager;

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Havanese');
        $this->breeds = [BREED_TOY];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_TREAT => 1];
        $this->eagerResource = RESOURCE_TOY;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

