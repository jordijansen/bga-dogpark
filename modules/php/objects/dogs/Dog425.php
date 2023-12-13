<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Eager;
use objects\dogtraits\Fusspot;

class Dog425 extends DogCard {
    use Fusspot;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Peruvian Hairless Dog');
        $this->breeds = [BREED_UTILITY];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_TREAT => 1];
        $this->fusspotResource = RESOURCE_TREAT;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

