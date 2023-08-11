<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Obedient;

class Dog152 extends DogCard {

    use Obedient;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Tibetan Mastiff');
        $this->breeds = [BREED_WORKING];
        $this->costs = [RESOURCE_STICK => 2, RESOURCE_TOY => 1];
        $this->obedientResource = RESOURCE_TREAT;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

