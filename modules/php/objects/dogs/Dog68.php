<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Sociable;

class Dog68 extends DogCard {

    use Sociable;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Whippet');
        $this->breeds = [BREED_HOUND];
        $this->costs = [RESOURCE_BALL => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

