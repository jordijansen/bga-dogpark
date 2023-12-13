<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Eager;
use objects\dogtraits\Friendly;

class Dog420 extends DogCard {
    use Friendly;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Kromfohrländer');
        $this->breeds = [BREED_TOY];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TREAT => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

