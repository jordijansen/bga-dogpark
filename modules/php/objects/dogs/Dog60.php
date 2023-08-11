<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Obedient;

class Dog60 extends DogCard {

    use Obedient;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Dachshund (Wire Haired)');
        $this->breeds = [BREED_HOUND];
        $this->costs = [RESOURCE_BALL => 2, RESOURCE_TREAT => 1];
        $this->obedientResource = RESOURCE_TREAT;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

