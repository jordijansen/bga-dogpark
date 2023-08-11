<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Obedient;

class Dog32 extends DogCard {
    use Obedient;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Spaniel (Sussex)');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_TOY => 2];
        $this->obedientResource = RESOURCE_BALL;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

