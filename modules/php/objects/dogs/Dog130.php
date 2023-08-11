<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Obedient;

class Dog130 extends DogCard {

    use Obedient;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Korean Jindo');
        $this->breeds = [BREED_UTILITY];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_TREAT => 2];
        $this->obedientResource = RESOURCE_BALL;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

