<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Eager;

class Dog401 extends DogCard {
    use Eager;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Australian Kelpie');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_BALL => 1, RESOURCE_TOY => 1];
        $this->eagerResource = RESOURCE_TREAT;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

