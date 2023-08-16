<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\ToyCollector;

class Dog18 extends DogCard {

    use ToyCollector;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Japanese Chin');
        $this->breeds = [BREED_TOY];
        $this->costs = [RESOURCE_TOY => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

