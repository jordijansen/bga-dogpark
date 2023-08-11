<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Obedient;

class Dog61 extends DogCard {

    use Obedient;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Otterhound');
        $this->breeds = [BREED_HOUND];
        $this->costs = [RESOURCE_BALL => 1, RESOURCE_TREAT => 2];
        $this->obedientResource = RESOURCE_BALL;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

