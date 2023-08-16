<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\ToyCollector;

class Dog141 extends DogCard {

    use ToyCollector;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Tibetan Terrier');
        $this->breeds = [BREED_UTILITY];
        $this->costs = [RESOURCE_TOY => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

